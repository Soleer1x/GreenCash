import time
import random
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager

class GreenCashLoginScrollModal:
    def __init__(self):
        self.driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
        self.wait = WebDriverWait(self.driver, 20)
        self.bases_urls = "http://localhost:8080/SAGreenCash/dashboard/pages/"

    def scroll_to_bottom(self):
        self.driver.execute_script("""
            window.scrollTo(0, document.body.scrollHeight);
            document.documentElement.scrollTop = document.documentElement.scrollHeight;
            var y = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
            window.scrollTo(0, y);
        """)
        time.sleep(0.8)

    def scroll_to_element(self, element):
        self.driver.execute_script("arguments[0].scrollIntoView({behavior:'auto', block:'center'});", element)
        time.sleep(0.3)

    def slow_typing(self, element, text, delay=0.08):
        """Digita texto com delay entre caracteres"""
        for char in text:
            element.send_keys(char)
            time.sleep(delay)

    def login(self, username, password):
        self.driver.get(self.bases_urls + "login.php")
        email_elem = self.wait.until(EC.presence_of_element_located((By.NAME, "email")))
        senha_elem = self.driver.find_element(By.NAME, "senha")
        email_elem.clear(); senha_elem.clear()
        
        # Digitação mais lenta
        self.slow_typing(email_elem, username)
        self.slow_typing(senha_elem, password)
        
        self.driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        self.wait.until(EC.url_contains("dashboard.php"))
        
        # Aguarda página carregar completamente
        time.sleep(1)
        
        # Vai para o campo "Gerenciar Finanças" após entrar no dashboard
        try:
            # Procura por elemento que contenha "Gerenciar Finanças"
            gerenciar_element = self.wait.until(EC.presence_of_element_located(
                (By.XPATH, "//*[contains(text(), 'Gerenciar Finanças')]")
            ))
            self.scroll_to_element(gerenciar_element)
            print("Navegou para seção Gerenciar Finanças")
        except Exception as e:
            print(f"Não encontrou 'Gerenciar Finanças', fazendo scroll para baixo: {e}")
            self.scroll_to_bottom()

    def close_welcome_modal(self):
        try:
            btn_later = self.wait.until(EC.element_to_be_clickable((By.ID, "btn-later")))
            btn_later.click()
            self.wait.until(EC.invisibility_of_element_located((By.ID, "welcome-overlay")))
            print("Modal de boas-vindas fechado com sucesso")
        except Exception as e:
            print(f"Não foi possível fechar o modal de boas-vindas: {str(e)}")

    def abrir_modal(self, tipo):
        self.scroll_to_bottom()
        dropdown = self.wait.until(EC.element_to_be_clickable((By.ID, "dropdownGerenciar")))
        self.scroll_to_element(dropdown)
        self.driver.execute_script("arguments[0].click();", dropdown)
        time.sleep(0.2)
        if tipo == "receita":
            texto_menu = "ADD Receitas"
        elif tipo == "despesa":
            texto_menu = "ADD Despesas"
        elif tipo == "plano":
            texto_menu = "ADD Planos"
        else:
            raise Exception("Tipo inválido")
        xpath = f"//a[contains(@class,'dropdown-item') and contains(., '{texto_menu}')]"
        menu_item = self.wait.until(EC.element_to_be_clickable((By.XPATH, xpath)))
        self.scroll_to_element(menu_item)
        self.driver.execute_script("arguments[0].click();", menu_item)
        self.wait.until(lambda d: d.find_element(By.ID, "submenu").value_of_css_property("display") != "none")
        self.scroll_to_bottom()
        time.sleep(0.2)

    def preencher_modal_e_salvar(self, tipo, descricao, valor, prazo=None):
        desc_elem = self.wait.until(EC.visibility_of_element_located((By.ID, "descricao")))
        valor_elem = self.driver.find_element(By.ID, "valor")
        desc_elem.clear(); valor_elem.clear()
        desc_elem.send_keys(descricao)
        valor_elem.send_keys(valor)
        if tipo == "plano":
            prazo_elem = self.driver.find_element(By.ID, "prazo")
            prazo_elem.clear(); prazo_elem.send_keys(str(prazo))

        self.scroll_to_bottom()
        btn_xpath = "//button[contains(text(), 'Salvar')]"
        btns = self.driver.find_elements(By.XPATH, btn_xpath)
        btn = btns[0] if btns else None
        if btn:
            self.scroll_to_element(btn)
            self.scroll_to_bottom()
            self.driver.execute_script("arguments[0].focus(); arguments[0].click();", btn)
            time.sleep(0.5)
        try:
            self.wait.until(lambda d: d.find_element(By.ID, "submenu").value_of_css_property("display") == "none")
        except Exception:
            self.driver.execute_script("if (typeof salvarInformacoes === 'function') salvarInformacoes();")
            self.wait.until(lambda d: d.find_element(By.ID, "submenu").value_of_css_property("display") == "none")
        self.scroll_to_bottom()

    def aguardar_lista(self, tipo, descricao):
        if tipo == "receita":
            cid = "receitas-container"
        elif tipo == "despesa":
            cid = "despesas-container"
        elif tipo == "plano":
            cid = "planos-container"
        else:
            raise Exception("Tipo inválido")
        container = self.wait.until(EC.visibility_of_element_located((By.ID, cid)))
        self.scroll_to_element(container)
        self.wait.until(EC.text_to_be_present_in_element((By.ID, cid), descricao))

    def cadastrar(self, tipo, descricao, valor, prazo=None):
        self.abrir_modal(tipo)
        self.preencher_modal_e_salvar(tipo, descricao, valor, prazo)
        self.aguardar_lista(tipo, descricao)
        print(f"[OK] {tipo.capitalize()} cadastrada: {descricao}")

    def run(self):
        try:
            self.login("usuario@teste.com", "123456")
            self.close_welcome_modal()
            # Receita
            self.cadastrar("receita", f"Receita Teste {random.randint(100,999)}", "1234.56")
            # Despesa
            self.cadastrar("despesa", f"Despesa Teste {random.randint(100,999)}", "789.01")
            # Plano
            self.cadastrar("plano", f"Plano Teste {random.randint(100,999)}", "3200.00", prazo=12)
            print("Todos os cadastros realizados com sucesso!")
        except Exception as e:
            print(f"ERRO: {e}")
        finally:
            time.sleep(2)
            self.driver.quit()

if __name__ == "__main__":
    GreenCashLoginScrollModal().run()