<?php
require_once 'db.php';
header('Content-Type: application/json');
$db = getDB();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['fecha'], $data['titulo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos obligatorios']);
    exit;
}

if (isset($data['id'])) {
    // Actualizar día existente
    $stmt = $db->prepare("UPDATE dias SET fecha=?, titulo=?, portada=?, texto=?, video=? WHERE id=?");
    $stmt->execute([
        $data['fecha'],
        $data['titulo'],
        $data['portada'] ?? null,
        $data['texto'] ?? null,
        $data['video'] ?? null,
        $data['id']
    ]);
} else {
    // Nuevo día
    $stmt = $db->prepare("INSERT INTO dias (fecha, titulo, portada, texto, video) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['fecha'],
        $data['titulo'],
        $data['portada'] ?? null,
        $data['texto'] ?? null,
        $data['video'] ?? null
    ]);
}
echo json_encode(['ok' => true]);
