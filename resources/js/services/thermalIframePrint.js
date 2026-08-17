/**
 * Isolated iframe thermal printing (browser popup path).
 * Avoids POS page CSS collisions that cause blank pages, huge scale, and broken fonts.
 *
 * Respects the Print Preview toggle: with Preview OFF we never open the Chrome
 * dialog unless the window was launched in silent (kiosk-printing) mode.
 * Callers that are a direct response to a user click may pass { force: true }.
 */
import {canDirectPrint, isPrintPreviewOn, syncSilentPrintFromUrl} from './printPreference.js';
import {PrintUnavailableError} from './printService.js';
import {useFrontendSettingStore} from '../stores/frontendSetting.js';

const THERMAL_CSS = `
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body {
    width: 80mm;
    margin: 0;
    padding: 0;
    background: #fff;
    color: #000;
    font-family: "Helvetica Neue", Arial, "DejaVu Sans", sans-serif;
    font-size: 12px;
    line-height: 1.3;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  @page { size: 80mm auto; margin: 0; }
  .sheet {
    width: 72mm;
    max-width: 72mm;
    margin: 0 auto;
    padding: 2mm 2mm 5mm;
  }
  .c { text-align: center; }
  .l { text-align: left; }
  .r { text-align: right; }
  .b { font-weight: 700; }

  /* Header */
  .shop { font-size: 14px; font-weight: 600; }
  .doc-title { font-size: 18px; font-weight: 700; letter-spacing: .3px; margin-bottom: 1px; }
  .kot-title { font-size: 22px; font-weight: 700; letter-spacing: 4px; }
  .kot-shop { font-size: 14px; font-weight: 700; margin-top: -1px; }

  .rule { border-top: 1px solid #000; margin: 4px 0; }
  .rule-dash { border-top: 1px dashed #000; margin: 4px 0; }

  /* Name line + two-up meta rows (Date | Bill#, Time | Table) */
  .name { font-size: 12.5px; }
  table.meta { width: 100%; border-collapse: collapse; table-layout: fixed; }
  table.meta td { font-size: 12px; padding: 1px 0; vertical-align: top; }
  table.meta td.r { text-align: right; }

  /* Item grid — rules above and below the header row only */
  table.grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
  table.grid th, table.grid td {
    font-size: 12px;
    padding: 2px 0;
    vertical-align: top;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
  table.grid th { font-weight: 700; }
  table.grid thead th {
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    padding: 3px 0;
  }
  table.grid tbody tr:first-child td { padding-top: 4px; }
  table.grid td.qty, table.grid td.price, table.grid td.amt,
  table.grid th.qty, table.grid th.price, table.grid th.amt { text-align: right; }

  /* Totals */
  table.tot { width: 100%; border-collapse: collapse; }
  table.tot td { font-size: 12px; padding: 1.5px 0; }
  table.tot td.amt { text-align: right; }
  table.tot tr.grand td {
    font-size: 16px;
    font-weight: 700;
    padding: 3px 0 1px;
  }
  .mod { font-size: 10.5px; padding-left: 8px; }
  .note { margin-top: 4px; font-weight: 700; font-size: 11.5px; white-space: pre-wrap; }
  .thanks { margin-top: 4px; text-align: center; font-size: 12.5px; font-weight: 700; line-height: 1.35; }
  .powered { margin-top: 4px; text-align: center; font-size: 11px; font-weight: 400; }
  .hint {
    position: fixed; top: 8px; left: 8px; right: 8px;
    background: #111; color: #fff; font-family: sans-serif;
    font-size: 13px; font-weight: 700; padding: 10px 12px; border-radius: 8px;
    z-index: 9;
  }
  @media print {
    .hint { display: none !important; }
    html, body { width: 80mm; }
  }
`;

