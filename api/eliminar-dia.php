<?php
require_once __DIR__ . '/db.php';
$db = getDB();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

// Elimina archivos asociados (portada, video) si existen
$stmt = $db->prepare("SELECT portada, video FROM dias WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    if ($row['portada'] && file_exists("../" . $row['portada'])) {
        @unlink("../" . $row['portada']);
    }
    if ($row['video'] && file_exists("../" . $row['video'])) {
        @unlink("../" . $row['video']);
    }
}

$stmt = $db->prepare("DELETE FROM dias WHERE id = ?");
$res = $stmt->execute([$id]);

echo json_encode(['ok' => $res]);
