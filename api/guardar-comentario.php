<?php
require_once 'db.php';
header('Content-Type: application/json');
$db = getDB();

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id_dia'], $data['comentario'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

$stmt = $db->prepare("INSERT INTO comentarios (id_dia, nombre, comentario) VALUES (?, ?, ?)");
$stmt->execute([
    $data['id_dia'],
    $data['nombre'] ?? null,
    $data['comentario']
]);
echo json_encode(['ok' => true]);
