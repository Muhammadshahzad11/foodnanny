/**
 * Send ESC/POS bytes to the Local Print Agent on the POS PC.
 * Supports:
 *  - Network: { ip, port, data }
 *  - USB Windows: { windows_printer, data }
 */

const DEFAULT_BRIDGE_PORT = 1811;
const REQUEST_TIMEOUT_MS = 10000;

function bridgeUrls(job = {}) {
    const port = Number(job.bridge_port || DEFAULT_BRIDGE_PORT);
    const hosts = ['127.0.0.1'];
    const computerIp = (job.computer_ipv4 || '').trim();
    if (computerIp && computerIp !== '127.0.0.1' && computerIp !== 'localhost') {
        hosts.push(computerIp);
    }
    return hosts.map((host) => `http://${host}:${port}/print`);
}

async function postPrint(url, body) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), REQUEST_TIMEOUT_MS);
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify(body),
            signal: controller.signal,
            mode: 'cors',
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || data.ok === false) {
            throw new Error(data.message || `Local agent HTTP ${res.status}`);
        }
        return data;
    } finally {
        clearTimeout(timer);
    }
}

/**
 * @param {{ raw_base64?: string, printer_ip?: string, printer_port?: number, windows_printer_name?: string, computer_ipv4?: string, bridge_port?: number }} job
 */
export async function sendViaLocalBridge(job = {}) {
    const raw = job.raw_base64;
    const ip = (job.printer_ip || '').trim();
    const windowsPrinter = (job.windows_printer_name || job.windows_printer || '').trim();
    const port = Number(job.printer_port || 9100);

    if (!raw) {
        throw new Error('Missing print data for local agent.');
    }
    if (!ip && !windowsPrinter) {
        throw new Error('Printer IP or Windows printer name is missing.');
    }

    // USB/Windows bill printer must not also carry a kitchen IP — the agent
    // would then send the tax invoice to 192.168.1.10 with the KOT.
    const body = {
        data: raw,
        ...(windowsPrinter
            ? {
                windows_printer: windowsPrinter,
                windows_printer_name: windowsPrinter,
            }
            : { ip, port }),
    };

    const urls = bridgeUrls(job);
    let lastError = null;

    for (const url of urls) {
        try {
            const data = await postPrint(url, body);
            return { ok: true, via: url, ...data };
        } catch (err) {
            lastError = err;
        }
    }

    const msg = lastError?.message || 'Local Print Agent is not running on this PC.';
    // Old agent returns this when USB windows_printer is sent without ip
    if (/ip and data are required/i.test(msg)) {
        const err = new Error(
            'Old Print Agent is running. Close it, download Local Print Agent v2, run the new .ps1 (must say v2), then try again.'
        );
        err.code = 'LOCAL_AGENT_OUTDATED';
        throw err;
    }

    const err = new Error(
        lastError?.name === 'AbortError'
            ? 'Local Print Agent timed out. Start the agent on this PC.'
            : msg
    );
    err.code = 'LOCAL_BRIDGE_UNAVAILABLE';
    throw err;
}

/**
 * @returns {Promise<{ok: boolean, version?: number, features?: string[]}>}
 */
export async function probeLocalAgentInfo(bridgePort = DEFAULT_BRIDGE_PORT) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), 2500);
    try {
        const res = await fetch(`http://127.0.0.1:${bridgePort}/health`, {
            method: 'GET',
            signal: controller.signal,
            mode: 'cors',
        });
        const data = await res.json().catch(() => ({}));
        return {
            ok: !!(res.ok && data.ok),
            version: Number(data.version || 1),
            features: Array.isArray(data.features) ? data.features : [],
        };
    } catch (e) {
        return { ok: false, version: 0, features: [] };
    } finally {
        clearTimeout(timer);
    }
}

export async function probeLocalAgent(bridgePort = DEFAULT_BRIDGE_PORT) {
    const info = await probeLocalAgentInfo(bridgePort);
    return info.ok;
}

export function localAgentSetupUrl() {
    return `${window.location.origin}/local-print-agent/?v=2`;
}

export default {
    sendViaLocalBridge,
    probeLocalAgent,
    probeLocalAgentInfo,
    localAgentSetupUrl,
};