function esc(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/** Pull a number out of "₹1,234.50" / "1234.5" / 1234.5 */
function numeric(value) {
    if (typeof value === 'number') return Number.isFinite(value) ? value : 0;
    if (value == null || value === '') return 0;
    const n = parseFloat(String(value).replace(/[^0-9.\-]/g, ''));
    return Number.isFinite(n) ? n : 0;
}

/**
 * Reuse the currency symbol/placement already produced by the API so the
 * derived rows (CGST / SGST / Round Off) look identical to the server ones.
 */
function currencyFormatter(sample) {
    const raw = String(sample ?? '');
    const match = raw.match(/^([^\d\-]*)[\d.,\-]+([^\d]*)$/);
    const prefix = match ? match[1] : '';
    const suffix = match ? match[2] : '';
    return (amount) => {
        const fixed = Math.abs(amount).toFixed(2);
        const sign = amount < 0 ? '-' : '';
        return `${sign}${prefix}${fixed}${suffix}`;
    };
}

/**
 * Server payloads carry powered_by; client-built tickets fall back to the
 * company name in settings so the footer never disappears.
 */
function poweredByHtml(explicit = '', prefix = '') {
    let name = String(explicit || '').trim();
    if (!name) {
        try {
            name = String(useFrontendSettingStore().lists?.company_name || '').trim();
        } catch (e) {
            name = '';
        }
    }

    return name ? `<div class="powered">${prefix}Powered by ${esc(name)}</div>` : '';
}

function waitAfterPrint(win, timeoutMs = 120000) {
    return new Promise((resolve) => {
        let settled = false;
        const finish = () => {
            if (settled) return;
            settled = true;
            try { win.removeEventListener('afterprint', onAfter); } catch (e) {}
            clearTimeout(timer);
            resolve();
        };
        const onAfter = () => finish();
        const timer = setTimeout(finish, timeoutMs);
        try { win.addEventListener('afterprint', onAfter); } catch (e) {}
    });
}

/**
 * With Preview OFF the browser dialog must never appear, otherwise the POS
 * operator sees a preview they explicitly disabled.
 */
function assertIframePrintAllowed(force = false) {
    if (force) return;
    syncSilentPrintFromUrl();
    if (isPrintPreviewOn() || canDirectPrint()) {
        return;
    }
    throw new PrintUnavailableError(
        'Print Preview is OFF and this printer is set to Browser Popup. '
        + 'Set the printer to Direct Print (Local Print Agent), open POS with Silent Print, or turn Print Preview ON.'
    );
}

function openPrintFrame(html) {
    const iframe = document.createElement('iframe');
    iframe.setAttribute('title', 'thermal-print');
    iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;opacity:0;pointer-events:none;';
    document.body.appendChild(iframe);

    const doc = iframe.contentDocument || iframe.contentWindow.document;
    doc.open();
    doc.write(html);
    doc.close();

    return iframe;
}

async function printHtmlDocument(html, options = {}) {
    assertIframePrintAllowed(options.force);

    const iframe = openPrintFrame(html);
    const win = iframe.contentWindow;
    await new Promise((r) => setTimeout(r, 250));

    try {
        win.focus();
        const wait = waitAfterPrint(win);
        win.print();
        await wait;
    } finally {
        setTimeout(() => {
            try { iframe.remove(); } catch (e) {}
        }, 400);
    }
}

export function buildKotHtml(payload = {}, printerHint = '') {
    const items = Array.isArray(payload.items) ? payload.items : [];
    const isMod = !!(payload.is_modification || payload.modification);

    const rows = items.map((item, idx) => {
        const mods = []
            .concat(item.variation_lines || [])
            .map((l) => `<div class="mod">- ${esc(l)}</div>`)
            .join('');
        const extras = (item.extra_lines || [])
            .map((l) => `<div class="mod">+ ${esc(l)}</div>`)
            .join('');
        const instr = item.instruction ? `<div class="mod">** ${esc(item.instruction)}</div>` : '';
        const qtyCell = item.change_label || item.direction
            ? esc(item.change_label || (`${item.direction}: ${item.quantity}`))
            : esc(item.quantity);
        return `
          <tr>
            <td style="width:82%">${idx + 1}. ${esc(item.name)}${mods}${extras}${instr}</td>
            <td class="qty" style="width:18%">${qtyCell}</td>
          </tr>`;
    }).join('');

    const table = payload.table_no || payload.table?.number || '';
    const orderType = (payload.order_type_label || 'POS').toString();
    const totalQty = payload.total_qty ?? items.reduce((s, i) => s + Number(i.quantity || 0), 0);
    const hint = printerHint
        ? `<div class="hint">KOT — select kitchen printer: ${esc(printerHint)}</div>`
        : `<div class="hint">KOT — select the KITCHEN / KOT printer</div>`;

    const leftLabel = table ? `Table: ${esc(table)}` : (payload.counter ? `${esc(payload.counter)} Counter` : 'Counter');
    const ticket = payload.ticket_no || (payload.kot_no ? `KOT - ${payload.kot_no}` : '');

    return `<!DOCTYPE html><html><head><meta charset="utf-8"><title>KOT</title><style>${THERMAL_CSS}</style></head><body>
      ${hint}
      <div class="sheet">
        <div class="c kot-title">${isMod ? 'ORDER CHANGE' : 'KOT'}</div>
        <div class="c kot-shop">${esc(payload.restaurant || '')}</div>
        <div class="rule"></div>
        <table class="meta">
          <tr><td>${leftLabel}</td><td class="r">${esc(orderType)}</td></tr>
          <tr><td>${esc(payload.order_date || '')} ${esc(payload.order_time || '')}</td><td class="r">${esc(ticket)}</td></tr>
        </table>
        <table class="grid">
          <thead><tr><th class="l">ITEM</th><th class="qty">${isMod ? 'CHANGE' : 'QTY'}</th></tr></thead>
          <tbody>${rows}</tbody>
        </table>
        <div class="rule"></div>
        <div class="r">Total: ${esc(totalQty)} items</div>
        ${payload.special_note || payload.order_note ? `<div class="note">NOTE: ${esc(payload.special_note || payload.order_note)}</div>` : ''}
        ${poweredByHtml(payload.powered_by, '— ')}
        <div class="rule-dash"></div>
      </div>
    </body></html>`;
}

export function buildBillHtml(order = {}, opts = {}) {
    const restaurant = opts.restaurant || {};
    const items = Array.isArray(opts.items) ? opts.items : [];
    const hintName = opts.printerHint || '';
    const hint = `<div class="hint">BILL — select the BILL / COUNTER printer${hintName ? `: ${esc(hintName)}` : ''}</div>`;

    const money = currencyFormatter(
        order.total_currency_price || order.subtotal_currency_price || order.total_tax_currency_price || ''
    );

    let totalQty = 0;
    const rows = items.map((item) => {
        const name = item.name || item.item_name || '';
        const qty = item.quantity ?? '';
        totalQty += Number(qty || 0);
        const price = item.unitPrice || item.price || item.price_currency_price || '';
        const amt = item.priceLabel || item.total_price || item.total_price_currency_price || '';
        return `<tr>
          <td style="width:40%">${esc(name)}</td>
          <td class="qty" style="width:12%">${esc(qty)}</td>
          <td class="price" style="width:24%">${esc(price)}</td>
          <td class="amt" style="width:24%">${esc(amt)}</td>
        </tr>`;
    }).join('');

    const customer = (() => {
        const name = (order?.user?.name || order?.customer_name || '').toString().trim();
        if (!name || /walking\s*customer/i.test(name) || /^guest$/i.test(name)) return 'Walk-in';
        return name;
    })();

    const orderType = opts.orderTypeLabel
        || (Number(order?.order_type) === 20 ? 'Dine In'
            : Number(order?.order_type) === 5 ? 'Delivery'
            : Number(order?.order_type) === 10 ? 'Take Away'
            : 'Take Away');

    // GST is stored as one aggregate figure; show it as the usual CGST + SGST halves.
    const taxTotal = numeric(order.total_tax);
    const halfTax = taxTotal / 2;
    const gstRate = numeric(
        opts.gstRate ?? order.gst_rate ?? items.find((i) => numeric(i.tax_rate) > 0)?.tax_rate
    );
    const halfRateLabel = gstRate > 0 ? ` (${(gstRate / 2).toFixed(2).replace(/\.?0+$/, '')}%)` : '';

    const subtotal = numeric(order.subtotal);
    const discount = numeric(order.discount);
    const extras = numeric(order.delivery_fee) + numeric(order.service_fee) + numeric(order.rider_tip);
    const total = numeric(order.total);
    const roundOff = total - (subtotal - discount + taxTotal + extras);

    const preGrandRows = [
        `<tr><td>Total Qty ${esc(totalQty)}</td><td class="amt">Sub Total. ${esc(order.subtotal_currency_price || money(subtotal))}</td></tr>`,
        discount > 0
            ? `<tr><td>Discount</td><td class="amt">- ${esc(order.discount_currency_price || money(discount))}</td></tr>`
            : '',
        taxTotal > 0
            ? `<tr><td>CGST${halfRateLabel}</td><td class="amt">${esc(money(halfTax))}</td></tr>`
              + `<tr><td>SGST${halfRateLabel}</td><td class="amt">${esc(money(halfTax))}</td></tr>`
            : '',
        numeric(order.delivery_fee) > 0
            ? `<tr><td>Delivery Fee</td><td class="amt">${esc(order.delivery_fee_currency_price || money(numeric(order.delivery_fee)))}</td></tr>`
            : '',
        numeric(order.service_fee) > 0
            ? `<tr><td>Service Fee</td><td class="amt">${esc(order.service_fee_currency_price || money(numeric(order.service_fee)))}</td></tr>`
            : '',
        Math.abs(roundOff) >= 0.01
            ? `<tr><td>Round Off</td><td class="amt">${esc(money(roundOff))}</td></tr>`
            : '',
    ].join('');

    const grandRows = [
        `<tr class="grand"><td>Grand Total</td><td class="amt">${esc(order.total_currency_price || money(total))}</td></tr>`,
        opts.paymentLabel
            ? `<tr><td>Payment</td><td class="amt">${esc(String(opts.paymentLabel).toUpperCase())}</td></tr>`
            : '',
    ].join('');

    const billNo = order.order_serial_no || order.id || '';
    // Second column of the Time row: table for dine-in, order type otherwise.
    const timeRowRight = opts.tableLabel ? `Table: ${esc(opts.tableLabel)}` : esc(orderType);

    return `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Tax Invoice</title><style>${THERMAL_CSS}</style></head><body>
      ${hint}
      <div class="sheet">
        <div class="c shop">${esc(restaurant.name || 'Restaurant')}</div>
        <div class="c doc-title">TAX INVOICE</div>
        <div class="rule"></div>
        <div class="name">Name: <span class="b">${esc(customer)}</span></div>
        <div class="rule-dash"></div>
        <table class="meta">
          <tr>
            <td>Date: ${esc(order.order_date || order.order_datetime || '')}</td>
            <td class="r">Bill#: ${esc(billNo)}</td>
          </tr>
          <tr>
            <td>Time: ${esc(order.order_time || '')}</td>
            <td class="r">${timeRowRight}</td>
          </tr>
          ${opts.cashierName ? `<tr><td>Cashier: ${esc(opts.cashierName)}</td><td class="r">${opts.tableLabel ? esc(orderType) : ''}</td></tr>` : ''}
        </table>
        <table class="grid">
          <thead><tr><th class="l">Item</th><th class="qty">Qty</th><th class="price">Price</th><th class="amt">Amt</th></tr></thead>
          <tbody>${rows}</tbody>
        </table>
        <div class="rule"></div>
        <table class="tot">${preGrandRows}</table>
        <div class="rule"></div>
        <table class="tot">${grandRows}</table>
        <div class="rule"></div>
        <div class="thanks">Thank you for dining with us!<br>Visit again.</div>
        ${poweredByHtml(opts.poweredBy || order.powered_by)}
        <div class="rule-dash"></div>
      </div>
    </body></html>`;
}

export async function printKotIframe(payload, printerHint = '', options = {}) {
    await printHtmlDocument(buildKotHtml(payload, printerHint), options);
}

export async function printBillIframe(order, opts = {}) {
    await printHtmlDocument(buildBillHtml(order, opts), {force: opts.force});
}

export default {
    printKotIframe,
    printBillIframe,
    buildKotHtml,
    buildBillHtml,
};
