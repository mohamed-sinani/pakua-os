<?php
/**
 * PakuaOS Web Download API
 * Streams real-time progress via SSE (Server-Sent Events)
 * Same experience as the CLI — percentage, speed, ETA, live bar.
 */

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

if (ob_get_level()) ob_end_clean();

function sse(string $event, array $data): void {
    echo "event: {$event}\n";
    echo "data: " . json_encode($data) . "\n\n";
    if (ob_get_level()) ob_end_flush();
    flush();
}

function formatBytes(int $bytes): string {
    if ($bytes <= 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = (int)floor((strlen((string)$bytes) - 1) / 3);
    $i = min($i, count($units) - 1);
    return sprintf('%.1f %s', $bytes / pow(1024, $i), $units[$i]);
}

function formatTime(int $seconds): string {
    if ($seconds < 60) return $seconds . 's';
    $h = (int)floor($seconds / 3600);
    $m = (int)floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    return $h > 0 ? "{$h}h {$m}m" : "{$m}m {$s}s";
}

// ─── Validate input ───
$url  = $_POST['url']  ?? '';
$name = $_POST['name'] ?? '';
$category = $_POST['category'] ?? 'other';

if (!$url) {
    sse('error', ['message' => 'No URL provided.']);
    exit;
}

// Sanitize filename
$name = preg_replace('/[^\w\s\-\.]+/u', '', $name);
$name = preg_replace('/\s+/', '_', trim($name));
$name = mb_substr($name, 0, 120);
if (!$name) $name = 'download_' . time();

// Resolve save directory
$saveDir = __DIR__ . '/../downloads';
if (!is_dir($saveDir)) mkdir($saveDir, 0755, true);

$filePath = $saveDir . '/' . $name;

sse('start', [
    'url'  => $url,
    'name' => $name,
]);

// ─── HEAD request to get file size ───
$headCh = curl_init($url);
curl_setopt_array($headCh, [
    CURLOPT_NOBODY         => true,
    CURLOPT_HEADER         => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => 'PakuaOS/1.0',
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT        => 15,
]);
curl_exec($headCh);
$totalSize = (int)curl_getinfo($headCh, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
$httpCode  = curl_getinfo($headCh, CURLINFO_HTTP_CODE);
curl_close($headCh);

sse('info', [
    'total_size'  => $totalSize,
    'total_human' => $totalSize > 0 ? formatBytes($totalSize) : 'Unknown',
    'http_code'   => $httpCode,
]);

// ─── Download ───
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $url,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_USERAGENT      => 'PakuaOS/1.0',
    CURLOPT_BINARYTRANSFER => true,
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_CONNECTTIMEOUT => 30,
    CURLOPT_TIMEOUT        => 0,
]);

$fp = fopen($filePath . '.part', 'wb');
$startTime = microtime(true);
$lastEmit = 0;
$lastBytes = 0;
$speedHistory = [];

curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function (
    $resource, $dlNow, $dlTotal, $ulNow, $ulTotal
) use ($fp, &$startTime, &$lastEmit, &$lastBytes, &$speedHistory, $totalSize) {

    $now = microtime(true);
    // Throttle to 200ms updates
    if ($now - $lastEmit < 0.20) return 0;

    $elapsed = $now - $startTime;
    $lastEmit = $now;

    // Calculate speed (smoothed)
    $instantSpeed = $elapsed > 0 ? $dlNow / $elapsed : 0;
    $speedHistory[] = $instantSpeed;
    if (count($speedHistory) > 8) array_shift($speedHistory);
    $avgSpeed = array_sum($speedHistory) / count($speedHistory);

    // Calculate percentage
    $effectiveTotal = $totalSize > 0 ? $totalSize : $dlTotal;
    $pct = $effectiveTotal > 0 ? min(($dlNow / $effectiveTotal) * 100, 100) : 0;

    // ETA
    $eta = '∞';
    if ($avgSpeed > 0 && $effectiveTotal > 0 && $dlNow < $effectiveTotal) {
        $remaining = $effectiveTotal - $dlNow;
        $sec = (int)ceil($remaining / $avgSpeed);
        $eta = formatTime($sec);
    }

    $bytesSinceLast = $dlNow - $lastBytes;
    $lastBytes = $dlNow;

    sse('progress', [
        'downloaded'    => (int)$dlNow,
        'total'         => (int)$effectiveTotal,
        'downloaded_human' => formatBytes((int)$dlNow),
        'total_human'      => formatBytes((int)$effectiveTotal),
        'pct'           => round($pct, 1),
        'speed'         => (int)$avgSpeed,
        'speed_human'   => formatBytes((int)$avgSpeed) . '/s',
        'eta'           => $eta,
        'elapsed'       => round($elapsed, 1),
        'elapsed_human' => formatTime((int)$elapsed),
        'chunk'         => (int)$bytesSinceLast,
    ]);

    return 0;
});

curl_setopt($ch, CURLOPT_FILE, $fp);
$success = curl_exec($ch);
$error   = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
curl_close($ch);
fclose($fp);

$finalTime = microtime(true) - $startTime;

// ─── Check result ───
if (!$success || ($httpCode >= 400 && $httpCode !== 206 && $httpCode !== 0)) {
    @unlink($filePath . '.part');
    sse('error', [
        'message'   => $error ?: "HTTP error {$httpCode}",
        'http_code' => $httpCode,
    ]);
    exit;
}

// Rename .part → final
rename($filePath . '.part', $filePath);
$finalSize = filesize($filePath);
$avgSpeed = $finalTime > 0 ? $finalSize / $finalTime : 0;

// Save to history
$historyFile = __DIR__ . '/history.json';
$history = [];
if (file_exists($historyFile)) {
    $history = json_decode(file_get_contents($historyFile), true) ?? [];
}
$history[] = [
    'id'         => count($history) + 1,
    'name'       => $name,
    'url'        => $url,
    'file_path'  => 'downloads/' . $name,
    'file_size'  => $finalSize,
    'size_human' => formatBytes($finalSize),
    'speed_avg'  => formatBytes((int)$avgSpeed) . '/s',
    'time'       => formatTime((int)$finalTime),
    'status'     => 'completed',
    'created_at' => date('Y-m-d H:i:s'),
];
// Keep last 50
if (count($history) > 50) $history = array_slice($history, -50);
file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT));

sse('complete', [
    'name'        => $name,
    'file_path'   => 'downloads/' . $name,
    'file_size'   => $finalSize,
    'size_human'  => formatBytes($finalSize),
    'speed_avg'   => formatBytes((int)$avgSpeed) . '/s',
    'time'        => formatTime((int)$finalTime),
    'time_sec'    => round($finalTime, 1),
]);
