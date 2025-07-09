// ======================
// Variáveis globais
let chamadosAbertos = [];
let chamadosConcluidos = [];
let logAtividades = [];
let notasChamados = {};
let chamadoNotasIndex = null;
let indexChamadoAConcluir = null;
let chamadoNotaIndexEditando = null;

// --- LocalStorage Integration ---
function loadChamadosFromLocalStorage() {
  const abertos = localStorage.getItem('chamadosAbertos');
  const concluidos = localStorage.getItem('chamadosConcluidos');
  const notas = localStorage.getItem('notasChamados');
  const logs = localStorage.getItem('logAtividades');
  if (abertos) chamadosAbertos = JSON.parse(abertos);
  if (concluidos) chamadosConcluidos = JSON.parse(concluidos);
  if (notas) notasChamados = JSON.parse(notas);
  if (logs) logAtividades = JSON.parse(logs);
}

function saveChamadosToLocalStorage() {
  localStorage.setItem('chamadosAbertos', JSON.stringify(chamadosAbertos));
  localStorage.setItem('chamadosConcluidos', JSON.stringify(chamadosConcluidos));
  localStorage.setItem('notasChamados', JSON.stringify(notasChamados));
  localStorage.setItem('logAtividades', JSON.stringify(logAtividades));
}

// Simula novo chamado
function simulateNewRequest() {
  const titulo = document.getElementById("test-titulo").value;
  const descricao = document.getElementById("test-descricao").value;
  const prioridade = document.getElementById("test-prioridade").value;

  const agora = new Date();
  const novoChamado = {
    id: Date.now(),
    titulo,
    descricao,
    prioridade,
    data: agora.toLocaleDateString(),
    dataAbertura: agora,
    status: "Aberto",
  };

  chamadosAbertos.push(novoChamado);
  saveChamadosToLocalStorage();
  closeTestModal();
  renderChamados();
  registrarLog('Chamado aberto: ' + titulo);
}

// Fecha o modal
function closeTestModal() {
  document.getElementById("test-modal").style.display = "none";
}

// Abre o modal
function openTestModal() {
  document.getElementById("test-titulo").value = "";
  document.getElementById("test-descricao").value = "";
  document.getElementById("test-prioridade").value = "Alta";
  document.getElementById("test-modal").style.display = "flex";
}

