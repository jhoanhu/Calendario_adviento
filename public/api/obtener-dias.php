<?php
require_once 'db.php';
header('Content-Type: application/json');
$db = getDB();

$stmt = $db->query("SELECT * FROM dias ORDER BY fecha ASC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
