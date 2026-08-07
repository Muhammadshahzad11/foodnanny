/**
 * One-click POS printing helper for Windows, macOS, and Linux.
 * Downloads an OS-specific launcher that starts the local agent + Chrome silent print.
 */

import { isSilentPrintReady, setPrintPreviewOn, setSilentPrintReady } from './printPreference.js';

export const SIMPLE_PRINT_SETUP_PATH = '/simple-print/';

export function simplePrintSetupUrl() {
    return `${window.location.origin}${SIMPLE_PRINT_SETUP_PATH}`;
}

/**
 * @returns {'windows'|'mac'|'linux'|'other'}
 */
export function detectClientOs() {
    const ua = (navigator.userAgent || '').toLowerCase();
    const platform = (navigator.platform || '').toLowerCase();
    if (platform.includes('win') || ua.includes('windows')) return 'windows';
    if (platform.includes('mac') || ua.includes('mac os') || ua.includes('macintosh')) return 'mac';
    if (platform.includes('linux') || ua.includes('linux') || ua.includes('x11')) return 'linux';
    return 'other';
}

export function clientOsLabel(os = detectClientOs()) {
    if (os === 'windows') return 'Windows';
    if (os === 'mac') return 'Mac';
    if (os === 'linux') return 'Linux';
    return 'this computer';
}

export async function isLocalAgentRunning(timeoutMs = 2500) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeoutMs);
    try {
        const res = await fetch('http://127.0.0.1:1811/health', {
            mode: 'cors',
            signal: controller.signal,
        });
        const data = await res.json().catch(() => ({}));
        return !!(res.ok && data.ok);
    } catch (e) {
        return false;
    } finally {
        clearTimeout(timer);
    }
}

/** True when POS can print without Chrome preview dialog. */
export async function isClientPrintReady() {
    if (isSilentPrintReady()) {
        return true;
    }
    return isLocalAgentRunning();
}

function downloadBlob(content, filename, mime = 'application/octet-stream') {
    const blob = new Blob([content], { type: mime });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(() => URL.revokeObjectURL(url), 2000);
    try {
        setPrintPreviewOn(false);
    } catch (e) {
        // ignore
    }
}

/**
 * Build one-click Windows launcher (.bat).
 */
