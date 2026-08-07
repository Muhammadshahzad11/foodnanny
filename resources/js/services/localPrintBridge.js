/**
 * Send ESC/POS bytes to the Local Print Agent running on the POS PC.
 * Cloud servers cannot reach restaurant LAN printers (192.168.x.x);
 * the agent on the POS machine forwards TCP to printer_ip:9100.
 */

const DEFAULT_BRIDGE_PORT = 1811;
const REQUEST_TIMEOUT_MS = 8000;

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
 * @param {{ raw_base64?: string, printer_ip?: string, printer_port?: number, computer_ipv4?: string, bridge_port?: number }} job
 * @returns {Promise<{ok: boolean, via?: string}>}
 */
export async function sendViaLocalBridge(job = {}) {
    const raw = job.raw_base64;
    const ip = (job.printer_ip || '').trim();
    const port = Number(job.printer_port || 9100);

    if (!raw) {
        throw new Error('Missing print data for local agent.');
    }
    if (!ip) {
        throw new Error('Printer IP is missing.');
    }

    const body = { ip, port, data: raw };
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

    const err = new Error(
        lastError?.name === 'AbortError'
            ? 'Local Print Agent timed out. Start the agent on this PC.'
            : (lastError?.message || 'Local Print Agent is not running on this PC.')
    );
    err.code = 'LOCAL_BRIDGE_UNAVAILABLE';
    throw err;
}

/**
 * Quick health check for the Local Print Agent on this machine.
 */
export async function probeLocalAgent(bridgePort = DEFAULT_BRIDGE_PORT) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), 2500);
    try {
        const res = await fetch(`http://127.0.0.1:${bridgePort}/health`, {
            method: 'GET',
            signal: controller.signal,
            mode: 'cors',
        });
        const data = await res.json().catch(() => ({}));
        return !!(res.ok && data.ok);
    } catch (e) {
        return false;
    } finally {
        clearTimeout(timer);
    }
}

export function localAgentSetupUrl() {
    return `${window.location.origin}/local-print-agent/`;
}

export default {
    sendViaLocalBridge,
    probeLocalAgent,
    localAgentSetupUrl,
};
