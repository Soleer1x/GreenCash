<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Escolha seu Plano - GreenCash</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #4ade80;
            --primary-dark: #22c55e;
            --secondary: #86efac;
            --accent: #bbf7d0;
            --success: #16a34a;
            --warning: #eab308;
            --error: #dc2626;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --white: #ffffff;
            --glass: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 50%, #86efac 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Background Effects */
        .bg-effects {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            opacity: 0.2;
            animation: float 20s infinite ease-in-out;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            top: -200px;
            left: -200px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 300px;
            height: 300px;
            top: 50%;
            right: -150px;
            animation-delay: -7s;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: 30%;
            animation-delay: -14s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            33% { transform: translateY(-30px) rotate(120deg) scale(1.1); }
            66% { transform: translateY(20px) rotate(240deg) scale(0.9); }
        }

        /* Back Button */
        .back-button {
            position: fixed;
            top: 2rem;
            left: 2rem;
            z-index: 100;
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-700);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.25rem;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* Main Container */
        .main-container {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem 2rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 3rem;
            animation: fadeInUp 0.8s ease;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
            box-shadow: 0 8px 32px rgba(74, 222, 128, 0.3);
        }

        .brand-name {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--gray-800), var(--gray-700));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-title {
            font-size: 3rem;
            font-weight: 900;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
            line-height: 1.1;
        }

        .header-subtitle {
            font-size: 1.25rem;
            color: var(--gray-600);
            font-weight: 400;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Plans Grid */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 2rem;
            width: 100%;
            margin-bottom: 3rem;
            animation: fadeInUp 0.8s 0.2s both;
        }

        .plan-card {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            padding: 2rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .plan-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .plan-card:hover::before,
        .plan-card.selected::before {
            transform: scaleX(1);
        }

        .plan-card:hover,
        .plan-card.selected {
            background: rgba(255, 255, 255, 0.6);
            border-color: rgba(255, 255, 255, 0.8);
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(74, 222, 128, 0.2);
        }

        .plan-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .plan-card:hover .plan-icon,
        .plan-card.selected .plan-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 32px rgba(74, 222, 128, 0.4);
        }

        .plan-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .plan-price {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--primary-dark);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
        }

        .plan-price .currency {
            font-size: 1.25rem;
            opacity: 0.8;
        }

        .plan-period {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 1.5rem;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            color: var(--gray-700);
            font-size: 0.95rem;
        }

        .plan-features .check-icon {
            width: 16px;
            height: 16px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .plan-badge {
            position: absolute;
            top: -8px;
            right: 2rem;
            background: linear-gradient(135deg, var(--warning), #f59e0b);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Payment Section */
        .payment-section {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            padding: 2rem;
            display: none;
            animation: fadeInUp 0.6s ease;
        }

        .payment-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .payment-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.25rem;
        }

        .payment-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--gray-700);
            font-size: 0.875rem;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            color: var(--gray-800);
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input::placeholder {
            color: var(--gray-500);
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .submit-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 12px;
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(74, 222, 128, 0.4);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        /* Success Modal */
        .success-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        .success-content {
            background: var(--white);
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: scaleIn 0.4s ease;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
            animation: bounce 0.6s ease;
        }

        .success-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .success-message {
            color: var(--gray-600);
            margin-bottom: 2rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0, -15px, 0);
            }
            70% {
                transform: translate3d(0, -7px, 0);
            }
            90% {
                transform: translate3d(0, -2px, 0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 2rem 1rem;
            }

            .header-title {
                font-size: 2rem;
            }

            .header-subtitle {
                font-size: 1rem;
            }

            .plans-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .plan-card {
                padding: 1.5rem;
            }

            .payment-section {
                padding: 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .back-button {
                top: 1rem;
                left: 1rem;
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-effects">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
    </div>

    <!-- Back Button -->
    <button class="back-button" onclick="window.history.back()" title="Voltar">
        <span class="material-symbols-rounded">arrow_back</span>
    </button>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Header -->
        <div class="header">
            <div class="brand">
                <div class="brand-icon">
                    <span class="material-symbols-rounded">account_balance_wallet</span>
                </div>
                <h1 class="brand-name">GreenCash</h1>
            </div>
            <h2 class="header-title">Escolha seu Plano</h2>
            <p class="header-subtitle">Selecione o plano ideal para controlar suas finanças e alcançar seus objetivos</p>
        </div>

        <!-- Plans Grid -->
        <div class="plans-grid">
            <!-- Basic Plan -->
            <div class="plan-card" data-plano="basico" tabindex="0">
                <div class="plan-icon">
                    <span class="material-symbols-rounded">account_balance_wallet</span>
                </div>
                <h3 class="plan-name">Básico</h3>
                <div class="plan-price">
                    <span class="currency">R$</span>0
                </div>
                <p class="plan-period">Gratuito para sempre</p>
                <ul class="plan-features">
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Controle de receitas e despesas
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Categorias básicas
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Histórico de 3 meses
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Suporte por email
                    </li>
                </ul>
            </div>

            <!-- Intermediate Plan -->
            <div class="plan-card" data-plano="intermediario" tabindex="0">
                <div class="plan-badge">Mais Popular</div>
                <div class="plan-icon">
                    <span class="material-symbols-rounded">trending_up</span>
                </div>
                <h3 class="plan-name">Intermediário</h3>
                <div class="plan-price">
                    <span class="currency">R$</span>19<span style="font-size:1rem;opacity:0.8;">,90</span>
                </div>
                <p class="plan-period">por mês</p>
                <ul class="plan-features">
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Tudo do plano Básico
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Relatórios avançados
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Gráficos interativos
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Metas financeiras
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Histórico ilimitado
                    </li>
                </ul>
            </div>

            <!-- Advanced Plan -->
            <div class="plan-card" data-plano="avancado" tabindex="0">
                <div class="plan-icon">
                    <span class="material-symbols-rounded">auto_awesome</span>
                </div>
                <h3 class="plan-name">Avançado</h3>
                <div class="plan-price">
                    <span class="currency">R$</span>29<span style="font-size:1rem;opacity:0.8;">,90</span>
                </div>
                <p class="plan-period">por mês</p>
                <ul class="plan-features">
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Tudo do plano Intermediário
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Suporte prioritário 24/7
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Análise de investimentos
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Planejamento financeiro
                    </li>
                    <li>
                        <span class="check-icon">
                            <span class="material-symbols-rounded">check</span>
                        </span>
                        Consultoria personalizada
                    </li>
                </ul>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="payment-section" id="paymentSection">
            <div class="payment-header">
                <div class="payment-icon">
                    <span class="material-symbols-rounded">credit_card</span>
                </div>
                <h3 class="payment-title">Dados do Cartão</h3>
            </div>

            <form id="addCardForm">
                <input type="hidden" id="selectedPlano" name="plano" value="">
                
                <div class="form-group">
                    <label for="cardNumber" class="form-label">Número do Cartão</label>
                    <input type="text" class="form-input" id="cardNumber" name="numero" placeholder="**** **** **** ****" required autocomplete="cc-number">
                </div>

                <div class="form-group">
                    <label for="cardHolder" class="form-label">Nome do Titular</label>
                    <input type="text" class="form-input" id="cardHolder" name="titular" placeholder="Nome completo" required autocomplete="cc-name">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cardExpiry" class="form-label">Validade</label>
                        <input type="text" class="form-input" id="cardExpiry" name="validade" placeholder="MM/AA" required autocomplete="cc-exp">
                    </div>
                    <div class="form-group">
                        <label for="cardCVV" class="form-label">CVV</label>
                        <input type="text" class="form-input" id="cardCVV" name="cvv" placeholder="123" maxlength="4" required autocomplete="cc-csc">
                    </div>
                </div>

                <div class="form-group">
                    <label for="salary" class="form-label">Salário</label>
                    <input type="number" class="form-input" id="salary" name="salario" placeholder="Ex.: 2000" required min="0">
                </div>

                <div class="form-group">
                    <label for="creditLimit" class="form-label">Limite do Cartão</label>
                    <input type="number" class="form-input" id="creditLimit" name="limite" placeholder="Ex.: 10000" required min="0">
                </div>

                <div class="form-group">
                    <label for="cardType" class="form-label">Bandeira do Cartão</label>
                    <select class="form-select" id="cardType" name="tipo" required>
                        <option value="Mastercard" selected>MasterCard</option>
                        <option value="Visa">Visa</option>
                        <option value="AmericanExpress">American Express</option>
                        <option value="Discover">Discover</option>
                    </select>
                </div>

                <button type="submit" class="submit-button">
                    Confirmar Assinatura
                </button>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="success-modal" id="modal-sucesso-plano">
        <div class="success-content">
            <div class="success-icon">
                <span class="material-symbols-rounded">check</span>
            </div>
            <h3 class="success-title">Sucesso!</h3>
            <p class="success-message">Seu plano foi ativado com sucesso. Redirecionando...</p>
        </div>
    </div>

    <script>
        // Plan selection logic
        let planoEscolhido = null;
        document.querySelectorAll('.plan-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                planoEscolhido = this.getAttribute('data-plano');
                document.getElementById('selectedPlano').value = planoEscolhido;
                document.getElementById('paymentSection').style.display = 'block';
                document.getElementById('paymentSection').scrollIntoView({ behavior: 'smooth' });
            });

            // Keyboard accessibility
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });

        // Input masks and validation
        document.getElementById("cardHolder").addEventListener("input", function (e) {
            this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, "");
        });

        document.getElementById("cardNumber").addEventListener("input", function (e) {
            let val = this.value.replace(/\D/g, "").slice(0, 16);
            let formatted = "";
            for (let i = 0; i < val.length; i += 4) {
                if (i > 0) formatted += " ";
                formatted += val.substr(i, 4);
            }
            this.value = formatted;
        });

        document.getElementById("cardExpiry").addEventListener("input", function (e) {
            let val = this.value.replace(/\D/g, "").slice(0, 4);
            if (val.length > 2) {
                this.value = val.slice(0, 2) + "/" + val.slice(2);
            } else {
                this.value = val;
            }
        });

        document.getElementById("cardCVV").addEventListener("input", function (e) {
            this.value = this.value.replace(/\D/g, "").slice(0, 4);
        });

        document.getElementById("salary").addEventListener("input", function (e) {
            this.value = this.value.replace(/\D/g, "");
        });

        document.getElementById("creditLimit").addEventListener("input", function (e) {
            this.value = this.value.replace(/\D/g, "");
        });

        // Form submission
        document.getElementById("addCardForm").addEventListener("submit", function (e) {
            e.preventDefault();
            
            const dados = {
                plano: document.getElementById('selectedPlano').value,
                numero: document.getElementById('cardNumber').value.replace(/\s/g, ''),
                titular: document.getElementById('cardHolder').value,
                validade: document.getElementById('cardExpiry').value,
                cvv: document.getElementById('cardCVV').value,
                salario: document.getElementById('salary').value,
                limite: document.getElementById('creditLimit').value,
                tipo: document.getElementById('cardType').value
            };

            fetch('plano_assinar.php', {
                method: 'POST',
                body: new URLSearchParams(dados)
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.sucesso) {
                    document.getElementById('modal-sucesso-plano').style.display = 'flex';
                    setTimeout(() => window.location.href = 'dashboard.php', 1800);
                } else {
                    alert('Erro ao assinar plano');
                }
            })
            .catch(() => {
                alert('Erro ao processar o pagamento. Tente novamente.');
            });
        });
    </script>
</body>
</html>