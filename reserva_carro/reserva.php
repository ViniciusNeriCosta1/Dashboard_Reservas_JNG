<?php
session_start();
include 'conexao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


// === EXCLUSÃO via GET ajax ===
if (isset($_GET['acao']) && $_GET['acao'] === 'excluir' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    $id = intval($_GET['id']);
    $sql = "DELETE FROM cadastro WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
}

//Função para informar manutenção do carro
$acao = $_POST['acao'] ?? '';

// === Buscar todos os registros de cadastro com dados do gestor ===
$sql = "
SELECT 
    c.*, 
    g.nome_gestor, 
    g.email_gestor, 
    g.apelido 
FROM cadastro c
LEFT JOIN gestores g ON c.id_gestores = g.id
ORDER BY c.id DESC
";
$res = $conn->query($sql);

// === Buscar todos os gestores para selects ===
$gestoresResult = $conn->query("SELECT * FROM gestores ORDER BY id ASC");
$gestores = [];
while ($g = $gestoresResult->fetch_assoc()) {
    $gestores[] = $g;
}

// === Buscar todos os carros para selects ===
$carrosResult = $conn->query("SELECT * FROM carros ORDER BY id ASC");
$carros = [];
while ($c = $carrosResult->fetch_assoc()) {
    $carros[] = $c;
}

