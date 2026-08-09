# Local Print Agent v3 - buildwithnexclass (TcpListener - no URL ACL / HttpListener conflicts)
# Silent print: network IP:9100 + USB Windows printer name

param(
    [int]$ListenPort = 1811,
    [switch]$InstallStartup
)

$ErrorActionPreference = 'Stop'
$AgentVersion = 3

if ($InstallStartup) {
    $startup = [Environment]::GetFolderPath('Startup')
    $target = Join-Path $startup 'buildwithnexclass-LocalPrintAgent.cmd'
    $self = $MyInvocation.MyCommand.Path
    $cmd = '@echo off' + [Environment]::NewLine + 'powershell -ExecutionPolicy Bypass -WindowStyle Minimized -File "' + $self + '"' + [Environment]::NewLine
    Set-Content -Path $target -Value $cmd -Encoding ASCII
    Write-Host "Installed to Startup: $target" -ForegroundColor Green
}

Add-Type -TypeDefinition @'
using System;
using System.Runtime.InteropServices;
public class RawPrinterHelper {
  [StructLayout(LayoutKind.Sequential, CharSet=CharSet.Ansi)]
  public class DOCINFOA {
    [MarshalAs(UnmanagedType.LPStr)] public string pDocName;
    [MarshalAs(UnmanagedType.LPStr)] public string pOutputFile;
    [MarshalAs(UnmanagedType.LPStr)] public string pDataType;
  }
  [DllImport("winspool.Drv", EntryPoint="OpenPrinterA", SetLastError=true, CharSet=CharSet.Ansi, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]
  public static extern bool OpenPrinter([MarshalAs(UnmanagedType.LPStr)] string szPrinter, out IntPtr hPrinter, IntPtr pd);
  [DllImport("winspool.Drv", EntryPoint="ClosePrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]
  public static extern bool ClosePrinter(IntPtr hPrinter);
  [DllImport("winspool.Drv", EntryPoint="StartDocPrinterA", SetLastError=true, CharSet=CharSet.Ansi, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]
  public static extern bool StartDocPrinter(IntPtr hPrinter, int level, [In, MarshalAs(UnmanagedType.LPStruct)] DOCINFOA di);
  [DllImport("winspool.Drv", EntryPoint="EndDocPrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]
  public static extern bool EndDocPrinter(IntPtr hPrinter);
  [DllImport("winspool.Drv", EntryPoint="StartPagePrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]
  public static extern bool StartPagePrinter(IntPtr hPrinter);
  [DllImport("winspool.Drv", EntryPoint="EndPagePrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]
  public static extern bool EndPagePrinter(IntPtr hPrinter);
  [DllImport("winspool.Drv", EntryPoint="WritePrinter", SetLastError=true, ExactSpelling=true, CallingConvention=CallingConvention.StdCall)]
  public static extern bool WritePrinter(IntPtr hPrinter, IntPtr pBytes, int dwCount, out int dwWritten);
  public static string LastError = "";
  public static bool SendBytes(string printerName, byte[] bytes) {
    LastError = "";
    IntPtr hPrinter;
    if (!OpenPrinter(printerName, out hPrinter, IntPtr.Zero)) {
      LastError = "OpenPrinter failed err=" + Marshal.GetLastWin32Error();
      return false;
    }
    try {
      DOCINFOA di = new DOCINFOA();
      di.pDocName = "buildwithnexclass ESC/POS";
      di.pDataType = "RAW";
      if (!StartDocPrinter(hPrinter, 1, di)) {
        LastError = "StartDocPrinter failed err=" + Marshal.GetLastWin32Error();
        return false;
      }
      try {
        if (!StartPagePrinter(hPrinter)) {
          LastError = "StartPagePrinter failed err=" + Marshal.GetLastWin32Error();
          return false;
        }
        try {
          IntPtr p = Marshal.AllocCoTaskMem(bytes.Length);
          Marshal.Copy(bytes, 0, p, bytes.Length);
          int written;
          bool ok = WritePrinter(hPrinter, p, bytes.Length, out written);
          Marshal.FreeCoTaskMem(p);
          if (!ok) LastError = "WritePrinter failed err=" + Marshal.GetLastWin32Error();
          return ok;
        } finally { EndPagePrinter(hPrinter); }
      } finally { EndDocPrinter(hPrinter); }
    } finally { ClosePrinter(hPrinter); }
  }
}
'@

function Get-WindowsPrinterNames {
    $names = @()
    try { $names = @(Get-Printer -ErrorAction SilentlyContinue | ForEach-Object { $_.Name }) } catch {}
    if ($names.Count -eq 0) {
        try { $names = @(Get-CimInstance Win32_Printer -ErrorAction SilentlyContinue | ForEach-Object { $_.Name }) } catch {}
    }
    return @($names | Where-Object { $_ } | Sort-Object -Unique)
}

function Resolve-WindowsPrinterName([string]$Requested) {
    $wanted = $Requested.Trim()
    $all = Get-WindowsPrinterNames
    if ($all.Count -eq 0) {
        return @{ ok = $false; name = $wanted; printers = @(); reason = 'No Windows printers found on this PC.' }
    }
    foreach ($n in $all) { if ($n -eq $wanted) { return @{ ok = $true; name = $n; printers = $all; reason = '' } } }
    foreach ($n in $all) {
        if ($n.ToLowerInvariant() -eq $wanted.ToLowerInvariant()) {
            return @{ ok = $true; name = $n; printers = $all; reason = '' }
        }
    }
    $needle = $wanted.ToLowerInvariant()
    $hits = @($all | Where-Object {
        $l = $_.ToLowerInvariant()
        return ($l.Contains($needle) -or $needle.Contains($l))
    })
    if ($hits.Count -eq 0) {
        $hits = @($all | Where-Object {
            $l = $_.ToLowerInvariant()
            return ($l.Contains('rp3200') -and ($l.Contains('bill') -or $l.Contains('lite')))
        })
    }
    if ($hits.Count -eq 0) {
        $hits = @($all | Where-Object {
            $l = $_.ToLowerInvariant()
            return ($l.Contains('rp3200') -or $l.Contains('tvse'))
        })
    }
    if ($hits.Count -gt 0) {
        $prefer = @($hits | Where-Object { $_.ToLowerInvariant().Contains('bill') -and $_ -notmatch 'redirected|AnyDesk|OneNote|Fax|Print to PDF|XPS' })
        if ($prefer.Count -gt 0) { return @{ ok = $true; name = $prefer[0]; printers = $all; reason = '' } }
        $local = @($hits | Where-Object { $_ -notmatch 'redirected|AnyDesk|OneNote|Fax|Print to PDF|XPS' })
        if ($local.Count -gt 0) { return @{ ok = $true; name = $local[0]; printers = $all; reason = '' } }
        return @{ ok = $true; name = $hits[0]; printers = $all; reason = '' }
    }
    return @{ ok = $false; name = $wanted; printers = $all; reason = 'No match. Available: ' + ($all -join ' | ') }
}

function Send-ViaCopyFallback([string]$PrinterName, [byte[]]$Payload) {
    $tmp = [System.IO.Path]::Combine([System.IO.Path]::GetTempPath(), ('fn-print-' + [Guid]::NewGuid().ToString('N') + '.bin'))
    try {
        [System.IO.File]::WriteAllBytes($tmp, $Payload)
        $p = Start-Process -FilePath 'cmd.exe' -ArgumentList @('/c', 'copy', '/b', $tmp, ('\\localhost\' + $PrinterName)) -Wait -PassThru -WindowStyle Hidden
        return ($p.ExitCode -eq 0)
    } catch { return $false } finally {
        try { Remove-Item -Force -ErrorAction SilentlyContinue $tmp } catch {}
    }
}

function Send-ToWindowsPrinter([string]$PrinterName, [byte[]]$Payload) {
    $resolved = Resolve-WindowsPrinterName -Requested $PrinterName
    if (-not $resolved.ok) { throw ('Windows printer not found: "' + $PrinterName + '". ' + $resolved.reason) }
    $useName = [string]$resolved.name
    $ok = [RawPrinterHelper]::SendBytes($useName, $Payload)
    if (-not $ok) {
        Write-Host ('RAW failed for ' + $useName + ' (' + [RawPrinterHelper]::LastError + ') - trying copy fallback...') -ForegroundColor Yellow
        $ok = Send-ViaCopyFallback -PrinterName $useName -Payload $Payload
    }
    if (-not $ok) {
        throw ('Windows print failed for "' + $useName + '". ' + [RawPrinterHelper]::LastError + ' Available: ' + ($resolved.printers -join ' | '))
    }
    return @{ bytes = $Payload.Length; printer = $useName }
}

function Send-ToNetworkPrinter([string]$Ip, [int]$Port, [byte[]]$Payload) {
    $client = [System.Net.Sockets.TcpClient]::new()
    try {
        $iar = $client.BeginConnect($Ip, $Port, $null, $null)
        $ok = $iar.AsyncWaitHandle.WaitOne(3000, $false)
        if (-not $ok -or -not $client.Connected) { throw ('Cannot connect to printer ' + $Ip + ':' + $Port) }
        $client.EndConnect($iar) | Out-Null
        $stream = $client.GetStream()
        $stream.WriteTimeout = 5000
        $stream.Write($Payload, 0, $Payload.Length)
        $stream.Flush()
        return $Payload.Length
    } finally { $client.Close() }
}

function Read-HttpRequest([System.Net.Sockets.NetworkStream]$Stream) {
    $buffer = New-Object byte[] 65536
    $ms = New-Object System.IO.MemoryStream
    $Stream.ReadTimeout = 8000
    while ($true) {
        $n = $Stream.Read($buffer, 0, $buffer.Length)
        if ($n -le 0) { break }
        $ms.Write($buffer, 0, $n)
        $text = [System.Text.Encoding]::ASCII.GetString($ms.ToArray())
        $idx = $text.IndexOf("`r`n`r`n")
        if ($idx -ge 0) {
            $headerText = $text.Substring(0, $idx)
            $bodyStart = $idx + 4
            $contentLength = 0
            foreach ($line in ($headerText -split "`r`n")) {
                if ($line -match '^(?i)Content-Length:\s*(\d+)') { $contentLength = [int]$Matches[1] }
            }
            $bodyBytesSoFar = $ms.Length - $bodyStart
            while ($bodyBytesSoFar -lt $contentLength) {
                $n2 = $Stream.Read($buffer, 0, $buffer.Length)
                if ($n2 -le 0) { break }
                $ms.Write($buffer, 0, $n2)
                $bodyBytesSoFar = $ms.Length - $bodyStart
            }
            $all = $ms.ToArray()
            $body = ''
            if ($contentLength -gt 0 -and $all.Length -ge ($bodyStart + $contentLength)) {
                $body = [System.Text.Encoding]::UTF8.GetString($all, $bodyStart, $contentLength)
            } elseif ($all.Length -gt $bodyStart) {
                $body = [System.Text.Encoding]::UTF8.GetString($all, $bodyStart, $all.Length - $bodyStart)
            }
            $first = ($headerText -split "`r`n")[0]
            $method = 'GET'
            $path = '/'
            if ($first -match '^(GET|POST|OPTIONS)\s+(\S+)') {
                $method = $Matches[1].ToUpperInvariant()
                $path = ($Matches[2] -split '\?')[0]
            }
            return @{ method = $method; path = $path.TrimEnd('/').ToLowerInvariant(); body = $body }
        }
        if ($ms.Length -gt 2MB) { break }
    }
    return $null
}

function Write-HttpResponse([System.Net.Sockets.NetworkStream]$Stream, [int]$Status, [string]$Json) {
    $payload = [System.Text.Encoding]::UTF8.GetBytes($Json)
    $reason = switch ($Status) { 200 { 'OK' } 204 { 'No Content' } 400 { 'Bad Request' } 404 { 'Not Found' } default { 'Error' } }
    $header = "HTTP/1.1 $Status $reason`r`nAccess-Control-Allow-Origin: *`r`nAccess-Control-Allow-Methods: POST, OPTIONS, GET`r`nAccess-Control-Allow-Headers: Content-Type, Accept`r`nContent-Type: application/json; charset=utf-8`r`nContent-Length: $($payload.Length)`r`nConnection: close`r`n`r`n"
    $headerBytes = [System.Text.Encoding]::ASCII.GetBytes($header)
    $Stream.Write($headerBytes, 0, $headerBytes.Length)
    if ($Status -ne 204 -and $payload.Length -gt 0) { $Stream.Write($payload, 0, $payload.Length) }
    $Stream.Flush()
}

function Stop-OtherAgentsOnPort([int]$Port) {
    try {
        Get-NetTCPConnection -LocalPort $Port -ErrorAction SilentlyContinue | ForEach-Object {
            if ($_.OwningProcess -and $_.OwningProcess -gt 0 -and $_.OwningProcess -ne $PID) {
                Stop-Process -Id $_.OwningProcess -Force -ErrorAction SilentlyContinue
            }
        }
    } catch {}
    try {
        Get-CimInstance Win32_Process -ErrorAction SilentlyContinue | Where-Object {
            $_.Name -match 'powershell|pwsh|python' -and $_.CommandLine -and
            $_.CommandLine -match 'LocalPrintAgent|print_agent|Start-LocalPrintAgent' -and
            $_.ProcessId -ne $PID
        } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }
    } catch {}
    Start-Sleep -Seconds 1
    # Clear leftover HttpListener URL ACLs that cause "conflicts with an existing registration"
    try {
        Start-Process -FilePath 'netsh' -ArgumentList @('http','delete','urlacl',"url=http://127.0.0.1:$Port/") -WindowStyle Hidden -Wait -ErrorAction SilentlyContinue | Out-Null
        Start-Process -FilePath 'netsh' -ArgumentList @('http','delete','urlacl',"url=http://+:$Port/") -WindowStyle Hidden -Wait -ErrorAction SilentlyContinue | Out-Null
    } catch {}
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host ("  Local Print Agent v" + $AgentVersion + "  (port " + $ListenPort + ")") -ForegroundColor Green
Write-Host "  Network IP + USB Windows printers" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host "Keep this window open while using POS."
Write-Host ""

$installed = Get-WindowsPrinterNames
Write-Host "Windows printers on this PC:" -ForegroundColor Cyan
if ($installed.Count -eq 0) { Write-Host "  (none found)" -ForegroundColor Yellow }
else { foreach ($n in $installed) { Write-Host ("  - " + $n) } }
Write-Host "Counter bill should use: RP3200 lite bill" -ForegroundColor Yellow
Write-Host ""

Write-Host "Freeing port $ListenPort if needed..." -ForegroundColor Yellow
Stop-OtherAgentsOnPort -Port $ListenPort

$listener = $null
try {
    $listener = [System.Net.Sockets.TcpListener]::new([System.Net.IPAddress]::Loopback, $ListenPort)
    $listener.Start()
    Write-Host ("Listening on http://127.0.0.1:$ListenPort/ (TCP)") -ForegroundColor Cyan
} catch {
    Write-Host ('FAILED to start on port ' + $ListenPort + ': ' + $_.Exception.Message) -ForegroundColor Red
    Write-Host 'Close ALL other PowerShell windows (Task Manager), then run Setup again.' -ForegroundColor Red
    Read-Host 'Press Enter to close'
    exit 1
}

while ($true) {
    $client = $null
    $stream = $null
    try {
        $client = $listener.AcceptTcpClient()
        $stream = $client.GetStream()
        $req = Read-HttpRequest -Stream $stream
        if (-not $req) {
            Write-HttpResponse -Stream $stream -Status 400 -Json '{"ok":false,"message":"Bad request"}'
            continue
        }

        $method = $req.method
        $path = $req.path
        if ([string]::IsNullOrWhiteSpace($path)) { $path = '/' }

        if ($method -eq 'OPTIONS') {
            Write-HttpResponse -Stream $stream -Status 204 -Json ''
            continue
        }

        if ($method -eq 'GET' -and ($path -eq '/' -or $path -eq '/health')) {
            $json = '{"ok":true,"service":"local-print-agent","port":' + $ListenPort + ',"version":' + $AgentVersion + ',"features":["network","windows"]}'
            Write-HttpResponse -Stream $stream -Status 200 -Json $json
            continue
        }

        if ($method -eq 'GET' -and ($path -eq '/printers' -or $path.EndsWith('/printers'))) {
            $names = Get-WindowsPrinterNames
            $parts = @()
            foreach ($n in $names) {
                $esc = $n.Replace('\', '\\').Replace('"', '\"')
                $parts += ('"' + $esc + '"')
            }
            Write-HttpResponse -Stream $stream -Status 200 -Json ('{"ok":true,"printers":[' + ($parts -join ',') + ']}')
            continue
        }

        if ($method -eq 'POST' -and ($path -eq '/print' -or $path.EndsWith('/print'))) {
            $body = $req.body | ConvertFrom-Json
            $data = [string]$body.data
            if ($body.windows_printer) { $windowsPrinter = [string]$body.windows_printer }
            elseif ($body.windows_printer_name) { $windowsPrinter = [string]$body.windows_printer_name }
            else { $windowsPrinter = '' }
            $ip = [string]$body.ip
            if ($body.port) { $port = [int]$body.port } else { $port = 9100 }

            if ([string]::IsNullOrWhiteSpace($data)) {
                Write-HttpResponse -Stream $stream -Status 400 -Json '{"ok":false,"message":"data (base64) is required"}'
                continue
            }

            $payload = [Convert]::FromBase64String($data)
            $stamp = Get-Date -Format 'HH:mm:ss'
            $bytes = 0
            $via = ''

            if (-not [string]::IsNullOrWhiteSpace($windowsPrinter)) {
                $printResult = Send-ToWindowsPrinter -PrinterName $windowsPrinter.Trim() -Payload $payload
                $bytes = [int]$printResult.bytes
                $via = 'windows:' + $printResult.printer
                Write-Host ('[' + $stamp + '] USB/Windows printed ' + $bytes + ' bytes -> ' + $printResult.printer) -ForegroundColor Cyan
            } elseif (-not [string]::IsNullOrWhiteSpace($ip)) {
                $bytes = Send-ToNetworkPrinter -Ip $ip.Trim() -Port $port -Payload $payload
                $via = 'tcp:' + $ip + ':' + $port
                Write-Host ('[' + $stamp + '] Network printed ' + $bytes + ' bytes -> ' + $ip + ':' + $port) -ForegroundColor Cyan
            } else {
                Write-HttpResponse -Stream $stream -Status 400 -Json '{"ok":false,"message":"windows_printer or ip is required"}'
                continue
            }

            $viaEsc = $via.Replace('\', '\\').Replace('"', '\"')
            Write-HttpResponse -Stream $stream -Status 200 -Json ('{"ok":true,"bytes":' + $bytes + ',"via":"' + $viaEsc + '"}')
            continue
        }

        Write-HttpResponse -Stream $stream -Status 404 -Json '{"ok":false,"message":"Not found. Use GET /health /printers or POST /print"}'
    } catch {
        $stamp = Get-Date -Format 'HH:mm:ss'
        $errMsg = $_.Exception.Message
        Write-Host ('[' + $stamp + '] ERROR: ' + $errMsg) -ForegroundColor Red
        try {
            if ($stream) {
                $esc = $errMsg.Replace('\', '\\').Replace('"', '\"')
                Write-HttpResponse -Stream $stream -Status 500 -Json ('{"ok":false,"message":"' + $esc + '"}')
            }
        } catch {}
    } finally {
        try { if ($stream) { $stream.Close() } } catch {}
        try { if ($client) { $client.Close() } } catch {}
    }
}
