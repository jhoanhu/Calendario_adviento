<?php
require_once 'db.php';
$db = getDB();

$db->exec("
CREATE TABLE IF NOT EXISTS dias (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    fecha TEXT NOT NULL,
    titulo TEXT NOT NULL,
    portada TEXT,
    texto TEXT,
    video TEXT
);

CREATE TABLE IF NOT EXISTS comentarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_dia INTEGER NOT NULL,
    fecha_creacion TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    nombre TEXT,
    comentario TEXT NOT NULL,
    FOREIGN KEY (id_dia) REFERENCES dias(id)
);

CREATE TABLE IF NOT EXISTS usuarios_admin (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
);
");

echo "Base de datos creada correctamente.\n";