export function buildWindowsLauncher(origin = window.location.origin) {
    const posUrl = `${origin}/admin/pos?silent_print=1`;
    const agentUrl = `${origin}/local-print-agent/Start-LocalPrintAgent.ps1`;
    const pyAgentUrl = `${origin}/local-print-agent/print_agent.py`;

    return [
        '@echo off',
        'title Cost to Cost Foods - Enable Printing',
        'color 0A',
        'echo.',
        'echo  ========================================',
        'echo   Cost to Cost Foods - Enable Printing',
        'echo  ========================================',
        'echo.',
        'echo  Starting print helper...',
        'set "AGENT_PS=%TEMP%\\fn-local-print-agent.ps1"',
        'set "AGENT_PY=%TEMP%\\fn-print-agent.py"',
        '',
        `powershell -NoProfile -ExecutionPolicy Bypass -Command "try { Invoke-WebRequest -Uri '${agentUrl}' -OutFile '%AGENT_PS%' -UseBasicParsing } catch { exit 1 }"`,
        'if errorlevel 1 (',
        `  powershell -NoProfile -ExecutionPolicy Bypass -Command "try { Invoke-WebRequest -Uri '${pyAgentUrl}' -OutFile '%AGENT_PY%' -UseBasicParsing } catch { exit 1 }"`,
        '  if errorlevel 1 (',
        '    echo  Could not download print helper. Check internet and try again.',
        '    pause',
        '    exit /b 1',
        '  )',
        '  where python >nul 2>&1 && start "FN Local Print Agent" /MIN python "%AGENT_PY%"',
        '  where python >nul 2>&1 || where py >nul 2>&1 && start "FN Local Print Agent" /MIN py -3 "%AGENT_PY%"',
        ') else (',
        '  start "FN Local Print Agent" /MIN powershell -NoProfile -ExecutionPolicy Bypass -WindowStyle Minimized -File "%AGENT_PS%"',
        ')',
        'timeout /t 2 /nobreak >nul',
        '',
        'set "CHROME="',
        'if exist "%ProgramFiles%\\Google\\Chrome\\Application\\chrome.exe" set "CHROME=%ProgramFiles%\\Google\\Chrome\\Application\\chrome.exe"',
        'if exist "%ProgramFiles(x86)%\\Google\\Chrome\\Application\\chrome.exe" set "CHROME=%ProgramFiles(x86)%\\Google\\Chrome\\Application\\chrome.exe"',
        'if exist "%LocalAppData%\\Google\\Chrome\\Application\\chrome.exe" set "CHROME=%LocalAppData%\\Google\\Chrome\\Application\\chrome.exe"',
        'if exist "%ProgramFiles(x86)%\\Microsoft\\Edge\\Application\\msedge.exe" if "%CHROME%"=="" set "CHROME=%ProgramFiles(x86)%\\Microsoft\\Edge\\Application\\msedge.exe"',
        'if exist "%ProgramFiles%\\Microsoft\\Edge\\Application\\msedge.exe" if "%CHROME%"=="" set "CHROME=%ProgramFiles%\\Microsoft\\Edge\\Application\\msedge.exe"',
        '',
        'if "%CHROME%"=="" (',
        '  echo  Chrome/Edge not found. Install Google Chrome, then run this file again.',
        `  start "" "${posUrl}"`,
        '  pause',
        '  exit /b 1',
        ')',
        '',
        `start "" "%CHROME%" --kiosk-printing --disable-print-preview --new-window "${posUrl}"`,
        'echo  Done! Always open POS with this file for printing without popup.',
        'echo  Tip: Set your thermal printer as the Windows default printer.',
        'timeout /t 4 >nul',
        'exit /b 0',
        '',
    ].join('\r\n');
}

/**
 * Shared Unix launcher body (macOS .command / Linux .sh).
 */
