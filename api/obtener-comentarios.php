<?php
require_once 'db.php';
$db = getDB();
header('Content-Type: application/json');

$id_dia = isset($_GET['id_dia']) ? intval($_GET['id_dia']) : 0;

if ($id_dia > 0) {
    $stmt = $db->prepare("
        SELECT c.*, d.fecha AS dia_fecha, d.titulo AS dia_titulo, d.portada AS dia_portada
        FROM comentarios c
        LEFT JOIN dias d ON c.id_dia = d.id
        WHERE c.id_dia = ?
        ORDER BY c.fecha_creacion DESC
    ");
    $stmt->execute([$id_dia]);
} else {
    $stmt = $db->query("
        SELECT c.*, d.fecha AS dia_fecha, d.titulo AS dia_titulo, d.portada AS dia_portada, d.texto AS dia_texto
FROM comentarios c
LEFT JOIN dias d ON c.id_dia = d.id
ORDER BY c.fecha_creacion DESC

    ");
}
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($comentarios);
