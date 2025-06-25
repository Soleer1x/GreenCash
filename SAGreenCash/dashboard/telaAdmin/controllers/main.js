// Adicionando a classe que muda cor ao passar o mouse
let list = document.querySelectorAll(".navigation li");

function activeLink() {
  list.forEach((item) => {
    item.classList.remove("hovered");
  });
  this.classList.add("hovered");
}

list.forEach((item) => item.addEventListener("mouseover", activeLink));

// Menu Toggle para recolher e mostar a navegação
let toggle = document.querySelector(".toggle");
let navigation = document.querySelector(".navigation");
let main = document.querySelector(".main");

toggle.onclick = function () {
  navigation.classList.toggle("active");
  main.classList.toggle("active");
  const db = firebase.firestore();
  const storage = firebase.storage();
  
  // Open cadastro modal
  function openCadastroModal() {
      document.querySelector('.cadastro-modal-overlay').classList.add('active');
  }
  
  // Close cadastro modal
  function closeCadastroModal() {
      document.querySelector('.cadastro-modal-overlay').classList.remove('active');
  }
  
  // Submit cadastro form
  async function submitCadastroForm(event) {
      event.preventDefault();
      const nome = document.getElementById('usuario-nome').value;
      const email = document.getElementById('usuario-email').value;
      const fotoInput = document.getElementById('usuario-foto').files[0];
  
      let fotoUrl = '';
      if (fotoInput) fotoUrl = await uploadImage(fotoInput);
  
      try {
          await db.collection('usuarios').add({ nome, email, foto: fotoUrl });
          alert('Usuário cadastrado com sucesso!');
          closeCadastroModal();
          loadUsuarios();
      } catch (err) {
          console.error('Erro ao cadastrar usuário:', err);
          alert('Erro ao cadastrar usuário.');
      }
  }
  
  // Carregar usuários
  async function loadUsuarios() {
      const tbody = document.querySelector('#usuarios-table tbody');
      tbody.innerHTML = '';
  
      const snapshot = await db.collection('usuarios').get();
      snapshot.forEach(doc => {
          const { nome, email, foto } = doc.data();
          tbody.innerHTML += `
              <tr>
                  <td><img src="${foto || '../views/imgs/user-placeholder.png'}" alt="Foto" class="user-image"></td>
                  <td>${nome}</td>
                  <td>${email}</td>
                  <td>
                      <button onclick="openEditModal('${doc.id}', '${nome}', '${email}', '${foto || ''}')">Editar</button>
                      <button onclick="deleteUsuario('${doc.id}')">Excluir</button>
                  </td>
              </tr>
          `;
      });
      document.getElementById('totalUsuariosDisplay').innerText = snapshot.size;
  }
  
  // Upload image
  async function uploadImage(file) {
      const storageRef = storage.ref(`usuarios/${file.name}`);
      await storageRef.put(file);
      return await storageRef.getDownloadURL();
  }
  
  // Delete usuário
  async function deleteUsuario(id) {
      if (confirm('Tem certeza que deseja excluir este usuário?')) {
          await db.collection('usuarios').doc(id).delete();
          alert('Usuário excluído com sucesso!');
          loadUsuarios();
      }
  }
  
  // Open edit modal
  function openEditModal(id, nome, email, foto) {
      document.querySelector('.edit-modal-overlay').classList.add('active');
      document.getElementById('edit-nome').value = nome;
      document.getElementById('edit-email').value = email;
      document.querySelector('.edit-modal').dataset.userId = id;
      document.querySelector('.edit-modal').dataset.userFoto = foto;
  }
  
  // Close edit modal
  function closeEditModal() {
      document.querySelector('.edit-modal-overlay').classList.remove('active');
  }
  
  // Submit edit form
  async function submitEditForm(event) {
      event.preventDefault();
      const id = document.querySelector('.edit-modal').dataset.userId;
      const nome = document.getElementById('edit-nome').value;
      const email = document.getElementById('edit-email').value;
      const fotoInput = document.getElementById('edit-foto').files[0];
  
      let fotoUrl = document.querySelector('.edit-modal').dataset.userFoto;
      if (fotoInput) fotoUrl = await uploadImage(fotoInput);
  
      try {
          await db.collection('usuarios').doc(id).update({ nome, email, foto: fotoUrl });
          alert('Usuário atualizado com sucesso!');
          closeEditModal();
          loadUsuarios();
      } catch (err) {
          console.error('Erro ao editar usuário:', err);
          alert('Erro ao editar usuário.');
      }
  }
  
  loadUsuarios();
};