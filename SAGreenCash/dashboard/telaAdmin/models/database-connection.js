const mysql = require('mysql2');

// Configuração da conexão
const connection = mysql.createConnection({
    host: 'localhost', // Endereço do servidor do banco de dados
    user: 'root', // Usuário do banco de dados
    password: '', // Senha do banco de dados
    database: 'SAGreenCash' // Nome do banco
});

// Testando a conexão
connection.connect((err) => {
    if (err) {
        console.error('Erro ao conectar ao banco de dados:', err);
        return;
    }
    console.log('Conexão com o banco de dados bem-sucedida!');
});

module.exports = connection;