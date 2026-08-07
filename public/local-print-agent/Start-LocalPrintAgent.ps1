# Local Print Agent — Cost to Cost Foods / FoodNanny
# Runs on the POS Windows PC. Receives ESC/POS from the browser and
# forwards raw bytes to the thermal printer IP:port (usually :9100).
#
# Usage (PowerShell as Administrator recommended once for firewall):
#   powershell -ExecutionPolicy Bypass -File .\Start-LocalPrintAgent.ps1
#
# Keep this window open while using POS.

param(
    [int]$ListenPort = 1811
)

$ErrorActionPreference = 'Stop'
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  Local Print Agent  (port $ListenPort)" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host "Keep this window open while using POS."
Write-Host "Press Ctrl+C to stop."
Write-Host ""

$listener = [System.Net.HttpListener]::new()
$prefix = "http://+:$ListenPort/"
try {
    $listener.Prefixes.Add($prefix)
    $listener.Start()
} catch {
    # Fall back to localhost-only if URL ACL is missing
    Write-Host "Could not bind $prefix — trying 127.0.0.1 only..." -ForegroundColor Yellow
    $listener = [System.Net.HttpListener]::new()
    $listener.Prefixes.Add("http://127.0.0.1:$ListenPort/")
    $listener.Start()
}

function Send-Cors([System.Net.HttpListenerResponse]$Response) {
    $Response.Headers.Add("Access-Control-Allow-Origin", "*")
    $Response.Headers.Add("Access-Control-Allow-Methods", "POST, OPTIONS, GET")
    $Response.Headers.Add("Access-Control-Allow-Headers", "Content-Type, Accept")
}

function Write-Json([System.Net.HttpListenerResponse]$Response, [int]$Status, [hashtable]$Obj) {
    $json = ($Obj | ConvertTo-Json -Compress)
    $bytes = [System.Text.Encoding]::UTF8.GetBytes($json)
    Send-Cors $Response
    $Response.StatusCode = $Status
    $Response.ContentType = "application/json; charset=utf-8"
    $Response.ContentLength64 = $bytes.Length
    $Response.OutputStream.Write($bytes, 0, $bytes.Length)
    $Response.OutputStream.Close()
}

function Send-ToPrinter([string]$Ip, [int]$Port, [byte[]]$Payload) {
    $client = [System.Net.Sockets.TcpClient]::new()
    try {
        $iar = $client.BeginConnect($Ip, $Port, $null, $null)
        $ok = $iar.AsyncWaitHandle.WaitOne(3000, $false)
        if (-not $ok -or -not $client.Connected) {
            throw "Cannot connect to printer ${Ip}:${Port}"
        }
        $client.EndConnect($iar) | Out-Null
        $stream = $client.GetStream()
        $stream.WriteTimeout = 5000
        $stream.Write($Payload, 0, $Payload.Length)
        $stream.Flush()
        return $Payload.Length
    } finally {
        $client.Close()
    }
}

while ($listener.IsListening) {
    $ctx = $listener.GetContext()
    $req = $ctx.Request
    $res = $ctx.Response
    $path = $req.Url.AbsolutePath.TrimEnd('/').ToLowerInvariant()

    try {
        if ($req.HttpMethod -eq 'OPTIONS') {
            Send-Cors $res
            $res.StatusCode = 204
            $res.Close()
            continue
        }

        if ($req.HttpMethod -eq 'GET' -and ($path -eq '' -or $path -eq '/' -or $path -eq '/health')) {
            Write-Json $res 200 @{ ok = $true; service = 'local-print-agent'; port = $ListenPort }
            continue
        }

        if ($req.HttpMethod -eq 'POST' -and $path -eq '/print') {
            $reader = [System.IO.StreamReader]::new($req.InputStream, $req.ContentEncoding)
            $rawJson = $reader.ReadToEnd()
            $reader.Close()
            $body = $rawJson | ConvertFrom-Json
            $ip = [string]$body.ip
            $port = [int]($(if ($body.port) { $body.port } else { 9100 }))
            $data = [string]$body.data

            if ([string]::IsNullOrWhiteSpace($ip) -or [string]::IsNullOrWhiteSpace($data)) {
                Write-Json $res 400 @{ ok = $false; message = 'ip and data are required' }
                continue
            }

            $payload = [Convert]::FromBase64String($data)
            $bytes = Send-ToPrinter -Ip $ip -Port $port -Payload $payload
            Write-Host ("[{0}] Printed {1} bytes -> {2}:{3}" -f (Get-Date -Format 'HH:mm:ss'), $bytes, $ip, $port) -ForegroundColor Cyan
            Write-Json $res 200 @{ ok = $true; bytes = $bytes; ip = $ip; port = $port }
            continue
        }

        Write-Json $res 404 @{ ok = $false; message = 'Not found. POST /print' }
    } catch {
        Write-Host ("[{0}] ERROR: {1}" -f (Get-Date -Format 'HH:mm:ss'), $_.Exception.Message) -ForegroundColor Red
        try {
            Write-Json $res 500 @{ ok = $false; message = $_.Exception.Message }
        } catch {}
    }
}
