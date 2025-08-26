<?php
require_once 'db.php';
$db = getDB();

// Cambia estos valores a tu gusto
$username = 'admin';
$password = '1234';

$stmt = $db->prepare("INSERT INTO usuarios_admin (username, password) VALUES (?, ?)");
$stmt->execute([$username, $password]);

echo "Usuario admin creado correctamente\n";
