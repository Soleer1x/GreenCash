// Variáveis globais para armazenar dados
let chamadosAbertos = [];
let chamadosConcluidos = [];
let historicoAlteracoes = [];
let comentariosChamados = {};

// Simular novo chamado
function simulateNewRequest() {
    const titulo = document.getElementById("test-titulo").value;
    const descricao = document.getElementById("test-descricao").value;
    const prioridade = document.getElementById("test-prioridade").value;

    if (!titulo || !descricao) {
        alert("Por favor, preencha todos os campos!");
        return;
    }

    const novoChamado = {
        id: Date.now(), // Gera um ID único
        titulo,
        descricao,
        prioridade,
        data: new Date().toLocaleDateString(),
        status: "Aberto",
    };

    chamadosAbertos.push(novoChamado);
    historicoAlteracoes.push(`Chamado '${titulo}' criado em ${novoChamado.data}.`);
    closeTestModal();
    renderChamados();
    exibirNotificacao("Novo chamado registrado com sucesso!");
}

// Renderizar chamados
function renderChamados() {
    const abertosTable = document.getElementById("chamados-abertos").querySelector("tbody");
    const concluidosTable = document.getElementById("chamados-concluidos").querySelector("tbody");

    abertosTable.innerHTML = "";
    concluidosTable.innerHTML = "";

    // Chamados Abertos
    chamadosAbertos.forEach((chamado, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${chamado.titulo}</td>
            <td>${chamado.descricao}</td>
            <td>${chamado.data}</td>
            <td>${chamado.prioridade}</td>
            <td>
                <button onclick="concluirChamado(${index})" class="btn-edit">Concluir</button>
                <button onclick="abrirComentario(${chamado.id})" class="btn-edit">Comentar</button>
                <button onclick="gerarPDF(${index})" class="btn-edit">PDF</button>
                <button onclick="excluirChamado(${index})" class="btn-delete">Excluir</button>
            </td>
        `;
        abertosTable.appendChild(row);
    });

    // Chamados Concluídos
    chamadosConcluidos.forEach((chamado) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${chamado.titulo}</td>
            <td>${chamado.descricao}</td>
            <td>${chamado.data}</td>
        `;
        concluidosTable.appendChild(row);
    });

    atualizarIndicadores();
}

// Atualizar indicadores
function atualizarIndicadores() {
    document.getElementById("totalChamadosAbertos").innerText = chamadosAbertos.length;
    document.getElementById("totalChamadosConcluidos").innerText = chamadosConcluidos.length;

    const altaPrioridade = chamadosAbertos.filter(chamado => chamado.prioridade === "Alta").length;
    document.getElementById("chamadosAltaPrioridade").innerText = altaPrioridade;
}

// Concluir chamado
function concluirChamado(index) {
    const chamado = chamadosAbertos.splice(index, 1)[0];
    chamado.status = "Concluído";
    chamadosConcluidos.push(chamado);
    historicoAlteracoes.push(`Chamado '${chamado.titulo}' concluído em ${new Date().toLocaleDateString()}.`);
    renderChamados();
    exibirNotificacao("Chamado concluído com sucesso!");
}

// Excluir chamado
function excluirChamado(index) {
    const chamado = chamadosAbertos.splice(index, 1)[0];
    historicoAlteracoes.push(`Chamado '${chamado.titulo}' excluído em ${new Date().toLocaleDateString()}.`);
    renderChamados();
    exibirNotificacao("Chamado excluído com sucesso!");
}

// Limpar histórico
function clearHistory() {
    if (confirm("Tem certeza de que deseja limpar todo o histórico de chamados concluídos?")) {
        chamadosConcluidos = [];
        renderChamados();
        exibirNotificacao("Histórico de chamados concluídos limpo!");
    }
}