// Renderiza todos os chamados
function renderChamados() {
  const abertosTable = document.getElementById("chamados-abertos").querySelector("tbody");
  const concluidosTable = document.getElementById("chamados-concluidos").querySelector("tbody");

  abertosTable.innerHTML = "";
  concluidosTable.innerHTML = "";

  chamadosAbertos.forEach((chamado, index) => {
    const prioSelect = `
      <select class="priority-select" onchange="mudarPrioridadeChamado(${index}, this.value)">
        <option value="Alta" ${chamado.prioridade==="Alta"?"selected":""}>Alta</option>
        <option value="Média" ${chamado.prioridade==="Média"?"selected":""}>Média</option>
        <option value="Baixa" ${chamado.prioridade==="Baixa"?"selected":""}>Baixa</option>
      </select>
    `;
    const row = document.createElement("tr");
    row.innerHTML = `
        <td>${chamado.titulo}</td>
        <td>${chamado.descricao}</td>
        <td>${chamado.data}</td>
        <td>${prioSelect}</td>
        <td>
<button onclick="verNotaChamado(${index})" class="btn-view-nota" title="Ver Nota">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="vertical-align:middle">
    <circle cx="11" cy="11" r="7" stroke="#4CAF50" stroke-width="2"/>
    <line x1="16.5" y1="16.5" x2="22" y2="22" stroke="#4CAF50" stroke-width="2" stroke-linecap="round"/>
  </svg>
</button>
        </td>
        <td class="acoes-botoes">
          <button onclick="abrirModalConcluirChamado(${index})" class="btn-edit">Concluir</button>
          <button onclick="exportarPDFChamadoUnico(${index})" class="btn-edit">PDF</button>
          <button onclick="abrirNotasChamado(${index})" class="btn-edit">Notas</button>
        </td>
    `;
    abertosTable.appendChild(row);
  });

  // Adiciona o CSS só uma vez
  if (!document.getElementById('btn-view-nota-style')) {
    const style = document.createElement('style');
    style.id = 'btn-view-nota-style';
    style.textContent = `
      .btn-view-nota {
        background: transparent;
        border: none;
        padding: 4px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }
      .btn-view-nota:hover {
        background: #f0f0f0;
      }
    `;
    document.head.appendChild(style);
  }

  // Agora renderiza o histórico COM AÇÕES (botão "Ver"!)
  chamadosConcluidos.forEach((chamado, idx) => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${chamado.titulo}</td>
      <td>${chamado.descricao}</td>
      <td>${chamado.data}</td>
      <td>
        <button onclick="verChamadoConcluido(${idx})" class="btn1" style="padding:2px 10px; font-size:13px;">Ver</button>
      </td>
    `;
    concluidosTable.appendChild(row);
  });

  atualizarIndicadores();
  atualizarKPIs();
}

// Notas
function verNotaChamado(index) {
  chamadoNotaIndexEditando = index;
  const chamado = chamadosAbertos[index];
  const nota = notasChamados[chamado.id] || "Sem nota para este chamado.";
  document.getElementById('conteudoNotaChamado').textContent = nota;
  document.getElementById('modalVerNota').classList.add('active');
}

function fecharVerNotaChamado() {
  document.getElementById('modalVerNota').classList.remove('active');
  chamadoNotaIndexEditando = null;
}

function mudarPrioridadeChamado(index, newValue) {
  chamadosAbertos[index].prioridade = newValue;
  saveChamadosToLocalStorage();
  renderChamados();
  registrarLog('Prioridade alterada para: ' + newValue);
}

// Abre modal de editar nota (fecha visualização)
function abrirEditarNotaChamado() {
  if (typeof chamadoNotaIndexEditando === "undefined" || chamadoNotaIndexEditando === null) return;
  const chamado = chamadosAbertos[chamadoNotaIndexEditando];
  const erroEl = document.getElementById('erroEditarNotaChamado');
  erroEl.style.display = 'none';
  erroEl.textContent = '';
  const nota = notasChamados[chamado.id] || "";

  if (!nota) {
    erroEl.textContent = 'Nenhuma nota criada para editar.';
    erroEl.style.display = 'block';
    document.getElementById('modalEditarNota').classList.remove('active');
    document.getElementById('modalVerNota').classList.remove('active');
    return;
  }

  document.getElementById('textareaEditarNotaChamado').value = nota;
  document.getElementById('modalVerNota').classList.remove('active');
  document.getElementById('modalEditarNota').classList.add('active');
}
function fecharEditarNotaChamado() {
  document.getElementById('modalEditarNota').classList.remove('active');
  const erroEl = document.getElementById('erroEditarNotaChamado');
  erroEl.style.display = 'none';
  erroEl.textContent = '';
}

function salvarEdicaoNotaChamado() {
  if (chamadoNotaIndexEditando === null) return;
  const nota = document.getElementById('textareaEditarNotaChamado').value.trim();
  const chamado = chamadosAbertos[chamadoNotaIndexEditando];
  notasChamados[chamado.id] = nota;
  saveChamadosToLocalStorage();
  document.getElementById('modalEditarNota').classList.remove('active');
  registrarLog('Nota editada do chamado: ' + chamado.titulo);
  verNotaChamado(chamadoNotaIndexEditando);
}

// Função para exportar apenas 1 chamado (PDF individual)
function exportarPDFChamadoUnico(index) {
  const chamado = chamadosAbertos[index];
  if (!chamado) return;

  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const img = new Image();
  img.crossOrigin = "Anonymous";
  img.src = "https://cdn-icons-png.flaticon.com/512/4149/4149650.png";
  img.onload = function () {
    doc.addImage(img, "PNG", 10, 10, 20, 20);
    doc.setFontSize(18);
    doc.setTextColor(33, 150, 243);
    doc.text("Detalhes do Chamado", 35, 20);

    let y = 40;
    doc.setFillColor(33, 150, 243); // azul
    doc.setTextColor(255);
    doc.rect(10, y, 190, 10, 'F');
    doc.text(`Título: ${chamado.titulo}`, 12, y + 7);
    y += 15;

    doc.setTextColor(0);
    doc.setFontSize(12);
    doc.text(`Descrição: ${chamado.descricao}`, 12, y);
    y += 10;

    doc.text(`Data: ${chamado.data}`, 12, y);
    y += 6;

    if (chamado.prioridade) {
      doc.text(`Prioridade: ${chamado.prioridade}`, 12, y);
      y += 6;
    }

    if (chamado.status) {
      doc.text(`Status: ${chamado.status}`, 12, y);
      y += 6;
    }

    doc.save(`chamado-${chamado.titulo}.pdf`);
  };
  registrarLog('Exportou PDF do chamado: ' + chamado.titulo);
}

function atualizarIndicadores() {
  document.getElementById("totalChamadosAbertos").innerText = chamadosAbertos.length;
  document.getElementById("totalChamadosConcluidos").innerText = chamadosConcluidos.length;
  const altaPrioridade = chamadosAbertos.filter(ch => ch.prioridade === "Alta").length;
  document.getElementById("chamadosAltaPrioridade").innerText = altaPrioridade;
}

// ==========================
// Tela de concluir chamado
function abrirModalConcluirChamado(index) {
  indexChamadoAConcluir = index;
  const chamado = chamadosAbertos[index];
  document.getElementById('tituloChamadoConcluir').textContent = 
    `Chamado: ${chamado.titulo}`;
  document.getElementById('comentarioConclusao').value = "";
  document.getElementById('dificuldadeEncontrada').value = "";
  document.getElementById('urgenciaReal').value = "";
  document.getElementById('erroConclusao').style.display = 'none';

  // Abrir com ambos os métodos para máxima compatibilidade
  const modal = document.getElementById('modalConcluirChamado');
  modal.classList.add('active');
  modal.style.display = 'flex'; // ou 'block', conforme seu CSS
}

function fecharModalConcluirChamado() {
  const modal = document.getElementById('modalConcluirChamado');
  modal.classList.remove('active');
  modal.style.display = 'none';
  indexChamadoAConcluir = null;
}

function confirmarConclusaoChamado() {
  if (indexChamadoAConcluir === null) return;
  const obs = document.getElementById('comentarioConclusao').value.trim();
  const dificuldade = document.getElementById('dificuldadeEncontrada').value;
  const urgenciaReal = document.getElementById('urgenciaReal').value;
  if (!obs) {
    document.getElementById('erroConclusao').textContent = "Por favor, escreva uma observação.";
    document.getElementById('erroConclusao').style.display = 'block';
    return;
  }
  if (!dificuldade || !urgenciaReal) {
    document.getElementById('erroConclusao').textContent = "Preencha todos os campos obrigatórios.";
    document.getElementById('erroConclusao').style.display = 'block';
    return;
  }
  concluirChamado(indexChamadoAConcluir, obs, dificuldade, urgenciaReal);
  fecharModalConcluirChamado();
}

function concluirChamado(index, observacao = "", dificuldade = "", urgenciaReal = "") {
  const chamado = chamadosAbertos.splice(index, 1)[0];
  const agora = new Date();
  chamado.status = "Concluído";
  chamado.dataConclusao = agora;
  chamado.dataConclusaoStr = agora.toLocaleDateString();
  if (observacao) chamado.observacaoConclusao = observacao;
  if (dificuldade) chamado.dificuldadeEncontrada = dificuldade;
  if (urgenciaReal) chamado.urgenciaReal = urgenciaReal;
  chamadosConcluidos.push(chamado);
  saveChamadosToLocalStorage();
  renderChamados();
  registrarLog('Chamado concluído: ' + chamado.titulo + 
    " | Dificuldade: " + dificuldade + 
    " | Urgência Real: " + urgenciaReal + 
    (observacao ? " | Obs: "+observacao : ""));
}

// ==========================
// Modal detalhes do chamado concluído
function verChamadoConcluido(idx) {
  const chamado = chamadosConcluidos[idx];
  let detalhes = `Título: ${chamado.titulo}\nDescrição: ${chamado.descricao}\nData: ${chamado.data}`;
  if (chamado.observacaoConclusao) detalhes += `\nObservação: ${chamado.observacaoConclusao}`;
  if (chamado.dificuldadeEncontrada) detalhes += `\nDificuldade Encontrada: ${chamado.dificuldadeEncontrada}`;
  if (chamado.urgenciaReal) detalhes += `\nUrgência Real Identificada: ${chamado.urgenciaReal}`;
  document.getElementById('detalhesChamadoConcluido').textContent = detalhes;

  // Para CSS com .active
  const modal = document.getElementById('modalVerChamadoConcluido');
  modal.classList.add('active');
  modal.style.display = 'flex'; // ou 'block', conforme seu CSS
}

function fecharVerChamadoConcluido() {
  const modal = document.getElementById('modalVerChamadoConcluido');
  modal.classList.remove('active');
  modal.style.display = 'none';
}

// ==========================
// Função para abrir o modal de confirmação (Limpar histórico)
function abrirModalConfirmacao() {
  const modal = document.getElementById("confirm-modal");
  modal.classList.add("active");
  modal.style.display = "flex";

  document.getElementById("confirm-ok").onclick = function () {
    modal.classList.remove("active");
    modal.style.display = "none";
    chamadosConcluidos = [];
    saveChamadosToLocalStorage();
    renderChamados();
    registrarLog('Histórico de chamados concluídos apagado');
  };
  document.getElementById("confirm-cancel").onclick = function () {
    modal.classList.remove("active");
    modal.style.display = "none";
  };
}

// Função para exportar todos os chamados em PDF
function exportarPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const img = new Image();
  img.crossOrigin = "Anonymous";
  img.src = "https://cdn-icons-png.flaticon.com/512/4149/4149650.png";
  img.onload = function () {
    doc.addImage(img, "PNG", 10, 10, 20, 20);
    doc.setFontSize(18);
    doc.setTextColor(33, 150, 243);
    doc.text("Relatório de Chamados", 35, 20);
    let y = 40;
    // Chamados Abertos
    doc.setFillColor(33, 150, 243);
    doc.setTextColor(255);
    doc.rect(10, y, 190, 10, 'F');
    doc.text("Chamados Abertos", 12, y + 7);
    y += 15;

    doc.setFontSize(12);
    doc.setTextColor(0);
    chamadosAbertos.forEach(c => {
      doc.text(`• Título: ${c.titulo}`, 12, y);
      y += 6;
      doc.text(`Descrição: ${c.descricao}`, 12, y);
      y += 6;
      doc.text(`Data: ${c.data} | Prioridade: ${c.prioridade}`, 12, y);
      y += 10;
    });

    // Chamados Concluídos
    doc.setFillColor(76, 175, 80);
    doc.setTextColor(255);
    doc.rect(10, y, 190, 10, 'F');
    doc.text("Chamados Concluídos", 12, y + 7);
    y += 15;

    doc.setTextColor(0);
    chamadosConcluidos.forEach(c => {
      doc.text(`• Título: ${c.titulo}`, 12, y);
      y += 6;
      doc.text(`Descrição: ${c.descricao}`, 12, y);
      y += 6;
      doc.text(`Data: ${c.data}`, 12, y);
      y += 10;
    });

    doc.save("chamados.pdf");
  };
  registrarLog('Exportou relatório PDF');
}

// Função para exportar Excel
function exportarExcel() {
  const wb = XLSX.utils.book_new();

  const dadosAbertos = chamadosAbertos.map(c => ({
    "Título": c.titulo,
    "Descrição": c.descricao,
    "Data": c.data,
    "Prioridade": c.prioridade
  }));

  const dadosConcluidos = chamadosConcluidos.map(c => ({
    "Título": c.titulo,
    "Descrição": c.descricao,
    "Data": c.data
  }));

  const wsAbertos = XLSX.utils.json_to_sheet(dadosAbertos);
  const wsConcluidos = XLSX.utils.json_to_sheet(dadosConcluidos);

  XLSX.utils.book_append_sheet(wb, wsAbertos, "Chamados Abertos");
  XLSX.utils.book_append_sheet(wb, wsConcluidos, "Chamados Concluídos");

  XLSX.writeFile(wb, "chamados.xlsx");
  registrarLog('Exportou relatório Excel');
}

// Filtro avançado por prioridade
function filtrarChamadosAvancado() {
  const prioridade = document.getElementById("filtroPrioridade").value;
  const termo = document.getElementById("searchChamados").value.toLowerCase();
  const linhas = document.querySelectorAll("#chamados-abertos tbody tr");
  linhas.forEach(linha => {
    const prio = linha.children[3].querySelector('select').value;
    const titulo = linha.children[0].textContent.toLowerCase();
    const descricao = linha.children[1].textContent.toLowerCase();
    let mostra = true;
    if (prioridade && prio !== prioridade) mostra = false;
    if (termo && !(titulo.includes(termo) || descricao.includes(termo))) mostra = false;
    linha.style.display = mostra ? "" : "none";
  });
}

function filtrarChamados() {
  filtrarChamadosAvancado();
}

// Log de atividades
function registrarLog(msg) {
  logAtividades.unshift(`(${new Date().toLocaleTimeString()}) ${msg}`);
  if (logAtividades.length > 20) logAtividades.pop();
  saveChamadosToLocalStorage();
  renderLog();
}
function limparLogAtividades() {
  logAtividades = [];
  saveChamadosToLocalStorage();
  renderLog();
}
function abrirModalConfirmacaoLog() {
  const modal = document.getElementById("confirm-modal-log");
  modal.classList.add("active");
  modal.style.display = "flex";
  document.getElementById("confirm-ok-log").onclick = function () {
    modal.classList.remove("active");
    modal.style.display = "none";
    logAtividades = [];
    saveChamadosToLocalStorage();
    renderLog();
  };
  document.getElementById("confirm-cancel-log").onclick = function () {
    modal.classList.remove("active");
    modal.style.display = "none";
  };
}
function renderLog() {
  const ul = document.getElementById('log-list');
  if (!ul) return;
  if (logAtividades.length === 0) {
    ul.innerHTML = '<li style="color:#999">Nenhuma atividade registrada ainda.</li>';
  } else {
    ul.innerHTML = logAtividades.map(item => `<li>${item}</li>`).join('');
  }
}

// Modal de Notas
function abrirNotasChamado(index) {
  chamadoNotasIndex = index;
  const chamado = chamadosAbertos[index];
  document.getElementById('campoNotas').value = notasChamados[chamado.id] || "";
  document.getElementById('modalNotas').classList.add('active');
  document.getElementById('modalNotas').style.display = 'flex';
}
function salvarNotasChamado() {
  if (chamadoNotasIndex === null) return;
  const campoNotas = document.getElementById('campoNotas');
  const nota = campoNotas.value.trim();

  if (nota === "") {
    alert("Por favor, escreva uma nota antes de salvar.");
    campoNotas.focus();
    return;
  }
  const chamado = chamadosAbertos[chamadoNotasIndex];
  notasChamados[chamado.id] = nota;
  saveChamadosToLocalStorage();
  document.getElementById('modalNotas').classList.remove('active');
  document.getElementById('modalNotas').style.display = 'none';
  registrarLog('Nota adicionada ao chamado: ' + chamado.titulo);
  chamadoNotasIndex = null;
}
function fecharNotasChamado() {
  document.getElementById('modalNotas').classList.remove('active');
  document.getElementById('modalNotas').style.display = 'none';
  chamadoNotasIndex = null;
}

// Atualiza KPIs (Chamados hoje, tempo médio em minutos)
function atualizarKPIs() {
  const hoje = (new Date()).toLocaleDateString();
  const abertosHoje = chamadosAbertos.filter(c => c.data === hoje).length;
  const concluidosHoje = chamadosConcluidos.filter(c => c.data === hoje).length;
  document.getElementById("abertos-hoje").textContent = abertosHoje + concluidosHoje;
}


// Atalho teclado: Ctrl+N para novo chamado
document.addEventListener('keydown', function(e) {
  if (e.ctrlKey && (e.key === 'n' || e.key === 'N')) {
    e.preventDefault();
    openTestModal();
  }
});

// ========= Inicialização =========
window.onload = function() {
  loadChamadosFromLocalStorage();
  renderChamados();
  renderLog();
  atualizarKPIs();

};