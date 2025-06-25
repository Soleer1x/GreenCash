// Inicializa as variáveis necessárias
const db = firebase.firestore();
let totalReceita = 0;
let totalDespesa = 0;
let dataItems = [];
let ordemCrescente = true; // Controla a ordem de ordenação

// Função para atualizar os valores dos cards
function updateCardValues() {
    document.getElementById('totalReceitas').textContent = `R$ ${totalReceita.toFixed(2)}`;
    document.getElementById('totalDespesas').textContent = `R$ ${totalDespesa.toFixed(2)}`;

    // Calcula e exibe o saldo total
    const saldoTotal = totalReceita - totalDespesa;
    document.getElementById('saldoTotal').textContent = `R$ ${saldoTotal.toFixed(2)}`;
}

// Função para formatar a data
function formatarData(date) {
    return new Date(date).toLocaleDateString('pt-BR');
}

// Função para calcular o status de uma receita, despesa ou plano
function calcularStatus(data) {
    const hoje = new Date();
    const dataObj = new Date(data);
    return hoje > dataObj ? 'Pendente' : 'Concluido';
}

// Função para carregar receitas, despesas e planos do Firestore
function loadData() {
    const userId = firebase.auth().currentUser.uid;

    // Carrega receitas
    db.collection('receitas')
        .where('type', '==', 'receita')
        .where('user', '==', userId)
        .get()
        .then((querySnapshot) => {
            totalReceita = 0;
            querySnapshot.forEach((doc) => {
                const receita = doc.data();
                dataItems.push(receita);
                totalReceita += receita.amount;
            });
            updateCardValues(); // Atualiza o valor total das receitas
        });

    // Carrega despesas
    db.collection('despesas')
        .where('type', '==', 'despesa')
        .where('user', '==', userId)
        .get()
        .then((querySnapshot) => {
            totalDespesa = 0;
            querySnapshot.forEach((doc) => {
                const despesa = doc.data();
                dataItems.push(despesa);
                totalDespesa += despesa.amount;
            });
            updateCardValues(); // Atualiza o valor total das despesas
        });

    // Carrega planos
    db.collection('planos')
        .where('type', '==', 'plano')
        .where('user', '==', userId)
        .get()
        .then((querySnapshot) => {
            totalPlano = 0;
            querySnapshot.forEach((doc) => {
                const plano = doc.data();
                dataItems.push(plano);
                totalPlano += plano.amount;
            });
            updateCardValues(); // Atualiza o valor total dos planos
            renderTable(); // Renderiza a tabela com os dados carregados
        });
}

// Função para adicionar uma linha na tabela de dados
function addRowToTable(item) {
    const table = document.getElementById('data-table-body');
    const newRow = table.insertRow();

    const descriptionCell = newRow.insertCell(0);
    descriptionCell.textContent = item.description;

    const amountCell = newRow.insertCell(1);
    amountCell.textContent = `R$ ${item.amount.toFixed(2)}`;

    const dateCell = newRow.insertCell(2);
    dateCell.textContent = formatarData(item.date);

    const typeCell = newRow.insertCell(3);
    typeCell.textContent = item.type.charAt(0).toUpperCase() + item.type.slice(1); // Capitaliza o tipo

    // Define a cor de fundo da célula da coluna Tipo com base no tipo
    const corFundo = obterCorFundo(item.type);
    typeCell.style.backgroundColor = corFundo;
    typeCell.style.color = '#ffffff'; // Cor do texto para contraste
    typeCell.style.borderRadius = '5px'; // Bordas arredondadas
    typeCell.style.padding = '5px'; // Adiciona um pouco de espaço interno
    typeCell.style.textAlign = 'center'; // Alinha o texto ao centro
    typeCell.style.fontWeight = 'bold'; // Destaca o texto

    const statusCell = newRow.insertCell(4);
    statusCell.textContent = calcularStatus(item.date);
}

// Função para renderizar a tabela com os dados
function renderTable() {
    const table = document.getElementById('data-table-body');
    table.innerHTML = ''; // Limpa a tabela antes de adicionar os dados

    dataItems.forEach(item => addRowToTable(item));
}

