<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}
require_once '../api/db.php';
$db = getDB();

$modo = "Nuevo";
$id = $_GET['id'] ?? null;
$dia = null;

// Si hay id, cargamos el día para editar
if ($id) {
    $stmt = $db->prepare("SELECT * FROM dias WHERE id=?");
    $stmt->execute([$id]);
    $dia = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($dia) $modo = "Editar";
}

// Manejo de guardado POST (con archivos)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];
    $idEdit = $_POST['id'] ?? null;

    // Procesar subida de portada
    $portadaRuta = $dia['portada'] ?? null;
    if (isset($_FILES['portada']) && $_FILES['portada']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['portada']['name'], PATHINFO_EXTENSION);
        $fileName = 'portada_' . $fecha . '_' . uniqid() . '.' . $ext;
        $destino = '../uploads/portadas/' . $fileName;
        if (move_uploaded_file($_FILES['portada']['tmp_name'], $destino)) {
            $portadaRuta = 'uploads/portadas/' . $fileName;
        }
    }

    // Procesar subida de video
    $videoRuta = $dia['video'] ?? null;
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
        $fileName = 'video_' . $fecha . '_' . uniqid() . '.' . $ext;
        $destino = '../uploads/videos/' . $fileName;
        if (move_uploaded_file($_FILES['video']['tmp_name'], $destino)) {
            $videoRuta = 'uploads/videos/' . $fileName;
        }
    }

    if ($idEdit) {
        // Editar día
        $stmt = $db->prepare("UPDATE dias SET fecha=?, titulo=?, portada=?, texto=?, video=? WHERE id=?");
        $stmt->execute([$fecha, $titulo, $portadaRuta, $texto, $videoRuta, $idEdit]);
    } else {
        // Nuevo día
        $stmt = $db->prepare("INSERT INTO dias (fecha, titulo, portada, texto, video) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fecha, $titulo, $portadaRuta, $texto, $videoRuta]);
    }
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - <?=$modo?> Día</title>
    <style>
body {
    background: linear-gradient(135deg, #bae1ff 0%, #b2f7ef 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0; padding: 0;
}
.form-box {
    background: #fff;
    border-radius: 20px;
    margin: 40px auto 0 auto;
    max-width: 460px;
    padding: 36px 30px 24px 30px;
    box-shadow: 0 8px 28px 0 rgba(100,199,220,0.13);
}
.form-box h2 {
    color: #2697a6; margin-bottom: 0.5em;
}
label { display: block; margin-top: 20px; color: #197388; font-weight: 500;}
input[type="text"], input[type="date"], textarea {
    width: 97%;
    padding: 9px 10px;
    margin: 8px 0 0 0;
    border: 1.5px solid #c5eefa;
    border-radius: 10px;
    outline: none;
    background: #f5fcff;
    font-size: 1.07em;
    transition: border 0.18s;
}
input:focus, textarea:focus { border-color: #63b3ed; }
textarea { min-height: 70px; resize: vertical; font-size: 1.08em; }
input[type="file"] { margin: 8px 0 0 0;}
input[type="submit"] {
    margin-top: 28px;
    padding: 12px 32px;
    background: linear-gradient(90deg, #82eefd 0%, #71f2c8 100%);
    border: none;
    border-radius: 999px;
    color: #197388;
    font-weight: bold;
    font-size: 1.13em;
    cursor: pointer;
    box-shadow: 0 2px 8px 0 rgba(82,181,219,0.13);
    transition: background 0.2s;
}
input[type="submit"]:hover {
    background: linear-gradient(90deg, #59d0fa 0%, #36dfa6 100%);
    color: #13697a;
}
.volver-link {
    color: #2697a6; background: #f2fbfa; padding: 5px 20px; border-radius: 999px;
    text-decoration: none; font-size: 1.03em; margin-bottom: 22px; display: inline-block;
}
@media (max-width: 650px) {
    .form-box { padding: 12px 3vw 16px 3vw; }
}
    </style>
</head>
<body>
    <div class="form-box">
        <a class="volver-link" href="index.php">&larr; Volver al panel</a>
        <h2><?=$modo?> Día Especial</h2>
        <form method="post" enctype="multipart/form-data">
            <?php if ($id): ?>
                <input type="hidden" name="id" value="<?=htmlspecialchars($id)?>">
            <?php endif; ?>
            <label>Fecha del día *</label>
            <input type="date" name="fecha" value="<?=htmlspecialchars($dia['fecha']??'')?>" required>
            
            <label>Título *</label>
            <input type="text" name="titulo" value="<?=htmlspecialchars($dia['titulo']??'')?>" required>
            
            <label>Portada (imagen) <small style="font-weight:normal;">(opcional)</small></label>
            <input type="file" name="portada" accept="image/*">
            <?php if (!empty($dia['portada'])): ?>
                <div style="margin-top:10px;">
                    <b>Actual:</b><br>
                    <img src="../<?=htmlspecialchars($dia['portada'])?>" alt="Portada" style="max-width:140px;max-height:90px;border-radius:12px;">
                </div>
            <?php endif; ?>
            
            <label>Texto del día <small style="font-weight:normal;">(opcional)</small></label>
            <textarea name="texto" placeholder="Escribe aquí el mensaje o reflexión..."><?=htmlspecialchars($dia['texto']??'')?></textarea>
            
            <label>Vídeo <small style="font-weight:normal;">(opcional, mp4/webm/mov)</small></label>
            <input type="file" name="video" accept="video/*">
            <?php if (!empty($dia['video'])): ?>
                <div style="margin-top:10px;">
                    <b>Actual:</b><br>
                    <video src="../<?=htmlspecialchars($dia['video'])?>" controls style="max-width:140px;max-height:90px;border-radius:12px;"></video>
                </div>
            <?php endif; ?>
            
            <input type="submit" value="Guardar">
        </form>
    </div>
</body>
</html>
