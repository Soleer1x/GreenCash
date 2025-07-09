// Menu hamburguer responsivo
const navToggle = document.getElementById('nav-toggle');
const nav = document.getElementById('main-nav');
navToggle.onclick = function() { nav.classList.toggle('open'); };

// Painel lateral de notificações
const sidePanel = document.getElementById('side-panel');
document.getElementById('open-panel').onclick = ()=> sidePanel.classList.add('open');
document.getElementById('close-panel').onclick = ()=> sidePanel.classList.remove('open');
window.addEventListener('keydown', e => { if(e.key==='Escape') sidePanel.classList.remove('open'); });

// Chart.js pizza despesas
let despesasData = {
  labels: ['Alimentação', 'Moradia', 'Transporte', 'Lazer', 'Outros'],
  datasets: [{
    data: [450, 600, 220, 180, 120],
    backgroundColor: ['#7c4dff','#ffab00','#39c779','#ff5252','#00bcd4']
  }]
};
let ctx = document.getElementById('despesasChart').getContext('2d');
let despesasChart = new Chart(ctx, {
  type: 'pie',
  data: despesasData,
  options: { plugins: { legend: { display: false } } }
});

// Modal Nova Despesa
const modal = document.getElementById('modal-despesa');
document.getElementById('nova-despesa-btn').onclick = ()=>modal.classList.add('open');
document.getElementById('fechar-modal').onclick = ()=>modal.classList.remove('open');
modal.onclick = e => { if(e.target===modal) modal.classList.remove('open'); };

// Adicionar despesa na pizza e dashboard
document.getElementById('form-despesa').onsubmit = function(e){
  e.preventDefault();
  let nome = document.getElementById('despesa-nome').value.trim();
  let valor = parseFloat(document.getElementById('despesa-valor').value);
  let cat = document.getElementById('despesa-cat').value;
  if(!nome || valor <= 0) return;
  let idx = despesasData.labels.indexOf(cat);
  despesasData.datasets[0].data[idx] += valor;
  despesasChart.update();
  // Atualiza dashboard valores
  let despesas = despesasData.datasets[0].data.reduce((a,b)=>a+b,0);
  let receitas = 5000;
  document.getElementById('despesas-mes').textContent = "R$ "+despesas.toLocaleString('pt-BR',{minimumFractionDigits:2});
  document.getElementById('saldo-atual').textContent = "R$ "+(receitas-despesas).toLocaleString('pt-BR',{minimumFractionDigits:2});
  modal.classList.remove('open');
  this.reset();
};

// Exportar CSV despesas
document.getElementById('export-csv').onclick = function(){
  let csv = 'Categoria,Valor\n';
  despesasData.labels.forEach((cat,i)=>{ csv+=cat+','+despesasData.datasets[0].data[i]+'\n'; });
  let blob = new Blob([csv],{type:"text/csv"});
  let a = document.createElement('a');
  a.href=URL.createObjectURL(blob); a.download="despesas.csv"; a.click();
};

// Barra de progresso das metas
let goal1 = 7600, goal1Total = 10000;
let goal2 = 1800, goal2Total = 5000;
function updateGoalBars() {
  const p1 = Math.min(100, Math.round(goal1/goal1Total*100));
  const p2 = Math.min(100, Math.round(goal2/goal2Total*100));
  document.getElementById('goal-bar-1').style.width = p1+"%";
  document.getElementById('goal-bar-2').style.width = p2+"%";
  document.getElementById('goal-bar-1-label').innerHTML = `${p1}% (${goal1.toLocaleString('pt-BR',{style:'currency',currency:'BRL'})})`;
  document.getElementById('goal-bar-2-label').innerHTML = `${p2}% (${goal2.toLocaleString('pt-BR',{style:'currency','currency':'BRL'})})`;
}
document.getElementById('update-goals-btn').onclick = function() {
  goal1 = Math.min(goal1Total, goal1 + Math.round(Math.random()*400 + 200));
  goal2 = Math.min(goal2Total, goal2 + Math.round(Math.random()*300 + 100));
  updateGoalBars();
  // Pontos de gamificação
  const pts = document.getElementById('g-points');
  pts.textContent = parseInt(pts.textContent) + 10;
};
updateGoalBars();

