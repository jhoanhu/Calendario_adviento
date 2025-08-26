<?php
require_once 'db.php'; // Usa tu getDB() aquí si lo tienes

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
$db = getDB();
$stmt = $db->prepare("DELETE FROM comentarios WHERE id = ?");
$res = $stmt->execute([$id]);
echo json_encode(['ok' => $res]);
