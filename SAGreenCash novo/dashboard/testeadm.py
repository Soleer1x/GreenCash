import unittest
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options

class GreenCashAdminPanelTest(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        # Configurações do Chrome para melhor performance
        chrome_options = Options()
        chrome_options.add_argument('--disable-extensions')
        chrome_options.add_argument('--disable-gpu')
        chrome_options.add_argument('--no-sandbox')
        chrome_options.add_argument('--disable-dev-shm-usage')
        chrome_options.add_argument('--disable-web-security')
        chrome_options.add_argument('--allow-running-insecure-content')
        
        cls.driver = webdriver.Chrome(options=chrome_options)
        cls.driver.implicitly_wait(5)  # Timeout implícito para elementos
        
        cls.login_url = "http://localhost:8080/SAGreenCash/dashboard/pages/login.php"
        cls.admin_url = "http://localhost:8080/SAGreenCash/dashboard/telaAdmin/views/Painel.php"
        cls.usuarios_url = "http://localhost:8080/SAGreenCash/dashboard/telaAdmin/views/usuarios.php"

    @classmethod
    def tearDownClass(cls):
        cls.driver.quit()

    def test_1_login_admin(self):
        driver = self.driver
        driver.get(self.login_url)
        wait = WebDriverWait(driver, 10)

        # Login com velocidade humana natural
        email_input = wait.until(EC.presence_of_element_located((By.NAME, "email")))
        senha_input = driver.find_element(By.NAME, "senha")
        submit_btn = driver.find_element(By.XPATH, "//form[@id='login-form']//button[@type='submit' and contains(.,'Entrar')]")

        # Preenche email com pequenos delays entre grupos de letras
        email_input.clear()
        for part in ["joao","@" ,"adm", ".", "com"]:
            email_input.send_keys(part)
            time.sleep(0.15)  # Pequena pausa entre grupos
        
        time.sleep(0.3)  # Pausa natural antes do próximo campo
        
        # Preenche senha
        senha_input.clear()
        for part in ["joao", "123"]:
            senha_input.send_keys(part)
            time.sleep(0.12)
        
        time.sleep(0.4)  # Pausa antes de clicar
        submit_btn.click()

        # Aguarda redirecionamento
        wait.until(EC.url_contains("Painel.php"))
        self.assertIn("Painel.php", driver.current_url)

    def test_2_cadastro_usuario(self):
        driver = self.driver
        wait = WebDriverWait(driver, 10)

        # Navega para a tela de usuários
        driver.get(self.usuarios_url)
        
        # Aguarda a página carregar completamente
        wait.until(EC.presence_of_element_located((By.XPATH, "//h2[contains(text(),'Usuários')]")))

        # Abre o modal de cadastro
        cadastrar_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(.,'+ Cadastrar Usuário')]")))
        cadastrar_btn.click()
        
        # Aguarda o modal aparecer
        wait.until(EC.visibility_of_element_located((By.ID, "cadastro-modal")))

        # Preenche o formulário com velocidade humana
        nome_input = wait.until(EC.visibility_of_element_located((By.ID, "cadastro-nome")))
        email_input = driver.find_element(By.ID, "cadastro-email")
        senha_input = driver.find_element(By.ID, "cadastro-senha")

        # Dados para cadastro
        nome = "Usuário Selenium Teste"
        email = f"selenium{int(time.time())}@teste.com"
        senha = "senhaTeste123"

        # Preenche nome em partes naturais
        nome_input.clear()
        for part in ["Usuário ", "Selenium ", "Teste"]:
            nome_input.send_keys(part)
            time.sleep(0.2)
        
        time.sleep(0.3)  # Pausa antes do próximo campo
        
        # Preenche email em partes
        email_input.clear()
        email_parts = email.split('@')
        email_input.send_keys(email_parts[0])  # parte antes do @
        time.sleep(0.15)
        email_input.send_keys('@')
        time.sleep(0.1)
        email_input.send_keys(email_parts[1])  # parte depois do @
        
        time.sleep(0.3)  # Pausa antes da senha
        
        # Preenche senha em partes
        senha_input.clear()
        for part in ["senha", "Teste", "123"]:
            senha_input.send_keys(part)
            time.sleep(0.12)
        
        time.sleep(0.4)  # Pausa antes de salvar

        # Salva o usuário
        salvar_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//div[@class='actions']//button[contains(.,'Salvar')]")))
        salvar_btn.click()
        
        # Aguarda o modal fechar
        wait.until(EC.invisibility_of_element_located((By.ID, "cadastro-modal")))

        # Verifica se o usuário foi adicionado à tabela
        wait.until(EC.presence_of_element_located((By.ID, "usuarios-table")))
        
        # Aguarda um pouco para a tabela atualizar
        time.sleep(0.8)  # Tempo para a tabela carregar
        wait.until(EC.text_to_be_present_in_element((By.TAG_NAME, "tbody"), email))
        
        # Verifica se o email está na tabela
        body = driver.find_element(By.TAG_NAME, "tbody")
        self.assertIn(email, body.text)

    def test_3_logout_admin(self):
        """Teste adicional para logout (opcional)"""
        driver = self.driver
        wait = WebDriverWait(driver, 10)
        
        try:
            # Procura botão de logout
            logout_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[contains(.,'Sair')] | //a[contains(.,'Logout')] | //a[contains(.,'Sair')]")))
            logout_btn.click()
            
            # Verifica se voltou para a página de login
            wait.until(EC.url_contains("login.php"))
            self.assertIn("login.php", driver.current_url)
        except:
            # Se não encontrar botão de logout, navega diretamente para login
            driver.get(self.login_url)
            wait.until(EC.presence_of_element_located((By.NAME, "email")))

if __name__ == "__main__":
    # Executa os testes em ordem
    unittest.main(verbosity=2)