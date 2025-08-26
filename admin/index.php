<?php
session_start();
require_once '../api/db.php';
$db = getDB();

// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $stmt = $db->prepare("SELECT * FROM usuarios_admin WHERE username=? AND password=?");
    $stmt->execute([$u, $p]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $_SESSION['admin'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}

// REQUIERE LOGIN
if (!isset($_SESSION['admin'])):
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Ingresar</title>
    <style>
    body {
        background: linear-gradient(135deg, #b2f7ef 0%, #bae1ff 100%);
        min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    .login-box {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 8px 32px 0 rgba(82, 181, 219, 0.18);
        padding: 32px 24px;
        width: 320px;
        max-width: 95vw;
        text-align: center;
    }
    .login-box h2 {
        margin-bottom: 12px;
        color: #2697a6;
    }
    .login-box input[type="text"],
    .login-box input[type="password"] {
        width: 90%;
        padding: 10px 12px;
        margin: 12px 0 18px 0;
        border: 1.5px solid #c5eefa;
        border-radius: 12px;
        outline: none;
        font-size: 1.08em;
        background: #f5fcff;
        transition: border 0.2s;
    }
    .login-box input:focus { border-color: #63b3ed; }
    .login-box button {
        padding: 10px 26px;
        background: linear-gradient(90deg, #82eefd 0%, #71f2c8 100%);
        border: none;
        border-radius: 999px;
        color: #197388;
        font-weight: bold;
        font-size: 1.13em;
        cursor: pointer;
        margin-top: 10px;
        box-shadow: 0 2px 8px 0 rgba(82,181,219,0.13);
        transition: background 0.2s;
    }
    .login-box button:hover {
        background: linear-gradient(90deg, #59d0fa 0%, #36dfa6 100%);
        color: #13697a;
    }
    .login-box p { color: #d00; margin-bottom: 0.7em; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Panel Admin</h2>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <form method="post" autocomplete="off">
            <input type="text" name="username" placeholder="Usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;

// DASHBOARD
$stmt = $db->query("SELECT * FROM dias ORDER BY fecha ASC");
$dias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Días</title>
    <style>
body {
    background: linear-gradient(135deg, #bae1ff 0%, #b2f7ef 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    padding: 0;
}
.header-admin {
    background: #2697a6;
    color: #fff;
    padding: 16px 0 12px 0;
    margin-bottom: 30px;
    text-align: center;
    font-size: 1.7em;
    border-radius: 0 0 22px 22px;
    box-shadow: 0 4px 18px 0 rgba(43,160,205,0.10);
    letter-spacing: 0.05em;
}
.dashboard-box {
    background: #fff;
    border-radius: 18px;
    margin: 0 auto 30px auto;
    max-width: 650px;
    padding: 28px 20px;
    box-shadow: 0 8px 28px 0 rgba(100,199,220,0.10);
}
.admin-actions {
    margin-bottom: 22px;
}
.admin-actions a {
    background: #f2fbfa;
    color: #2697a6;
    padding: 7px 22px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 2px 9px 0 rgba(43,160,205,0.06);
    margin-right: 10px;
    transition: background 0.18s;
}
.admin-actions a:hover {
    background: #b2f7ef;
    color: #167276;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: #f4fbfd;
    border-radius: 12px;
    overflow: hidden;
}
th, td {
    padding: 10px 5px;
    text-align: left;
}
th {
    background: #ebfafd;
    color: #2697a6;
    font-weight: 600;
}
tr:nth-child(even) { background: #f6fffc; }
tr:nth-child(odd) { background: #e8f9fd; }
td:last-child a {
    background: #bae1ff;
    padding: 5px 18px;
    border-radius: 8px;
    text-decoration: none;
    color: #1b5566;
    font-weight: 500;
    transition: background 0.18s;
}
td:last-child a:hover { background: #82eefd; }
.eliminar-dia {
    background: #f55;
    color: #fff;
    border: none;
    padding: 5px 13px;
    border-radius: 7px;
    margin-left: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.17s;
}
.eliminar-dia:hover { background: #d00; }
@media (max-width: 750px) {
    .dashboard-box { padding: 10px 4vw; }
    th, td { padding: 6px 2vw; font-size: 1em; }
}
    </style>
</head>
<body>
    <div class="header-admin">Panel Admin: Días Registrados</div>
    <div class="dashboard-box">
        <div class="admin-actions">
            <a href="nuevo-dia.php">➕ Nuevo día</a>
            <a href="ver-respuestas.php">📋 Ver respuestas</a>
            <a href="logout.php">Cerrar sesión</a>
        </div>
        <table>
        <tr><th>Fecha</th><th>Título</th><th>Acciones</th></tr>
        <?php foreach($dias as $d): ?>
        <tr data-id="<?= $d['id'] ?>">
            <td><?=htmlspecialchars($d['fecha'])?></td>
            <td><?=htmlspecialchars($d['titulo'])?></td>
            <td>
                <a href="nuevo-dia.php?id=<?=$d['id']?>">Editar</a>
                <button class="eliminar-dia" onclick="eliminarDia(<?= $d['id'] ?>, this)">Eliminar</button>
            </td>
        </tr>
        <?php endforeach; ?>
        </table>
    </div>
    <script>
    function eliminarDia(id, btn) {
        if (!confirm("¿Seguro que deseas eliminar este día? No se podrá recuperar.")) return;
        fetch("../api/eliminar-dia.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        })
        .then(r => r.json())
        .then(resp => {
            if (resp.ok) {
                // Elimina la fila de la tabla en el DOM
                const fila = btn.closest('tr');
                if (fila) fila.remove();
                alert("¡Día eliminado!");
            } else {
                alert("No se pudo eliminar el día.");
            }
        });
    }
    </script>
</body>
</html>

