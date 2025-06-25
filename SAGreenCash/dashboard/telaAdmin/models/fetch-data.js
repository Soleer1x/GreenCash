fetch('http://localhost:3000/usuarios')
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Erro:', error));