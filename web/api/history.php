<?php
/**
 * PakuaOS Web — Download History API
 * GET /api/history.php?action=list    → returns JSON history
 * GET /api/history.php?action=delete&id=1 → removes entry
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$historyFile = __DIR__ . '/history.json';
$action = $_GET['action'] ?? 'list';

function loadHistory(): array {
    global $historyFile;
    if (!file_exists($historyFile)) return [];
    return json_decode(file_get_contents($historyFile), true) ?? [];
}

function saveHistory(array $data): void {
    global $historyFile;
    file_put_contents($historyFile, json_encode($data, JSON_PRETTY_PRINT));
}

switch ($action) {
    case 'list':
        $history = loadHistory();
        usort($history, fn($a, $b) => ($b['created_at'] ?? '') <=> ($a['created_at'] ?? ''));
        echo json_encode(['ok' => true, 'data' => $history]);
        break;

    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        $history = loadHistory();
        $history = array_values(array_filter($history, fn($d) => ($d['id'] ?? 0) !== $id));
        saveHistory($history);
        echo json_encode(['ok' => true]);
        break;

    case 'clear':
        saveHistory([]);
        echo json_encode(['ok' => true]);
        break;

    default:
        echo json_encode(['ok' => false, 'error' => 'Unknown action']);
        break;
}
