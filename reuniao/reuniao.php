<?php
include 'conexao.php';

// === EXCLUSÃO via GET ajax ===
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['id']);
    $sql = "DELETE FROM reservas_salas WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit; // para não carregar o resto da página
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <link rel="shortcut icon" href="https://www.jng.com.br/site/img/favicon.ico">
    <title>Reserva de Reunião</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-700 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="bg-white  w-20 flex-shrink-0 flex-col hidden sm:flex">
        <div class="h-16 flex items-center justify-center text-blue-500">
            <img src="https://www.jng.com.br/site/img/logos/logo.svg" width="40" alt="Logo" />
        </div>
        <div class="flex mx-auto flex-grow mt-4 flex-col text-gray-400 space-y-4">
            <a href="../dashboard.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 dark:hover:bg-gray-700 rounded-md">
                <i class="fa fa-home"></i>
            </a>
            <a href="../reserva_carro/reserva.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 dark:hover:bg-gray-700 rounded-md">
                <i class="fa fa-car"></i>
            </a>
            <a href="../visitantes/visitantes.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 dark:hover:bg-gray-700 rounded-md">
                <i class="fa fa-user-friends"></i>
            </a>
            <a href="../reuniao/reuniao.php" class="h-10 w-12 flex items-center justify-center bg-blue-500 text-blue-500 dark:bg-gray-700 dark:text-white rounded-md">
                <i class="fa fa-users"></i>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col">
        <!-- Header -->
        <header class="h-16 flex items-center px-10 bg-white dark:bg-gray-900">
            <div class="nav-links flex h-full text-gray-600 dark:text-gray-400">
                <a href="../dashboard.php" class="inline-flex items-center mr-8">Dashboard</a>
                <a href="../reserva_carro/reserva.php" class="inline-flex items-center mr-8">Reserva de Carro</a>
                <a href="../visitantes/visitantes.php" class="inline-flex items-center mr-8">Reserva de Visitante</a>
                <a href="../reuniao/reuniao.php" class="inline-flex items-center mr-8">Reserva de Reunião</a>
            </div>

            <div class="ml-auto flex items-center space-x-7">
                <div class="dark-mode-toggle" id="modeToggle" role="button" aria-label="Alternar modo claro/escuro" tabindex="0">
                    <span class="icon-sun icon">🌞</span>
                    <span class="icon-moon icon">🌙</span>
                    <div class="toggle-circle"></div>
                </div>
            </div>

        </header>

        <!-- Conteúdo principal -->
        <section class="flex-grow bg-gray-100 dark:bg-gray-900 overflow-y-auto p-8">
            <main class="content">
                <div class="header-bar">
                    <h1>Reserva de Reunião</h1>
                    <div class="search-container">
                        <input type="text" id="search-input" placeholder="🔍 Pesquisar por nome, email, telefone..." />
                    </div>
                </div>


                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Sala</th>
                            <th>Data</th>
                            <th>Hora Início</th>
                            <th>Hora Fim</th>
                            <th>Participantes</th>
                            <th>Assunto</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM reservas_salas ORDER BY id DESC";
                        $res = $conn->query($sql);
                        while ($row = $res->fetch_assoc()) {
                            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['nome']}</td>
                <td>{$row['email']}</td>
                <td>{$row['telefone']}</td>
                <td>{$row['sala']}</td>
                <td>{$row['data']}</td>
                <td>{$row['hora_inicio']}</td>
                <td>{$row['hora_fim']}</td>
                <td>{$row['participantes']}</td>
                <td>{$row['assunto']}</td>
                <td>
                    <button class='edit-btn' data-id='{$row['id']}'>✏️</button>
                    <button class='delete-btn' data-id='{$row['id']}'>🗑️</button>
                </td>
            </tr>";
                        }
                        ?>
                    </tbody>
                </table>

            </main>
            </div>

            <!-- Modal confirmação abrir edição -->
            <div id="confirm-modal" class="modal-overlay">
                <div class="modal-box">
                    <p class="modal-message">CERTEZA QUE DESEJA EDITAR ESTE REGISTRO?</p>
                    <div class="modal-buttons">
                        <button id="confirm-yes" class="btn yes">Sim</button>
                        <button id="confirm-no" class="btn no">Não</button>
                    </div>
                </div>
            </div>

            <!-- Modal edição -->
            <div id="edit-modal" class="modal-overlay">
                <div class="modal-content">
                    <form id="edit-form" method="POST" action="editar.php">
                        <input type="hidden" name="id" id="edit-id">
                        <label>Nome: <input type="text" name="nome" id="edit-nome" required></label>
                        <label>Email: <input type="email" name="email" id="edit-email" required></label>
                        <label>Telefone: <input type="text" name="telefone" id="edit-telefone" required></label>
                        <label>Sala:
                            <select name="sala" id="edit-sala" required>
                                <option value="" disabled selected>Selecione a sala</option>
                                <option value="Sala 1">Sala 1, 18 Lugares</option>
                                <option value="Sala 2">Sala 2, 7 Lugares</option>
                                <option value="Sala 3">Sala 3, 4 Lugares</option>
                            </select>
                        </label>
                        <label>Data: <input type="date" name="data" id="edit-data" required></label>
                        <label>Hora Início: <input type="time" name="hora_inicio" id="edit-hora_inicio" required></label>
                        <label>Hora Fim: <input type="time" name="hora_fim" id="edit-hora_fim" required></label>
                        <label>Participantes: <input type="number" name="participantes" id="edit-participantes"></label>
                        <label>Assunto: <textarea name="assunto" id="edit-assunto" required></textarea></label>

                        <div class="form-actions">
                            <button type="submit">Salvar</button>
                            <button type="button" onclick="fecharModal()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal confirmação salvar edição -->
            <div id="confirm-edit-modal" class="modal-overlay">
                <div class="modal-box">
                    <p class="modal-message">CERTEZA QUE DESEJA SALVAR AS ALTERAÇÕES?</p>
                    <div class="modal-buttons">
                        <button id="confirm-edit-yes" class="btn yes">Sim</button>
                        <button id="confirm-edit-no" class="btn no">Não</button>
                    </div>
                </div>
            </div>

            <!-- Modal confirmação exclusão -->
            <div id="delete-modal" class="modal-overlay">
                <div class="modal-box">
                    <p class="modal-message">CERTEZA QUE DESEJA EXCLUIR?</p>
                    <div class="modal-buttons">
                        <button id="delete-yes" class="btn yes">Sim</button>
                        <button id="delete-no" class="btn no">Não</button>
                    </div>
                </div>
            </div>

        </section>
    </main>

    <script>
        let rowToEdit = null;

        // Abrir modal confirmação antes de abrir modal edição
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                rowToEdit = button.closest('tr');
                document.getElementById('confirm-modal').style.display = 'flex';
            });
        });

        document.getElementById('confirm-yes').addEventListener('click', () => {
            const row = rowToEdit;
            document.getElementById('edit-id').value = row.children[0].textContent.trim();
            document.getElementById('edit-nome').value = row.children[1].textContent.trim();
            document.getElementById('edit-email').value = row.children[2].textContent.trim();
            document.getElementById('edit-telefone').value = row.children[3].textContent.trim();
            document.getElementById('edit-sala').value = row.children[4].textContent.trim();
            document.getElementById('edit-data').value = row.children[5].textContent.trim();
            document.getElementById('edit-hora_inicio').value = row.children[6].textContent.trim();
            document.getElementById('edit-hora_fim').value = row.children[7].textContent.trim();
            document.getElementById('edit-participantes').value = row.children[8].textContent.trim();
            document.getElementById('edit-assunto').value = row.children[9].textContent.trim();

            fecharModal();
            abrirModal('edit-modal');
        });


        document.getElementById('confirm-no').addEventListener('click', () => {
            fecharModal();
        });

        // Modal exclusão
        let rowToDelete = null;
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                rowToDelete = button.closest('tr');
                abrirModal('delete-modal');
            });
        });

        document.getElementById('delete-no').addEventListener('click', () => {
            fecharModal();
        });

        document.getElementById('delete-yes').addEventListener('click', () => {
            const id = rowToDelete.children[0].textContent.trim();
            fetch(`?acao=excluir&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        rowToDelete.remove();
                        alert('Excluído com sucesso!');
                    } else {
                        alert('Erro ao excluir: ' + (data.error || 'Erro desconhecido'));
                    }
                    fecharModal();
                })
                .catch(() => {
                    alert('Erro na requisição');
                    fecharModal();
                });
        });

        // Modal confirmação salvar edição
        const editForm = document.getElementById('edit-form');
        const confirmEditModal = document.getElementById('confirm-edit-modal');
        const btnConfirmYes = document.getElementById('confirm-edit-yes');
        const btnConfirmNo = document.getElementById('confirm-edit-no');

        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            confirmEditModal.style.display = 'flex';
        });

        btnConfirmNo.addEventListener('click', () => {
            confirmEditModal.style.display = 'none';
        });

        btnConfirmYes.addEventListener('click', () => {
            const formData = new FormData(editForm);

            fetch(editForm.action, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const id = formData.get('id');
                        document.querySelectorAll('tbody tr').forEach(row => {
                            if (row.children[0].textContent.trim() === id) {
                                row.children[1].textContent = formData.get('nome');
                                row.children[2].textContent = formData.get('email');
                                row.children[3].textContent = formData.get('telefone');
                                row.children[4].textContent = formData.get('sala');
                                row.children[5].textContent = formData.get('data');
                                row.children[6].textContent = formData.get('hora_inicio');
                                row.children[7].textContent = formData.get('hora_fim');
                                row.children[8].textContent = formData.get('participantes');
                                row.children[9].textContent = formData.get('assunto');
                            }
                        });

                        alert('Editado com sucesso!');
                        fecharModal();
                    } else {
                        alert('Erro: ' + (data.error || 'Erro desconhecido'));
                    }
                    confirmEditModal.style.display = 'none';
                })
                .catch(() => {
                    alert('Erro na requisição');
                    confirmEditModal.style.display = 'none';
                });
        });

        function abrirModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function fecharModal() {
            document.querySelectorAll('.modal-overlay').forEach(modal => modal.style.display = 'none');
        }


        document.getElementById('search-input').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>

    <script>
        function toggleDarkMode() {
            const body = document.body;
            const isDark = body.classList.toggle('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }

        window.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
            }

            document.getElementById('modeToggle').addEventListener('click', toggleDarkMode);

            // Toggle também via teclado (acessibilidade)
            document.getElementById('modeToggle').addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleDarkMode();
                }
            });
        });
    </script>
</body>

</html>