<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Admin - Respuestas</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
  <style>
    body {
      background: linear-gradient(135deg, #b2f7ef 0%, #bae1ff 100%);
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
      display: flex;
      justify-content: flex-start;
      align-items: center;
      gap: 14px;
    }
    .admin-actions a {
      background: #f2fbfa;
      color: #2697a6;
      padding: 7px 22px;
      border-radius: 999px;
      text-decoration: none;
      font-weight: bold;
      box-shadow: 0 2px 9px 0 rgba(43,160,205,0.06);
      transition: background 0.18s;
      display: flex; align-items: center; gap: 8px;
    }
    .admin-actions a:hover {
      background: #b2f7ef;
      color: #167276;
    }
    .cards-respuestas {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .card-respuesta {
      display: flex;
      align-items: center;
      background: #f4fbfd;
      border-radius: 11px;
      box-shadow: 0 2px 12px #b2e6ef22;
      padding: 10px 16px;
      position: relative;
      transition: box-shadow .16s, transform .14s;
      cursor: pointer;
      border: 1.3px solid #c1e9f8;
      min-height: 60px;
    }
    .card-respuesta:hover {
      box-shadow: 0 6px 16px #b2e6ef88;
      background: #e0faf8;
      transform: translateY(-2px) scale(1.015);
      border-color: #79d2e4;
    }
    .card-respuesta .portada-mini {
      height: 50px;
      border-radius: 7px;
      margin-right: 12px;
      box-shadow: 0 2px 7px #b2e6ef55;
      vertical-align: middle;
      background: #e1f5fa;
    }
    .card-respuesta .no-portada {
      width: 50px; height: 50px;
      background: #e1f5fa;
      border-radius: 7px;
      margin-right: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2em;
      color: #70b5c6bb;
    }
    .card-respuesta .comentario {
      font-size: 1.11em;
      color: #1c354a;
      font-weight: bold;
      margin-right: 18px;
      flex: 1;
      word-break: break-word;
    }
    .card-respuesta .info {
      display: flex;
      flex-direction: column;
      margin-right: 18px;
      gap: 2px;
    }
    .card-respuesta .nombre {
      font-size: 0.97em;
      color: #23869e;
      font-weight: 500;
    }
    .card-respuesta .fecha {
      font-size: 0.98em;
      color: #2697a6cc;
    }
    .eliminar-btn {
      background: #fd4c4c;
      color: #fff;
      border: none;
      padding: 6px 17px;
      border-radius: 999px;
      font-weight: bold;
      font-size: 1em;
      cursor: pointer;
      box-shadow: 0 2px 7px #f7b5b5a6;
      transition: background .14s;
      margin-left: 10px;
    }
    .eliminar-btn:hover { background: #c72323; }
    #detalle-modal {
      display: none; position: fixed; z-index: 1001; top: 0; left: 0; width: 100vw; height: 100vh;
      background: rgba(20,50,60,0.23); align-items: center; justify-content: center;
    }
    #detalle-modal .modal-content {
      background: #fff;
      border-radius: 20px;
      padding: 34px 26px 23px 26px;
      min-width: 340px;
      max-width: 97vw;
      box-shadow: 0 5px 25px #2fc8f52f;
      position: relative;
      animation: abrirModal .19s cubic-bezier(.64,.03,.22,.93);
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    @keyframes abrirModal {
      from { opacity: 0; transform: scale(0.93);}
      to   { opacity: 1; transform: scale(1);}
    }
    #detalle-modal .cerrar-modal {
      position: absolute; top: 13px; right: 18px;
      color: #197388; font-size: 1.4em; cursor: pointer;
      font-weight: bold; transition: color .13s;
    }
    #detalle-modal .cerrar-modal:hover { color: #f33; }
    #modal-portada img {
      max-width: 320px; max-height: 180px; border-radius: 13px;
      box-shadow: 0 2px 7px #3cc9ed44; margin-bottom: 14px;
    }
    #modal-titulo {
      font-size: 1.25em; font-weight: bold; color: #2697a6; margin-bottom: 2px; text-align: center;
    }
    #modal-fecha {
      color:#197388;font-size:1.07em;margin-bottom:13px; text-align: center;
    }
    #modal-texto-dia {
      color: #555; margin-bottom: 13px; font-size:1.11em; text-align: center;
    }
    #modal-respuesta {
      color:#1c354a; font-size:1.19em; border-top: 1px solid #c8e2ef; margin-top: 17px; padding-top: 9px;
      text-align: center;
      font-style: italic;
    }
    #no-resp { text-align:center;margin:15px 0;color:#999;}
    @media (max-width: 750px) {
      .dashboard-box { padding: 10px 4vw; }
    }
  </style>
