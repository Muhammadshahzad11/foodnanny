/**
 * Isolated iframe thermal printing (iRestora-style browser popup).
 * Avoids POS page CSS collisions that cause blank pages, huge scale, and broken fonts.
 */

const THERMAL_CSS = `
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body {
    width: 80mm;
    margin: 0;
    padding: 0;
    background: #fff;
    color: #000;
    font-family: "Courier New", Courier, monospace;
    font-size: 12px;
    line-height: 1.25;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  @page { size: 80mm auto; margin: 0; }
  .sheet {
    width: 72mm;
    max-width: 72mm;
    margin: 0 auto;
    padding: 2mm 3mm 4mm;
  }
  .c { text-align: center; }
  .l { text-align: left; }
  .r { text-align: right; }
  .b { font-weight: 700; }
  .title { font-size: 14px; font-weight: 900; letter-spacing: 1px; margin-bottom: 2px; }
  .meta { font-size: 11px; }
  .dash { border-top: 1px dashed #000; margin: 4px 0; }
  .line { border-top: 1px solid #000; margin: 4px 0; }
  .banner {
    text-align: center;
    margin: 4px 0;
    padding: 3px 0;
    border-top: 2px solid #000;
    border-bottom: 2px solid #000;
  }
  .banner-type { font-size: 16px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; }
  .banner-table { font-size: 14px; font-weight: 900; text-transform: uppercase; }
  table { width: 100%; border-collapse: collapse; table-layout: fixed; }
  th, td { font-size: 11px; vertical-align: top; padding: 1px 0; word-wrap: break-word; }
  th { font-weight: 800; }
  .mod { font-size: 10px; padding-left: 8px; }
  .note { margin-top: 4px; font-weight: 800; font-size: 11px; white-space: pre-wrap; }
  .thanks { margin-top: 6px; font-weight: 800; text-align: center; }
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

async function printHtmlDocument(html) {
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
    const rows = items.map((item, idx) => {
        const mods = []
            .concat(item.variation_lines || [])
            .map((l) => `<div class="mod">- ${esc(l)}</div>`)
            .join('');
        const extras = (item.extra_lines || [])
            .map((l) => `<div class="mod">+ ${esc(l)}</div>`)
            .join('');
        const instr = item.instruction ? `<div class="mod">** ${esc(item.instruction)}</div>` : '';
        return `
          <tr>
            <td style="width:10%">${idx + 1}</td>
            <td style="width:72%">${esc(item.name)}${mods}${extras}${instr}</td>
            <td class="r" style="width:18%">${esc(item.quantity)}</td>
          </tr>`;
    }).join('');

    const table = payload.table_no || payload.table?.number || '';
    const hint = printerHint
        ? `<div class="hint">KOT — select kitchen printer${printerHint ? `: ${esc(printerHint)}` : ''}</div>`
        : `<div class="hint">KOT — select the KITCHEN / KOT printer</div>`;

    return `<!DOCTYPE html><html><head><meta charset="utf-8"><title>KOT</title><style>${THERMAL_CSS}</style></head><body>
      ${hint}
      <div class="sheet">
        <div class="c title">KITCHEN KOT</div>
        <div class="c meta">${esc(payload.order_date || '')} ${esc(payload.order_time || '')}</div>
        <div class="c b" style="margin:4px 0">${esc(payload.ticket_no || ('KOT - ' + (payload.kot_no || '')))}</div>
        <div class="banner">
          <div class="banner-type">${esc((payload.order_type_label || 'POS').toString().toUpperCase())}</div>
          ${table ? `<div class="banner-table">TABLE NO: ${esc(table)}</div>` : ''}
          ${!table && payload.counter ? `<div class="banner-table">${esc(payload.counter)} COUNTER</div>` : ''}
        </div>
        <div>Biller: ${esc(payload.biller || 'Cashier')}</div>
        ${payload.waiter ? `<div>Waiter: ${esc(payload.waiter)}</div>` : ''}
        <div class="dash"></div>
        <table>
          <thead><tr><th class="l">No.</th><th class="l">Item</th><th class="r">Qty</th></tr></thead>
          <tbody>${rows}</tbody>
        </table>
        <div class="dash"></div>
        <table><tr><td class="b">TOTAL QTY</td><td class="r b">${esc(payload.total_qty ?? items.reduce((s, i) => s + Number(i.quantity || 0), 0))}</td></tr></table>
        ${payload.special_note || payload.order_note ? `<div class="note">NOTE: ${esc(payload.special_note || payload.order_note)}</div>` : ''}
      </div>
    </body></html>`;
}

export function buildBillHtml(order = {}, opts = {}) {
    const restaurant = opts.restaurant || {};
    const items = Array.isArray(opts.items) ? opts.items : [];
    const hintName = opts.printerHint || '';
    const hint = `<div class="hint">BILL — select the BILL / COUNTER printer${hintName ? `: ${esc(hintName)}` : ''}</div>`;

    const money = (v) => {
        if (v == null || v === '') return '';
        return String(v);
    };

    const rows = items.map((item, idx) => {
        const name = item.name || item.item_name || '';
        const qty = item.quantity ?? '';
        const price = item.unitPrice || item.price || item.price_currency_price || '';
        const amt = item.priceLabel || item.total_price || item.total_price_currency_price || '';
        return `<tr>
          <td style="width:8%">${idx + 1}</td>
          <td style="width:42%">${esc(name)}</td>
          <td class="r" style="width:12%">${esc(qty)}</td>
          <td class="r" style="width:18%">${esc(price)}</td>
          <td class="r" style="width:20%">${esc(amt)}</td>
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

    return `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Bill</title><style>${THERMAL_CSS}</style></head><body>
      ${hint}
      <div class="sheet">
        <div class="c title">${esc(restaurant.name || 'Restaurant')}</div>
        ${restaurant.address ? `<div class="c meta">${esc(restaurant.address)}</div>` : ''}
        ${restaurant.phone ? `<div class="c meta">ph: ${esc((restaurant.country_code || '') + restaurant.phone)}</div>` : ''}
        <div class="line"></div>
        <div>Name: ${esc(customer)}</div>
        <div>Date: ${esc(order.order_date || order.order_datetime || '')}</div>
        ${order.order_time ? `<div>Time: ${esc(order.order_time)}</div>` : ''}
        ${opts.cashierName ? `<div>Cashier: ${esc(opts.cashierName)}</div>` : ''}
        ${opts.tableLabel ? `<div>Table: ${esc(opts.tableLabel)}</div>` : ''}
        <div class="b">${esc(orderType)}</div>
        <div>Bill No.: ${esc(order.order_serial_no || order.id || '')}</div>
        <div class="dash"></div>
        <table>
          <thead><tr><th class="l">No</th><th class="l">Item</th><th class="r">Qty</th><th class="r">Price</th><th class="r">Amt</th></tr></thead>
          <tbody>${rows}</tbody>
        </table>
        <div class="dash"></div>
        <table>
          <tr><td>Sub Total</td><td class="r">${esc(order.subtotal_currency_price || money(order.subtotal))}</td></tr>
          ${parseFloat(order.discount || 0) > 0 ? `<tr><td>Discount</td><td class="r">${esc(order.discount_currency_price || money(order.discount))}</td></tr>` : ''}
          ${parseFloat(order.total_tax || 0) > 0 ? `<tr><td>Tax</td><td class="r">${esc(order.total_tax_currency_price || money(order.total_tax))}</td></tr>` : ''}
          <tr><td class="b">Grand Total</td><td class="r b">${esc(order.total_currency_price || money(order.total))}</td></tr>
        </table>
        ${opts.paymentLabel ? `<div style="margin-top:4px">Payment: ${esc(opts.paymentLabel)}</div>` : ''}
        <div class="line"></div>
        <div class="thanks">Thank You | Visit Again!</div>
      </div>
    </body></html>`;
}

export async function printKotIframe(payload, printerHint = '') {
    await printHtmlDocument(buildKotHtml(payload, printerHint));
}

export async function printBillIframe(order, opts = {}) {
    await printHtmlDocument(buildBillHtml(order, opts));
}

export default {
    printKotIframe,
    printBillIframe,
    buildKotHtml,
    buildBillHtml,
};