function buildUnixLauncher(origin = window.location.origin, { mac = false } = {}) {
    const posUrl = `${origin}/admin/pos?silent_print=1`;
    const agentUrl = `${origin}/local-print-agent/print_agent.py`;

    const chromeFind = mac
        ? [
            'CHROME=""',
            'if [ -x "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" ]; then',
            '  CHROME="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"',
            'elif [ -x "/Applications/Chromium.app/Contents/MacOS/Chromium" ]; then',
            '  CHROME="/Applications/Chromium.app/Contents/MacOS/Chromium"',
            'elif [ -x "/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge" ]; then',
            '  CHROME="/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge"',
            'fi',
        ]
        : [
            'CHROME=""',
            'for c in google-chrome google-chrome-stable chromium-browser chromium microsoft-edge microsoft-edge-stable; do',
            '  if command -v "$c" >/dev/null 2>&1; then CHROME="$c"; break; fi',
            'done',
        ];

    return [
        '#!/bin/bash',
        'set +e',
        mac ? 'cd "$(dirname "$0")" >/dev/null 2>&1' : '',
        'echo ""',
        'echo "========================================"',
        'echo "  Cost to Cost Foods - Enable Printing"',
        'echo "========================================"',
        'echo ""',
        `ORIGIN="${origin}"`,
        `POS_URL="${posUrl}"`,
        `AGENT_URL="${agentUrl}"`,
        'AGENT="${TMPDIR:-/tmp}/fn-print-agent.py"',
        '',
        'echo "Step 1: Downloading print helper..."',
        'if command -v curl >/dev/null 2>&1; then',
        '  curl -fsSL "$AGENT_URL" -o "$AGENT"',
        'elif command -v wget >/dev/null 2>&1; then',
        '  wget -q -O "$AGENT" "$AGENT_URL"',
        'else',
        '  echo "Need curl or wget. Install one and try again."',
        '  read -r -p "Press Enter to close..." _',
        '  exit 1',
        'fi',
        '',
        'if [ ! -s "$AGENT" ]; then',
        '  echo "Download failed. Check internet and try again."',
        '  read -r -p "Press Enter to close..." _',
        '  exit 1',
        'fi',
        '',
        'PYTHON=""',
        'if command -v python3 >/dev/null 2>&1; then PYTHON=python3',
        'elif command -v python >/dev/null 2>&1; then PYTHON=python',
        'fi',
        'if [ -z "$PYTHON" ]; then',
        '  echo "Python 3 is required. On Mac: install from python.org or run: brew install python"',
        '  read -r -p "Press Enter to close..." _',
        '  exit 1',
        'fi',
        '',
        'echo "Step 2: Starting print helper in background..."',
        '# Stop previous agent on same port if still running',
        'if command -v lsof >/dev/null 2>&1; then',
        '  OLD_PIDS=$(lsof -tiTCP:1811 -sTCP:LISTEN 2>/dev/null)',
        '  if [ -n "$OLD_PIDS" ]; then kill $OLD_PIDS 2>/dev/null; sleep 1; fi',
        'fi',
        'nohup "$PYTHON" "$AGENT" --port 1811 >/tmp/fn-print-agent.log 2>&1 &',
        'sleep 2',
        '',
        'echo "Step 3: Opening POS with direct print..."',
        ...chromeFind,
        'if [ -z "$CHROME" ]; then',
        '  echo "Google Chrome not found. Opening POS in default browser (print popup may appear)."',
        mac ? '  open "$POS_URL"' : '  (xdg-open "$POS_URL" >/dev/null 2>&1 || true)',
        '  read -r -p "Press Enter to close..." _',
        '  exit 1',
        'fi',
        '',
        '"$CHROME" --kiosk-printing --disable-print-preview --new-window "$POS_URL" >/dev/null 2>&1 &',
        'echo ""',
        'echo "Done! Always open POS with this file for printing without popup."',
        mac
            ? 'echo "Tip: System Settings > Printers — set your thermal printer as default."'
            : 'echo "Tip: Set your thermal printer as the system default printer."',
        'sleep 3',
        'exit 0',
        '',
    ].filter((line) => line !== undefined).join('\n');
}

export function buildMacLauncher(origin = window.location.origin) {
    return buildUnixLauncher(origin, { mac: true });
}

export function buildLinuxLauncher(origin = window.location.origin) {
    return buildUnixLauncher(origin, { mac: false });
}

/**
 * Download the correct one-click helper for this OS.
 * @returns {{ os: string, filename: string }}
 */
export function downloadSimplePrintHelper() {
    const os = detectClientOs();
    let filename;
    let content;

    if (os === 'mac') {
        filename = 'Enable-POS-Printing.command';
        content = buildMacLauncher();
    } else if (os === 'linux') {
        filename = 'Enable-POS-Printing.sh';
        content = buildLinuxLauncher();
    } else {
        // Windows + unknown → Windows bat (most POS terminals)
        filename = 'Enable-POS-Printing.bat';
        content = buildWindowsLauncher();
    }

    downloadBlob(content, filename);
    return { os, filename };
}

/** @deprecated use downloadSimplePrintHelper */
export function downloadSimplePrintBat() {
    return downloadSimplePrintHelper();
}

export function buildSimplePrintBat(origin = window.location.origin) {
    return buildWindowsLauncher(origin);
}

/** Mark print ready after user confirms helper is running (or silent flag detected). */
export function markSimplePrintReady() {
    setSilentPrintReady(true);
    setPrintPreviewOn(false);
}

export default {
    SIMPLE_PRINT_SETUP_PATH,
    simplePrintSetupUrl,
    detectClientOs,
    clientOsLabel,
    isLocalAgentRunning,
    isClientPrintReady,
    buildWindowsLauncher,
    buildMacLauncher,
    buildLinuxLauncher,
    buildSimplePrintBat,
    downloadSimplePrintHelper,
    downloadSimplePrintBat,
    markSimplePrintReady,
};