// Função para ordenar os dados por data
function ordenarPorData() {
    dataItems.sort((a, b) => {
        const dateA = new Date(a.date);
        const dateB = new Date(b.date);
        return ordemCrescente ? dateA - dateB : dateB - dateA;
    });
    ordemCrescente = !ordemCrescente; // Alterna a ordem de ordenação
    renderTable(); // Renderiza a tabela com os dados ordenados
}

// Função para obter a cor de fundo com base no tipo
function obterCorFundo(tipo) {
    switch (tipo) {
        case 'receita':
            return '#28a745'; // Verde forte
        case 'despesa':
            return '#dc3545'; // Vermelho forte
        case 'plano':
            return '#007bff'; // Azul forte
        default:
            return '#ffffff'; // Branco padrão
    }
}

// Inicializa o carregamento dos dados ao carregar a página
window.onload = function() {
    firebase.auth().onAuthStateChanged((user) => {
        if (user) {
            loadData();
        } else {
            console.log("Usuário não autenticado.");
        }
    });

    // Adiciona o evento de clique ao botão "Ordenar por Data"
    document.getElementById('btnOrdenarPorData').addEventListener('click', ordenarPorData);
};

// Verifica o estado de autenticação do usuário
firebase.auth().onAuthStateChanged((user) => {
    if (user) {
        // Usuário está logado
        const userEmail = user.email; // Pega o email do usuário
        const userName = user.displayName; // Pega o nome do usuário, se estiver disponível
        
        // Exibe o email ou nome do usuário no DOM
        document.getElementById('user-email').textContent = userName ? userName : userEmail;
    } else {
        // Usuário não está logado
        document.getElementById('user-info').textContent = "Nenhum usuário logado.";
    }


    
});
function atualizarTabelaDespesas() {
    const tableBody = document.getElementById("modal-despesas-body");
    tableBody.innerHTML = "";

    const rows = document.querySelectorAll("#data-table-body tr");

    rows.forEach(row => {
        if (row.style.display === "none") return;

        const tipo = row.cells[3]?.textContent?.toLowerCase();
        const descricao = row.cells[0]?.textContent;
        const valor = row.cells[1]?.textContent;
        const data = row.cells[2]?.textContent;

        if (tipo === "despesa" && descricao) {
            const newRow = document.createElement("tr");

            newRow.innerHTML = `
                <td>${descricao}</td>
                <td>${valor}</td>
                <td>${data}</td>
                <td><button onclick="pagarDespesa(this)" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 5px;">Pagar</button></td>
            `;

            // Guarda uma referência da linha original
            newRow.dataset.dashboardIndex = [...rows].indexOf(row);

            tableBody.appendChild(newRow);
        }
    });
}

// Quando clicar em "Pagar"
function pagarDespesa(button) {
    const row = button.closest('tr');
    const descricao = row.cells[0].textContent;

    const uniqueId = Date.now();
    const paymentUrl = `https://seusite.com/pagar?id=${uniqueId}&desc=${encodeURIComponent(descricao)}`;

    // Limpar QR antigo
    const qrDiv = document.getElementById("qrcode");
    qrDiv.innerHTML = "";

    // Gerar novo QR Code
    new QRCode(qrDiv, {
        text: paymentUrl,
        width: 200,
        height: 200
    });

    // Espera 3 segundos para simular pagamento e remove a despesa
    setTimeout(() => {
        // Remove do modal
        row.remove();

        // Remove do dashboard
        const dashboardRows = document.querySelectorAll("#data-table-body tr");
        const dashboardIndex = row.dataset.dashboardIndex;
        if (dashboardRows[dashboardIndex]) {
            dashboardRows[dashboardIndex].remove();
        }

        console.log(`Despesa "${descricao}" paga e removida.`);
    }, 3000); // 3 segundos (ou você pode colocar "Confirmar" manualmente se quiser)
}

// Atualizar automaticamente
setInterval(atualizarTabelaDespesas, 1000);


