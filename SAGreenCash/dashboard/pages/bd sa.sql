-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS greencash CHARACTER SET utf8mb4;
USE greencash;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  nome VARCHAR(100) NOT NULL,
  tipo TINYINT(1) NOT NULL DEFAULT 0,             -- 0=usuário, 1=admin
  telefone VARCHAR(20) DEFAULT NULL,
  localizacao VARCHAR(100) DEFAULT NULL,
  bio VARCHAR(255) DEFAULT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  plano VARCHAR(30) DEFAULT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,            -- 1=ativo, 0=inativo
  dataCadastro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuários comuns exemplo
INSERT INTO usuarios (email, senha, nome, tipo, ativo, dataCadastro) VALUES
('usuario@teste.com', MD5('123456'), 'Usuário Teste', 0, 1, NOW()),
('usuario@testa.com', MD5('1234567'), 'Usuário Testa', 0, 1, NOW())
ON DUPLICATE KEY UPDATE nome=VALUES(nome), senha=VALUES(senha);

-- Tabela de administradores
CREATE TABLE IF NOT EXISTS adm (
  id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(100) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL,
  nome VARCHAR(100),
  tipo TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Administradores exemplo
INSERT INTO adm (email, senha, nome, tipo) VALUES
('diego@adm.com', MD5('diego123'), 'Diego', 1),
('joao@adm.com', MD5('joao123'), 'João', 1),
('marcos@adm.com', MD5('marcos123'), 'Marcos', 1),
('tiago@adm.com', MD5('tiago123'), 'Tiago', 1)
ON DUPLICATE KEY UPDATE nome=VALUES(nome), senha=VALUES(senha);

-- Tabela de cartões
CREATE TABLE IF NOT EXISTS cartoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id int NOT NULL,      -- Corrigido aqui!
    numero VARCHAR(20) NOT NULL,
    titular VARCHAR(100) NOT NULL,
    validade VARCHAR(7) NOT NULL,
    cvv VARCHAR(4) NOT NULL,
    salario DECIMAL(10,2) NOT NULL,
    limite DECIMAL(10,2) NOT NULL,
    tipo VARCHAR(30) NOT NULL,
    principal TINYINT DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de suporte
CREATE TABLE IF NOT EXISTS suporte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    prioridade VARCHAR(10) DEFAULT 'Média',
    status VARCHAR(20) DEFAULT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de respostas de suporte
CREATE TABLE IF NOT EXISTS suporte_resposta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    suporte_id INT NOT NULL,
    admin_id INT,
    mensagem TEXT NOT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacao VARCHAR(255),
    prioridade VARCHAR(10) DEFAULT 'Média',
    FOREIGN KEY (suporte_id) REFERENCES suporte(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES adm(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de log de atividades administrativas
CREATE TABLE IF NOT EXISTS log_atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    acao VARCHAR(255),
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES adm(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de receitas financeiras
CREATE TABLE IF NOT EXISTS receitas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  cartao_id INT NULL,
  cartao_numero VARCHAR(24) NULL,
  excluido TINYINT(1) DEFAULT 0,
  data DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de despesas financeiras (CORRIGIDA)
CREATE TABLE IF NOT EXISTS despesas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  pago TINYINT DEFAULT 0,
  cartao_id INT NULL,
  cartao_numero VARCHAR(24) NULL,
  excluido TINYINT(1) DEFAULT 0,
  origem_pagamento VARCHAR(10) DEFAULT NULL,  -- <== NOVO CAMPO
  data DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de planos financeiras/metas (CORRIGIDA)
CREATE TABLE IF NOT EXISTS planos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  prazo INT NOT NULL,
  realizado TINYINT DEFAULT 0,
  cartao_id INT NULL,
  cartao_numero VARCHAR(24) NULL,
  excluido TINYINT(1) DEFAULT 0,
  origem_pagamento VARCHAR(10) DEFAULT NULL,  -- <== NOVO CAMPO
  data DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Consultas de teste
SELECT * FROM usuarios;
SELECT * FROM adm;
SELECT * FROM cartoes;
SELECT * FROM suporte;
SELECT * FROM suporte_resposta;
SELECT * FROM log_atividades;