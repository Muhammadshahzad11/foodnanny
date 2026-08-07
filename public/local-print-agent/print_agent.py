#!/usr/bin/env python3
"""
Local Print Agent — Cost to Cost Foods / FoodNanny
Cross-platform (Windows / macOS / Linux).

Receives ESC/POS from the browser and forwards raw bytes to the thermal
printer IP:port (usually :9100).

Usage:
  python3 print_agent.py
  python3 print_agent.py --port 1811

Keep this process running while using POS.
"""

from __future__ import annotations

import argparse
import base64
import json
import socket
import sys
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer


LISTEN_HOST = "127.0.0.1"
DEFAULT_PORT = 1811


def send_to_printer(ip: str, port: int, payload: bytes, timeout: float = 5.0) -> int:
    with socket.create_connection((ip, port), timeout=timeout) as sock:
        sock.settimeout(timeout)
        sock.sendall(payload)
    return len(payload)


class PrintHandler(BaseHTTPRequestHandler):
    server_version = "FNPrintAgent/1.0"

    def log_message(self, fmt: str, *args) -> None:
        sys.stdout.write("[%s] %s\n" % (self.log_date_time_string(), fmt % args))
        sys.stdout.flush()

    def _cors(self) -> None:
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Methods", "GET, POST, OPTIONS")
        self.send_header("Access-Control-Allow-Headers", "Content-Type, Accept")

    def _json(self, status: int, obj: dict) -> None:
        body = json.dumps(obj).encode("utf-8")
        self.send_response(status)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self._cors()
        self.end_headers()
        self.wfile.write(body)

    def do_OPTIONS(self) -> None:
        self.send_response(204)
        self._cors()
        self.end_headers()

    def do_GET(self) -> None:
        path = self.path.split("?", 1)[0].rstrip("/") or "/"
        if path in ("/", "/health"):
            self._json(200, {"ok": True, "service": "local-print-agent", "port": self.server.server_port})
            return
        self._json(404, {"ok": False, "message": "Not found. POST /print"})

    def do_POST(self) -> None:
        path = self.path.split("?", 1)[0].rstrip("/") or "/"
        if path != "/print":
            self._json(404, {"ok": False, "message": "Not found. POST /print"})
            return

        length = int(self.headers.get("Content-Length") or 0)
        raw = self.rfile.read(length) if length > 0 else b"{}"
        try:
            body = json.loads(raw.decode("utf-8") or "{}")
        except Exception:
            self._json(400, {"ok": False, "message": "Invalid JSON"})
            return

        ip = str(body.get("ip") or "").strip()
        port = int(body.get("port") or 9100)
        data = str(body.get("data") or "")

        if not ip or not data:
            self._json(400, {"ok": False, "message": "ip and data are required"})
            return

        try:
            payload = base64.b64decode(data)
            written = send_to_printer(ip, port, payload)
            self.log_message("Printed %s bytes -> %s:%s", written, ip, port)
            self._json(200, {"ok": True, "bytes": written, "ip": ip, "port": port})
        except Exception as exc:
            self.log_message("ERROR: %s", exc)
            self._json(500, {"ok": False, "message": str(exc)})


def main() -> int:
    parser = argparse.ArgumentParser(description="FoodNanny local print agent")
    parser.add_argument("--host", default=LISTEN_HOST)
    parser.add_argument("--port", type=int, default=DEFAULT_PORT)
    args = parser.parse_args()

    try:
        httpd = ThreadingHTTPServer((args.host, args.port), PrintHandler)
    except OSError as exc:
        sys.stderr.write("Could not bind %s:%s — %s\n" % (args.host, args.port, exc))
        return 1

    print("")
    print("========================================")
    print("  Local Print Agent  (port %s)" % args.port)
    print("========================================")
    print("Keep this window open while using POS.")
    print("Press Ctrl+C to stop.")
    print("")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\nStopped.")
    finally:
        httpd.server_close()
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