// === Função de display de status de carro ===
function statusCarro($c) {
    $hoje = date("Y-m-d");

    if (
        $c['status'] == 2 &&
        !empty($c['dt_manu_inicio']) &&
        !empty($c['dt_manu_fim']) &&
        $c['dt_manu_inicio'] <= $hoje &&
        $c['dt_manu_fim'] >= $hoje
    ) {
        return ' (EM MANUTENÇÃO)';
    }

    if ($c['status'] == 0) {
        return ' (INDISPONÍVEL)';
    }

    return '';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <link rel="shortcut icon" href="https://www.jng.com.br/site/img/favicon.ico">
    <title>Reserva de Carros</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        /* ===== Modal ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.5);
            z-index: 50;
        }

        .modal-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 100%;
        }

        .modal-buttons button {
            margin: 0 8px;
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
        }


        .bloqueio-info {
            font-size: 14px;
            color: #d9534f;
            /* vermelho */
            font-weight: 500;
            margin-top: 5px;
            display: block;
        }


        .status-veiculos-title {
            text-align: center;
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: bold;
            color: var(--text, #333);
        }

        .status-veiculos-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .status-veiculos-item {
            background: var(--card-bg, #fff);
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .status-veiculos-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        }

        .status-veiculos-item strong {
            color: #007bff;
            font-weight: bold;
        }

        .status-veiculos-item strong:contains("BLOQUEADO") {
            color: #d9534f;
            /* vermelho se bloqueado */
        }

        /* Formulário dentro do card */
        .status-veiculos-item form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .status-veiculos-item label {
            font-size: 14px;
            color: #555;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .status-veiculos-item input[type="datetime-local"] {
            padding: 6px 10px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .status-veiculos-toggle-btn {
            background: #007bff;
            border: none;
            color: #fff;
            margin-top: 10px;
            padding: 8px 14px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .status-veiculos-toggle-btn:hover {
            background: #0056b3;
        }

        .status-veiculos-toggle-btn[style*="background:#FF0000"] {
            background: #d9534f !important;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-700 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="bg-white w-20 flex-shrink-0 flex-col hidden sm:flex">
        <div class="h-16 flex items-center justify-center text-blue-500">
            <img src="https://www.jng.com.br/site/img/logos/logo.svg" width="40" alt="Logo" />
        </div>
        <div class="flex mx-auto flex-grow mt-4 flex-col text-gray-400 space-y-4">
            <a href="../dashboard.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 rounded-md"><i class="fa fa-home"></i></a>
            <a href="../reserva_carro/reserva.php" class="h-10 w-12 flex items-center justify-center bg-blue-500 text-blue-500 rounded-md"><i class="fa fa-car"></i></a>
            <a href="../visitantes/visitantes.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 rounded-md"><i class="fa fa-user-friends"></i></a>
            <a href="../reuniao/reuniao.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 rounded-md"><i class="fa fa-users"></i></a>
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
            <h1 class="text-2xl mb-4 font-semibold">Reserva de Carros</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- ========== 🔹 GESTORES ========== -->
                <div class="p-4 border border-slate-300 dark:border-slate-700 rounded-lg flex flex-col h-full">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">
                        Editar Gestores
                    </h3>

                    <select id="gestor"
                        class="w-full border rounded p-2 bg-white dark:bg-slate-800 dark:text-white">
                        <option value="" disabled selected>Selecione o gestor</option>
                        <?php foreach ($gestores as $g): ?>
                            <option
                                value="<?= $g['id'] ?>"
                                data-nome="<?= htmlspecialchars($g['nome_gestor']) ?>"
                                data-email="<?= htmlspecialchars($g['email_gestor']) ?>">
                                <?= $g['nome_gestor'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="mt-3 mb-2 flex gap-2">
                        <button onclick="App.Gestores.editar()"
                            class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">
                            Editar
                        </button>
                        <button onclick="App.Gestores.apagar()"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                            Apagar
                        </button>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">
                        Adicionar Gestores
                    </h3>

                    <div class="flex flex-col gap-2 mb-2">
                        <input id="novo_gestor"
                            type="text"
                            placeholder="Nome do gestor"
                            class="border rounded p-2 dark:bg-slate-800 dark:text-white">

                        <input id="email_gestor"
                            type="email"
                            placeholder="Email do gestor"
                            class="border rounded p-2 dark:bg-slate-800 dark:text-white">
                    </div>

                    <button onclick="App.Gestores.adicionar()"
                        class="mt-auto bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded w-fit">
                        Adicionar
                    </button>
                </div>

                <!-- ========== 🔹 VEÍCULOS ========== -->
                <div class="p-4 border border-slate-300 dark:border-slate-700 rounded-lg flex flex-col h-full">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">
                        Veículos
                    </h3>

                    <select id="carro"
                        class="w-full border rounded p-2 bg-white dark:bg-slate-800 dark:text-white">
                        <option value="" disabled selected>Selecione o veículo</option>
                        <?php foreach ($carros as $c): ?>
                            <option
                                value="<?= $c['id'] ?>"
                                data-nome="<?= htmlspecialchars($c['nome_carro']) ?>"
                                data-display="<?= htmlspecialchars($c['display_carro']) ?>">
                                <?= $c['nome_carro'] ?> (<?= $c['display_carro'] ?>)
                                <?= statusCarro($c) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>


                    <div class="mt-3 mb-2 flex gap-2">
                        <button onclick="App.Veiculos.editar()"
                            class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded">
                            Editar
                        </button>
                        <button onclick="App.Veiculos.apagar()"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                            Apagar
                        </button>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">
                        Adicionar Carro
                    </h3>

                    <div class="flex flex-col gap-2 mb-2">
                        <input id="nome_carro"
                            type="text"
                            placeholder="Nome do veículo"
                            class="w-full border rounded p-1">
                        <input id="display_carro"
                            type="text"
                            placeholder="Display do veículo"
                            class="border rounded p-2 dark:bg-slate-800 dark:text-white">
                    </div>

                    <button onclick="App.Veiculos.adicionar()"
                        class="mt-auto bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded w-fit">
                        Adicionar
                    </button>
                </div>

                <!-- ========== 🔹 MANUTENÇÃO ========== -->
                <div class="p-4 border border-slate-300 dark:border-slate-700 rounded-lg flex flex-col h-full">

                    <!-- ===== INDISPONIBILIDADE ===== -->
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-2">
                        Informar Indisponibilidade Veículo
                    </h3>

                    <select id="indisponivel_carro"
                        class="w-full border rounded p-2 mb-3 bg-white dark:bg-slate-800 dark:text-white">
                        <option value="" disabled selected>Selecione o veículo</option>
                        <?php foreach ($carros as $c): ?>
                            <option
                                value="<?= $c['id'] ?>"
                                data-status="<?= $c['status'] ?>">
                                <?= $c['nome_carro'] ?> (<?= $c['display_carro'] ?>)
                                <?= statusCarro($c) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="mb-2 flex gap-2">
                        <button
                            onclick="App.Manutencao.indisponivel()"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                            Indisponível
                        </button>

                        <button
                            onclick="App.Manutencao.disponivel()"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                            Disponível
                        </button>
                    </div>

                    <!-- ===== MANUTENÇÃO ===== -->
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mt-4 mb-2">
                        Informar Manutenção Veículo
                    </h3>

                    <select id="manutencao_carro"
                        class="w-full border rounded p-2 mb-3 bg-white dark:bg-slate-800 dark:text-white">
                        <option value="" disabled selected>Selecione o veículo</option>
                        <?php foreach ($carros as $c): ?>
                            <option
                                value="<?= $c['id'] ?>"
                                data-status="<?= $c['status'] ?>">
                                <?= $c['nome_carro'] ?> (<?= $c['display_carro'] ?>)
                                <?= statusCarro($c) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="flex gap-2 mb-3">
                        <input type="date" id="manutencao_inicio" class="border rounded p-2 w-1/2">
                        <input type="date" id="manutencao_fim" class="border rounded p-2 w-1/2">
                    </div>

                    <button
                        onclick="App.Manutencao.manutencao()"
                        class="mt-auto bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded w-fit">
                        Colocar em Manutenção
                    </button>

                </div>

            </div>

            <!-- Status Veículos -->
            <h2 class="status-veiculos-title mt-5">Gerenciar Agendamentos</h2>

            <!-- Tabela -->
            <div class="table-wrapper">
                <input type="text" id="search-input" placeholder="🔍 Pesquisar..." class="border rounded p-1 mb-2 w-full">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Carro</th>
                            <th>Ramal</th>
                            <th>Data</th>
                            <th>Motivo</th>
                            <th>Período</th>
                            <th>Email</th>
                            <th>Aprovado</th>
                            <th>Gestor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $res->fetch_assoc()): ?>
                            <tr data-id="<?= $row['id'] ?>" data-gestor="<?= $row['id_gestores'] ?>">
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['nome'] ?></td>
                                <td><?= $row['carro'] ?></td>
                                <td><?= $row['ramal'] ?></td>
                                <td><?= $row['data'] ?></td>
                                <td><?= $row['motivo'] ?></td>
                                <td><?= $row['periodo'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['aprovado'] ? 'Sim' : 'Não' ?></td>
                                <td><?= $row['nome_gestor'] ?></td>
                                <td>
                                    <button class="edit-btn px-2 py-1 bg-yellow-400 rounded">✏️</button>
                                    <button class="delete-btn px-2 py-1 bg-red-500 text-white rounded">🗑️</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div id="toast" class="hidden"></div>

        <!-- Modal Genérico -->
        <div id="modal" class="modal-overlay flex">
            <div class="modal-box">
                <p id="modal-message"></p>
                <div class="modal-buttons text-center mt-4">
                    <button id="modal-yes" class="bg-green-600 text-white">Sim</button>
                    <button id="modal-no" class="bg-red-600 text-white">Não</button>
                </div>
            </div>
        </div>

        <!-- Modal Padrão -->
        <div id="modalBase"
            class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg w-full max-w-lg p-6">
                <h3 id="modalTitulo"
                    class="text-lg font-semibold mb-4 text-slate-800 dark:text-white">
                </h3>

                <div id="modalConteudo"></div>

                <div class="flex justify-end gap-2 mt-4">
                    <button onclick="fecharModal()"
                        class="px-3 py-1 rounded bg-slate-400 hover:bg-slate-500 text-white">
                        Cancelar
                    </button>

                    <button id="modalConfirmar"
                        class="px-3 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white">
                        Salvar
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Editar agendamento -->
        <div id="edit-modal" class="modal-overlay flex">
            <div class="modal-box overflow-y-auto max-h-[90vh]">
                <form id="edit-form" class="grid grid-cols-1 md:grid-cols-2 gap-4" method="POST" action="editar.php">
                    <input type="hidden" name="id" id="edit-id">
                    <label>Nome:<input type="text" name="nome" id="edit-nome" required class="w-full border rounded p-1"></label>
                    <label>Carro:
                        <select name="carro" id="edit-carro" required class="w-full border rounded p-1">
                            <?php foreach ($carros as $c): ?>
                                <option
                                    value="<?= $c['id'] ?>"
                                    data-nome="<?= htmlspecialchars($c['nome_carro']) ?>"
                                    data-display="<?= htmlspecialchars($c['display_carro']) ?>">
                                    <?= $c['nome_carro'] ?> (<?= $c['display_carro'] ?>)
                                    <?= $c['status'] == 2 ? ' (EM MANUTENÇÃO)' : ($c['status'] == 0 ? ' (INDISPONÍVEL)' : '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Ramal:<input type="text" name="ramal" id="edit-ramal" required class="w-full border rounded p-1"></label>
                    <label>Data:<input type="text" name="data" id="edit-data" required class="w-full border rounded p-1"></label>
                    <label>Motivo:<input type="text" name="motivo" id="edit-motivo" required class="w-full border rounded p-1"></label>
                    <label>Período:
                        <select name="periodo" id="edit-periodo" required class="w-full border rounded p-1">
                            <option>Manhã</option>
                            <option>Tarde</option>
                            <option>Integral</option>
                        </select>
                    </label>
                    <label>Email:<input type="email" name="email" id="edit-email" required class="w-full border rounded p-1"></label>
                    <label>Aprovado:
                        <select name="aprovado" id="edit-aprovado" class="w-full border rounded p-1">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </label>
                    <label>Gestor:
                        <select name="id_gestores" id="edit-id_gestores" class="w-full border rounded p-1">
                            <?php foreach ($gestores as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= $g['nome_gestor'] ?> (<?= $g['apelido'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="col-span-2 text-right mt-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar</button>
                        <button type="button" onclick="fecharEditModal()" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Editar Gestor -->
        <div id="modal-editar-gestor" class="modal-overlay">
            <div class="modal-box">
                <h3 class="text-lg font-semibold mb-3">Editar Gestor</h3>

                <input type="hidden" id="edit_gestor_id">

                <label class="block mb-2">
                    Nome do gestor
                    <input id="edit_nome_gestor" type="text"
                        class="w-full border rounded p-2">
                </label>

                <label class="block mb-4">
                    Email do gestor
                    <input id="edit_email_gestor" type="email"
                        class="w-full border rounded p-2">
                </label>

                <div class="flex justify-end gap-2">
                    <button onclick="App.Gestores.salvar()"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                        Salvar
                    </button>
                    <button onclick="App.Gestores.fechar()"
                        class="bg-gray-300 px-4 py-2 rounded">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Editar Veiculo -->
        <div id="modal-editar-carro" class="modal-overlay flex">
            <div class="modal-box">
                <h3 class="text-lg font-semibold mb-3">Editar Veículo</h3>

                <input type="hidden" id="edit_carro_id">

                <label class="block mb-2">
                    Nome do veículo
                    <input
                        type="text"
                        id="edit_nome_carro"
                        class="w-full border rounded p-2">
                </label>

                <label class="block mb-4">
                    Display do veículo
                    <input
                        type="text"
                        id="edit_display_carro"
                        class="w-full border rounded p-2">
                </label>

                <div class="text-right space-x-2">
                    <button
                        onclick="App.Veiculos.salvar()"
                        class="bg-blue-600 text-white px-4 py-2 rounded">
                        Salvar
                    </button>
                    <button type="button"
                        onclick="App.Modal.fechar('modal-editar-carro')"
                        class="bg-gray-300 px-4 py-2 rounded">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>

        <script src="./js/app.js"></script>

        <script>
            // ===== Dark Mode =====
            const body = document.body;
            if (localStorage.getItem('theme') === 'dark') body.classList.add('dark');
            document.getElementById('modeToggle').addEventListener('click', () => {
                body.classList.toggle('dark');
                localStorage.setItem('theme', body.classList.contains('dark') ? 'dark' : 'light');
            });

            // ===== Modais =====
            const modal = document.getElementById('modal');
            const modalMessage = document.getElementById('modal-message');
            const modalYes = document.getElementById('modal-yes');
            const modalNo = document.getElementById('modal-no');
            let modalAction = null;

            function abrirModal(message, yesCallback) {
                modalMessage.textContent = message;
                modal.style.display = 'flex';
                modalAction = yesCallback;
            }

            function fecharModal() {
                modal.style.display = 'none';
            }
            modalYes.onclick = () => {
                if (modalAction) modalAction();
                fecharModal();
            };
            modalNo.onclick = () => fecharModal();

            const editModal = document.getElementById('edit-modal');
            const editForm = document.getElementById('edit-form');
            let rowToEdit = null;

            function fecharEditModal() {
                editModal.style.display = 'none';
            }

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    rowToEdit = btn.closest('tr');
                    editForm['id'].value = rowToEdit.children[0].textContent.trim();
                    editForm['nome'].value = rowToEdit.children[1].textContent.trim();
                    editForm['carro'].value = rowToEdit.children[2].textContent.trim();
                    editForm['ramal'].value = rowToEdit.children[3].textContent.trim();
                    editForm['data'].value = rowToEdit.children[4].textContent.trim();
                    editForm['motivo'].value = rowToEdit.children[5].textContent.trim();
                    editForm['periodo'].value = rowToEdit.children[6].textContent.trim();
                    editForm['email'].value = rowToEdit.children[7].textContent.trim();
                    editForm['aprovado'].value = rowToEdit.children[10].textContent.trim() === 'Sim' ? '1' : '0';
                    editForm['id_gestores'].value = rowToEdit.dataset.gestor || '';
                    editModal.style.display = 'flex';
                });
            });

            editForm.addEventListener('submit', e => {
                e.preventDefault();
                fetch(editForm.action, {
                        method: 'POST',
                        body: new FormData(editForm)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Atualiza tabela
                            rowToEdit.children[1].textContent = editForm['nome'].value;
                            rowToEdit.children[2].textContent = editForm['carro'].value;
                            rowToEdit.children[3].textContent = editForm['ramal'].value;
                            rowToEdit.children[4].textContent = editForm['data'].value;
                            rowToEdit.children[5].textContent = editForm['motivo'].value;
                            rowToEdit.children[6].textContent = editForm['periodo'].value;
                            rowToEdit.children[7].textContent = editForm['email'].value;
                            rowToEdit.children[10].textContent = editForm['aprovado'].value === '1' ? 'Sim' : 'Não';
                            rowToEdit.dataset.gestor = editForm['id_gestores'].value;

                            fecharEditModal();
                            alert('Editado com sucesso!');

                            // Atualiza visual dos cards de veículos
                            window.location.reload(); // jeito simples de garantir atualização
                        }

                    });
            });

            // ===== Excluir =====
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const tr = btn.closest('tr');
                    abrirModal('Deseja realmente excluir?', () => {
                        const id = tr.children[0].textContent.trim();
                        fetch(`?acao=excluir&id=${id}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    tr.remove();
                                    alert('Excluído!');
                                } else alert('Erro ao excluir');
                            });
                    });
                });
            });

            // ===== Busca =====
            document.getElementById('search-input').addEventListener('input', function() {
                const term = this.value.toLowerCase();
                document.querySelectorAll('tbody tr').forEach(tr => {
                    tr.style.display = tr.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            });
        </script>

</body>

</html>