// Gamificação - convite
document.getElementById('invite-btn').onclick = function() {
  let email = document.getElementById('invite-email').value;
  let status = document.getElementById('invite-status');
  if(!email || email.length<4 || !email.includes("@")) {
    status.style.color = "#e75d5d";
    status.textContent = "E-mail inválido!";
    return;
  }
  status.style.color = "#39c779";
  status.textContent = "Convite enviado!";
  setTimeout(()=>{status.textContent="";}, 1500);
  document.getElementById('g-points').textContent = parseInt(document.getElementById('g-points').textContent)+20;
  document.getElementById('invite-email').value = '';
};

// Ranking fictício
const rankingFake = [
  {pos:1, user:"Amanda", avatar:"https://randomuser.me/api/portraits/women/44.jpg", pts:950, ach:"🏆💰🧮"},
  {pos:2, user:"Pedro", avatar:"https://randomuser.me/api/portraits/men/41.jpg", pts:910, ach:"💰🧮"},
  {pos:3, user:"Thiago", avatar:"https://randomuser.me/api/portraits/men/50.jpg", pts:890, ach:"🏆💰"},
  {pos:4, user:"Carol", avatar:"https://randomuser.me/api/portraits/women/50.jpg", pts:850, ach:"🏆"},
  {pos:5, user:"Lucas", avatar:"https://randomuser.me/api/portraits/men/39.jpg", pts:790, ach:"💰"},
  {pos:6, user:"Sofia", avatar:"https://randomuser.me/api/portraits/women/65.jpg", pts:773, ach:"💰🧮"},
  {pos:7, user:"Fernanda", avatar:"https://randomuser.me/api/portraits/women/48.jpg", pts:710, ach:"🏆"},
  {pos:8, user:"Rafael", avatar:"https://randomuser.me/api/portraits/men/60.jpg", pts:690, ach:"💰"},
  {pos:9, user:"Vitor", avatar:"https://randomuser.me/api/portraits/men/33.jpg", pts:680, ach:"🏆"},
  {pos:10, user:"Maria", avatar:"https://randomuser.me/api/portraits/women/24.jpg", pts:670, ach:"💰"},
  {pos:11, user:"Elisa", avatar:"https://randomuser.me/api/portraits/women/70.jpg", pts:623, ach:"💰"},
  {pos:12, user:"Você", avatar:"https://randomuser.me/api/portraits/men/10.jpg", pts:370, ach:"🏆💰"},
];
function renderRankingTable() {
  document.getElementById('ranking-list').innerHTML =
    rankingFake.map(r=>
      `<tr>
        <td>${r.pos <= 3 ? `<span class="crown">${["🥇","🥈","🥉"][r.pos-1]}</span>` : r.pos}</td>
        <td><img src="${r.avatar}"> ${r.user}</td>
        <td>${r.pts}</td>
        <td>${r.ach}</td>
      </tr>`
    ).join('');
}
renderRankingTable();

// Motivational Quote Widget
async function fetchQuote() {
  const txt = document.getElementById('quote-text');
  const author = document.getElementById('quote-author');
  txt.textContent = "Carregando citação motivacional...";
  author.textContent = "";
  try {
    let res = await fetch('https://api.quotable.io/random?tags=success|motivation|money');
    let data = await res.json();
    txt.textContent = `"${data.content}"`;
    author.textContent = data.author ? "- " + data.author : "";
  } catch {
    txt.textContent = "Não foi possível carregar a citação.";
    author.textContent = "";
  }
}
document.getElementById('refresh-quote').onclick = fetchQuote;
fetchQuote();

