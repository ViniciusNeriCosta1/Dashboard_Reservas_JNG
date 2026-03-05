<?php
include 'conexao.php';

// === EXCLUSÃO via GET ajax ===
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['id']);
    $sql = "DELETE FROM visitantes WHERE id = $id";
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
    <title>Reserva de Visitante</title>
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
            <a href="../visitantes/visitantes.php" class="h-10 w-12 flex items-center justify-center bg-blue-500 text-blue-500 dark:bg-gray-700 dark:text-white rounded-md">
                <i class="fa fa-user-friends"></i>
            </a>
            <a href="../reuniao/reuniao.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 dark:hover:bg-gray-700 rounded-md">
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
                <a href="./visitantes.php" class="inline-flex items-center mr-8">Reserva de Visitante</a>
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
                    <h1>Reserva de Visitante</h1>
                    <div class="search-container">
                        <input type="text" id="search-input" placeholder="🔍 Pesquisar por nome, tipo doc, data..." />
                    </div>
                </div>


                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Tipo Doc</th>
                            <th>Nº Documento</th>
                            <th>Data Cadastro</th>
                            <th>Data Visita</th>
                            <th>Visitado</th>
                            <th>Ramal</th>
                            <th>Check-in</th>
                            <th>Ações</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM visitantes ORDER BY id DESC";
                        $res = $conn->query($sql);
                        while ($row = $res->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['nome']}</td>
                                <td>{$row['tipo_documento']}</td>
                                <td>{$row['numero_documento']}</td>
                                <td>{$row['data_cadastro']}</td>
                                <td>{$row['data_visita']}</td>
                                <td>{$row['visitado']}</td>
                                <td>{$row['ramal']}</td>
                                <td>{$row['checkin']}</td>
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

            <!-- Modal edição -->
            <div id="edit-modal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="modal-content bg-white text-black p-8 rounded-xl shadow-lg w-full max-w-3xl max-h-screen overflow-y-auto">
                    <form id="edit-form" method="POST" action="editar.php" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" name="id" id="edit-id" />

                        <label class="block col-span-1">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Nome:</span>
                            <input type="text" name="nome" id="edit-nome" required class="w-full border border-gray-300 rounded-md px-4 py-2" />
                        </label>

                        <label class="block col-span-1">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Tipo Documento:</span>
                            <select name="tipo_documento" id="edit-tipo_documento" required class="w-full border border-gray-300 rounded-md px-4 py-2 bg-white">
                                <option value="RG">RG</option>
                                <option value="CPF">CPF</option>
                                <option value="CNH">CNH</option>
                            </select>
                        </label>

                        <label class="block col-span-1">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Nº Documento:</span>
                            <input type="text" name="numero_documento" id="edit-numero_documento" required class="w-full border border-gray-300 rounded-md px-4 py-2" />
                        </label>

                        <label class="block col-span-1">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Data Visita:</span>
                            <input type="date" name="data_visita" id="edit-data_visita" class="w-full border border-gray-300 rounded-md px-4 py-2" />
                        </label>

                        <label class="block col-span-1">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Visitado:</span>
                            <input type="text" name="visitado" id="edit-visitado" class="w-full border border-gray-300 rounded-md px-4 py-2" />
                        </label>

                        <label class="block col-span-1">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Ramal:</span>
                            <input type="text" name="ramal" id="edit-ramal" class="w-full border border-gray-300 rounded-md px-4 py-2" />
                        </label>

                        <label class="block col-span-2">
                            <span class="block text-sm font-medium text-gray-700 mb-1">Check-in:</span>
                            <input type="text" name="checkin" id="edit-checkin" class="w-full border border-gray-300 rounded-md px-4 py-2" />
                        </label>

                        <div class="form-actions col-span-2 flex justify-end gap-4 pt-4">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Salvar</button>
                            <button type="button" onclick="fecharModal()" class="px-6 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400 transition">Cancelar</button>
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
            <div id="delete-modal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="modal-box bg-white text-black p-6 rounded-xl shadow-lg w-full max-w-md">
                    <p class="modal-message text-xl font-semibold text-center text-gray-800 mb-6">
                        CERTEZA QUE DESEJA EXCLUIR?
                    </p>
                    <div class="modal-buttons flex justify-center gap-6">
                        <button id="delete-yes" class="btn yes px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">Sim</button>
                        <button id="delete-no" class="btn no px-6 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">Não</button>
                    </div>
                </div>
            </div>
            <!-- Modal confirmação abrir edição -->
            <div id="confirm-modal" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="modal-box bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
                    <p class="modal-message text-lg font-semibold text-center text-gray-800 mb-4">CERTEZA QUE DESEJA EDITAR ESTE REGISTRO?</p>
                    <div class="modal-buttons flex justify-end">
                        <button id="confirm-yes" class="btn yes px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition">Sim</button>
                        <button id="confirm-no" class="btn no px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition">Não</button>
                    </div>
                </div>
            </div>


            <!-- Modal edição -->


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
            // Preenche formulário edição com dados da linha
            document.getElementById('edit-id').value = row.children[0].textContent.trim();
            document.getElementById('edit-nome').value = row.children[1].textContent.trim();
            document.getElementById('edit-tipo_documento').value = row.children[2].textContent.trim();
            document.getElementById('edit-numero_documento').value = row.children[3].textContent.trim();
            document.getElementById('edit-data_visita').value = row.children[5].textContent.trim();
            document.getElementById('edit-visitado').value = row.children[6].textContent.trim();
            document.getElementById('edit-ramal').value = row.children[7].textContent.trim();
            document.getElementById('edit-checkin').value = row.children[8].textContent.trim();


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
                                row.children[2].textContent = formData.get('tipo_documento');
                                row.children[3].textContent = formData.get('numero_documento');
                                row.children[4].textContent = formData.get('data_visita');
                                row.children[5].textContent = formData.get('visitado');
                                row.children[6].textContent = formData.get('ramal');
                                row.children[7].textContent = formData.get('checkin');
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