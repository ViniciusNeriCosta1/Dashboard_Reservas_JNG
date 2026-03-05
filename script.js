let rowToEdit = null;

document.querySelectorAll('.edit-btn').forEach(button => {
  button.addEventListener('click', () => {
    rowToEdit = button.closest('tr');
    document.getElementById('confirm-modal').style.display = 'flex';
  });
});

document.getElementById('confirm-yes').addEventListener('click', () => {
  const row = rowToEdit;

  document.getElementById('edit-id').value = row.children[0].textContent;
  document.getElementById('edit-nome').value = row.children[1].textContent;
  document.getElementById('edit-carro').value = row.children[2].textContent;
  document.getElementById('edit-ramal').value = row.children[3].textContent;
  document.getElementById('edit-data').value = row.children[4].textContent;
  document.getElementById('edit-motivo').value = row.children[5].textContent;
  document.getElementById('edit-periodo').value = row.children[6].textContent;
  document.getElementById('edit-email').value = row.children[7].textContent;

  document.getElementById('confirm-modal').style.display = 'none';
  document.getElementById('edit-modal').style.display = 'flex';
  rowToEdit = null;
});

document.getElementById('confirm-no').addEventListener('click', () => {
  document.getElementById('confirm-modal').style.display = 'none';
  rowToEdit = null;
});

function fecharModal() {
  document.getElementById('edit-modal').style.display = 'none';
}


let rowToDelete = null;
let deleteId = null;

document.querySelectorAll('.delete-btn').forEach(button => {
  button.addEventListener('click', (e) => {
    e.preventDefault();  // **CANCELA o redirecionamento imediato**

    rowToDelete = button.closest('tr');
    deleteId = button.getAttribute('href').split('id=')[1]; // pega o id do link

    document.getElementById('delete-modal').style.display = 'flex';
  });
});

document.getElementById('delete-yes').addEventListener('click', () => {
  if (deleteId) {
    window.location.href = `excluir.php?id=${encodeURIComponent(deleteId)}`;
  }
});

document.getElementById('delete-no').addEventListener('click', () => {
  document.getElementById('delete-modal').style.display = 'none';
  rowToDelete = null;
  deleteId = null;
});