// Tema escuro/claro persistente
function setTheme(dark) {
  document.body.classList.toggle('dark', dark);
  document.getElementById('theme-toggle').textContent = dark ? "☀️" : "🌙";
  localStorage.setItem("financepro_theme", dark ? "dark" : "light");
}
(function(){
  let theme = localStorage.getItem("financepro_theme");
  if(!theme) theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? "dark" : "light";
  setTheme(theme==="dark");
})();
document.getElementById('theme-toggle').onclick = () => setTheme(!document.body.classList.contains('dark'));

// FAQ interativo
document.querySelectorAll('.faq-question').forEach(btn => {
  btn.onclick = function() {
    const parent = btn.parentElement;
    if (parent.classList.contains('open')) {
      parent.classList.remove('open');
    } else {
      document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
      parent.classList.add('open');
    }
  };
});

// Simulação de envio do formulário de contato
document.getElementById('contact-form').onsubmit = function(e) {
  e.preventDefault();
  const nome = document.getElementById('c-nome').value.trim();
  const email = document.getElementById('c-email').value.trim();
  const msg = document.getElementById('c-msg').value.trim();
  const status = document.getElementById('contact-status');
  if (nome.length < 2 || !email.includes('@') || msg.length < 5) {
    status.style.color = '#e75d5d';
    status.textContent = 'Preencha os campos corretamente.';
    return;
  }
  status.style.color = '#39c779';
  status.textContent = 'Mensagem enviada! Retornaremos em breve.';
  setTimeout(() => { status.textContent = ''; document.getElementById('contact-form').reset(); }, 2500);
};

// Newsletter
document.getElementById('newsletter-form').onsubmit = function(e) {
  e.preventDefault();
  const email = document.getElementById('newsletter-email').value.trim();
  const status = document.getElementById('newsletter-status');
  if(!email.includes('@') || email.length < 6) {
    status.style.color = '#e75d5d';
    status.textContent = 'Insira um e-mail válido!';
    return;
  }
  status.style.color = '#39c779';
  status.textContent = 'Assinatura realizada!';
  setTimeout(() => { status.textContent = ''; document.getElementById('newsletter-form').reset(); }, 1800);
};

// Simulador de Objetivo
document.getElementById('goal-simulator').onsubmit = function(e){
  e.preventDefault();
  const total = parseFloat(document.getElementById('goal-total').value);
  const months = parseInt(document.getElementById('goal-months').value, 10);
  const res = document.getElementById('goal-result');
  if(total > 0 && months > 0){
    const mensal = total / months;
    res.textContent = `Guarde R$ ${mensal.toLocaleString('pt-BR',{minimumFractionDigits:2})} por mês para atingir seu objetivo.`;
  } else {
    res.textContent = '';
  }
};

// Simulador de Investimento
document.getElementById('invest-simulator').onsubmit = function(e){
  e.preventDefault();
  const ini = parseFloat(document.getElementById('invest-initial').value);
  const rate = parseFloat(document.getElementById('invest-rate').value)/100;
  const months = parseInt(document.getElementById('invest-months').value, 10);
  const res = document.getElementById('invest-result');
  if(ini > 0 && rate > 0 && months > 0){
    let total = ini * Math.pow(1+rate, months);
    res.textContent = `Após ${months} meses: R$ ${total.toLocaleString('pt-BR',{minimumFractionDigits:2})}`;
  } else {
    res.textContent = '';
  }
};

// Comunidade (fórum)
document.querySelector('.join-community').onclick = function(e){
  e.preventDefault();
  alert('Comunidade em breve! Fique ligado para participar do fórum FinancePro.');
};

// Popup de boas-vindas
window.addEventListener("DOMContentLoaded", function(){
  if(!localStorage.getItem("financepro_welcome")) {
    document.getElementById('welcome-popup').classList.add('active');
    document.getElementById('close-welcome').onclick = function(){
      document.getElementById('welcome-popup').classList.remove('active');
      localStorage.setItem("financepro_welcome", "1");
    };
  }
});