</head>
<body>
  <div class="header-admin">Panel Admin: Respuestas de los usuarios</div>
  <div class="dashboard-box">
    <div class="admin-actions">
      <a href="index.php"><i class="ri-arrow-left-s-line"></i> Días</a>
      <a href="logout.php"><i class="ri-logout-circle-r-line"></i> Cerrar sesión</a>
    </div>
    <div class="cards-respuestas" id="cards-respuestas"></div>
    <div id="no-resp" style="display:none;">No hay respuestas registradas.</div>
  </div>
  <div id="detalle-modal" onclick="if(event.target===this)cerrarModalDetalle()">
    <div class="modal-content">
      <span class="cerrar-modal" onclick="cerrarModalDetalle()">&times;</span>
      <div id="modal-portada"></div>
      <div id="modal-titulo"></div>
      <div id="modal-fecha"></div>
      <div id="modal-texto-dia"></div>
      <div id="modal-respuesta"></div>
    </div>
  </div>
  <script>
    let comentarios = [];
    function cargarRespuestas() {
      fetch("../api/obtener-comentarios.php")
        .then(r => r.json())
        .then(resps => {
          comentarios = resps;
          mostrarCards(resps);
        });
    }
    function mostrarCards(respuestas) {
      const container = document.getElementById("cards-respuestas");
      container.innerHTML = "";
      if (!respuestas.length) {
        document.getElementById('no-resp').style.display = "block";
        return;
      }
      document.getElementById('no-resp').style.display = "none";
      respuestas.forEach(r => {
        const card = document.createElement("div");
        card.className = "card-respuesta";
        card.innerHTML = `
          ${
            r.dia_portada
            ? `<img class="portada-mini" src="../${r.dia_portada}" alt="Portada">`
            : `<span class="no-portada"><i class="ri-chat-3-line"></i></span>`
          }
          <span class="comentario">${r.comentario}</span>
          <div class="info">
            <span class="nombre">${r.nombre ? r.nombre : ''}</span>
            <span class="fecha">${r.fecha_creacion}</span>
          </div>
          <button class="eliminar-btn" onclick="eliminarRespuesta(${r.id}, this);event.stopPropagation();"><i class="ri-delete-bin-7-line"></i> Eliminar</button>
        `;
        card.onclick = () => mostrarDetalle(r);
        container.appendChild(card);
      });
    }
    function mostrarDetalle(r) {
      document.getElementById("modal-portada").innerHTML = r.dia_portada ? `<img src="../${r.dia_portada}" alt="Portada">` : '';
      document.getElementById("modal-titulo").innerText = r.dia_titulo ? `🎁 ${r.dia_titulo}` : "";
      document.getElementById("modal-fecha").innerText = r.dia_fecha ? `Fecha: ${r.dia_fecha}` : "";
      document.getElementById("modal-texto-dia").innerText = r.dia_texto || "";
      document.getElementById("modal-respuesta").innerText = r.comentario || "";
      document.getElementById("detalle-modal").style.display = "flex";
    }
    function cerrarModalDetalle() {
      document.getElementById("detalle-modal").style.display = "none";
    }
    function eliminarRespuesta(id, btn) {
      if (!confirm("¿Seguro que deseas eliminar esta respuesta?")) return;
      fetch("../api/eliminar-respuesta.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ id })
      })
      .then(r => r.json())
      .then(resp => {
        if (resp.ok) {
          btn.closest('.card-respuesta').remove();
        } else {
          alert("No se pudo eliminar.");
        }
      });
    }
    cargarRespuestas();
  </script>
</body>
</html>




