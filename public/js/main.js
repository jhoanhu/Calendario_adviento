const apiURL = "../api/obtener-dias.php";
const diasGrid = document.getElementById("dias-grid");
const modal = document.getElementById("modal");
const modalContent = document.getElementById("modal-content");
const searchInput = document.getElementById("search");
const previewMode = document.getElementById("preview-mode");

let dias = [];

fetch(apiURL)
  .then((r) => r.json())
  .then((data) => {
    dias = data;
    renderDias();
  });

function renderDias() {
  diasGrid.innerHTML = "";
  const q = (searchInput.value || "").trim().toLowerCase();

  dias
    .filter(
      (dia) =>
        !q ||
        (dia.titulo && dia.titulo.toLowerCase().includes(q)) ||
        (dia.fecha && dia.fecha.toLowerCase().includes(q))
    )
    .forEach((dia) => {
      const hoy = new Date();
      const diaFecha = new Date(dia.fecha);
      let unlocked = previewMode.checked || hoy >= diaFecha;
      // Card
      const card = document.createElement("div");
      card.className = "dia-card" + (unlocked ? "" : " locked");
      // Portada
      if (dia.portada) {
        const img = document.createElement("img");
        img.src = "../" + dia.portada.replace(/^\/+/, "");
        img.alt = "Portada";
        img.className = "card-bg";
        card.appendChild(img);
      }
      // Overlay
      const overlay = document.createElement("div");
      overlay.className = "card-overlay" + (unlocked ? "" : " locked");
      card.appendChild(overlay);

      // Badge "Bloqueado" arriba izquierda (fuera del contentBox)
      if (!unlocked) {
        const lbl = document.createElement("div");
        lbl.className = "locked-label";
        lbl.innerText = "Bloqueado";
        card.appendChild(lbl);
      }

      // Content box (solo emoji + info-chip, centrado)
      const contentBox = document.createElement("div");
      contentBox.className = "card-content-box";
      contentBox.innerHTML = `
        <div class="card-emoji">🎁</div>
        <div class="info-chip">
          <span class="card-fecha">${dia.fecha}</span>
          <span class="card-titulo">${dia.titulo}</span>
        </div>
      `;
      card.appendChild(contentBox);

      if (unlocked) {
        card.onclick = () => mostrarModalDia(dia);
      }
      diasGrid.appendChild(card);
    });
}

searchInput.oninput = renderDias;
previewMode.onchange = renderDias;

window.closeModal = function () {
  modal.style.display = "none";
  modalContent.innerHTML = "";
};

function mostrarModalDia(dia) {
  let html = `
    <div style="text-align:right;">
      <button onclick="closeModal()" style="font-size:1.4em;background:none;border:none;cursor:pointer;">✕</button>
    </div>
    <h2>${dia.titulo}</h2>
    <div class="modal-fecha">${dia.fecha}</div>
    ${
      dia.portada
        ? `<img src="../${dia.portada.replace(/^\/+/, "")}" alt="Portada">`
        : ""
    }
    ${
      dia.video
        ? `<video src="../${dia.video.replace(
            /^\/+/,
            ""
          )}" controls style="width:100%;max-width:98%;max-height:340px;min-height:120px;border-radius:13px;object-fit:contain;background:#eaf5fa;display:block;margin-bottom:20px;" allowfullscreen></video>`
        : ""
    }
    ${
      dia.texto
        ? `<p style="font-size:1.10em;color:#173a55;margin-top:10px;">${dia.texto.replace(
            /\n/g,
            "<br>"
          )}</p>`
        : ""
    }
    <form id="form-respuesta">
      <label for="comentario">Tu respuesta o mensaje:</label><br>
      <textarea id="comentario" rows="3" required></textarea><br>
      <input type="hidden" id="id-dia" value="${dia.id}">
      <button type="submit">Enviar</button>
    </form>
    <div id="respuestas-list" style="margin-top:24px;"></div>
  `;
  modalContent.innerHTML = html;
  modal.style.display = "flex";

  // Reinicia animación
  modalContent.classList.remove("modal-content");
  void modalContent.offsetWidth;
  modalContent.classList.add("modal-content");
  cargarRespuestas(dia.id);

  // Manejo envío respuesta
  document.getElementById("form-respuesta").onsubmit = function (e) {
    e.preventDefault();
    const comentario = document.getElementById("comentario").value.trim();
    if (!comentario) return;
    fetch("../api/guardar-comentario.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id_dia: dia.id,
        nombre: "",
        comentario,
      }),
    })
      .then((r) => r.json())
      .then((resp) => {
        document.getElementById("comentario").value = "";
        cargarRespuestas(dia.id);
      });
  };
}

function cargarRespuestas(idDia) {
  fetch(`../api/obtener-comentarios.php?id_dia=${idDia}`)
    .then((r) => r.json())
    .then((list) => {
      const div = document.getElementById("respuestas-list");
      if (!div) return;
      if (!list.length) {
        div.innerHTML = "<em>Aún no hay respuestas.</em>";
        return;
      }
      div.innerHTML =
        "<b>Respuestas anteriores:</b><ul style='padding-left:18px;'>";
      list.forEach((com) => {
        div.innerHTML += `<li style="margin-bottom:10px;"><span style="color:#346">${
          com.nombre ? `<b>${com.nombre}</b>: ` : ""
        }</span>${com.comentario}</li>`;
      });
      div.innerHTML += "</ul>";
    });
}