// Gerar PDF
function gerarPDF(index) {
    const chamado = chamadosAbertos[index];
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(18);
    doc.text("Relatório de Chamado", 10, 10);

    doc.setFontSize(12);
    doc.text(`Título: ${chamado.titulo}`, 10, 30);
    doc.text(`Descrição: ${chamado.descricao}`, 10, 50);
    doc.text(`Prioridade: ${chamado.prioridade}`, 10, 70);
    doc.text(`Data: ${chamado.data}`, 10, 90);

    doc.save(`${chamado.titulo}.pdf`);
}

// Exportar para Excel
function exportarExcel() {
    const XLSX = window.XLSX;
    const dados = chamadosAbertos.map(chamado => ({
        Título: chamado.titulo,
        Descrição: chamado.descricao,
        Data: chamado.data,
        Prioridade: chamado.prioridade,
        Status: chamado.status,
    }));

    const worksheet = XLSX.utils.json_to_sheet(dados);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Chamados Abertos");

    XLSX.writeFile(workbook, "chamados_abertos.xlsx");
    exibirNotificacao("Arquivo Excel exportado com sucesso!");
}

// Adicionar comentário
function abrirComentario(chamadoId) {
    document.getElementById("comment-modal").classList.add("active");
    document.getElementById("comment-modal").setAttribute("data-chamado-id", chamadoId);
}

function adicionarComentario() {
    const chamadoId = document.getElementById("comment-modal").getAttribute("data-chamado-id");
    const comentario = document.getElementById("comment-text").value;

    if (!comentariosChamados[chamadoId]) {
        comentariosChamados[chamadoId] = [];
    }

    comentariosChamados[chamadoId].push({
        texto: comentario,
        data: new Date().toLocaleDateString(),
    });

    historicoAlteracoes.push(`Comentário adicionado ao chamado (${chamadoId}) em ${new Date().toLocaleDateString()}.`);
    closeCommentModal();
    exibirNotificacao("Comentário adicionado com sucesso!");
}

// Exibir notificações
function exibirNotificacao(mensagem) {
    const notificacao = document.createElement("div");
    notificacao.className = "notificacao";
    notificacao.innerText = mensagem;
    document.body.appendChild(notificacao);

    setTimeout(() => notificacao.remove(), 3000);
}

// Filtrar chamados
function filtrarChamados() {
    const termo = document.getElementById("searchChamados").value.toLowerCase();
    const chamadosFiltrados = chamadosAbertos.filter(chamado =>
        chamado.titulo.toLowerCase().includes(termo) ||
        chamado.descricao.toLowerCase().includes(termo)
    );

    const abertosTable = document.getElementById("chamados-abertos").querySelector("tbody");
    abertosTable.innerHTML = "";

    chamadosFiltrados.forEach((chamado, index) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${chamado.titulo}</td>
            <td>${chamado.descricao}</td>
            <td>${chamado.data}</td>
            <td>${chamado.prioridade}</td>
            <td>
                <button onclick="concluirChamado(${index})" class="btn-edit">Concluir</button>
                <button onclick="abrirComentario(${chamado.id})" class="btn-edit">Comentar</button>
                <button onclick="gerarPDF(${index})" class="btn-edit">PDF</button>
                <button onclick="excluirChamado(${index})" class="btn-delete">Excluir</button>
            </td>
        `;
        abertosTable.appendChild(row);
    });
}

// Alternância de tema (claro/escuro)
function toggleTheme() {
    const body = document.body;
    const themeLabel = document.getElementById("themeLabel");

    body.classList.toggle("dark-theme");
    if (body.classList.contains("dark-theme")) {
        themeLabel.textContent = "Modo Escuro";
    } else {
        themeLabel.textContent = "Modo Claro";
    }
}

// Alternar modal
function openTestModal() {
    document.getElementById("test-modal").classList.add("active");
}

function closeTestModal() {
    document.getElementById("test-modal").classList.remove("active");
}

function closeCommentModal() {
    document.getElementById("comment-modal").classList.remove("active");
}