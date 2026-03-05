<?php

include 'conexao_cr.php'; // define $host_cr, $dbname_cr, $user_cr, $pass_cr
include 'conexao_vt.php'; // define $host_vt, $dbname_vt, $user_vt, $pass_vt
include 'conexao_rr.php'; // define $host_rr, $dbname_rr, $user_rr, $pass_rr

try {
    // Conexão 1: Reuniões
    $pdo_rr = new PDO("mysql:host=$host_rr;dbname=$dbname_rr;charset=utf8", $user_rr, $pass_rr);
    $pdo_rr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmtReuniao = $pdo_rr->query("SELECT COUNT(*) FROM reservas_salas");
    $totalReuniao = $stmtReuniao->fetchColumn();

    // Conexão 2: Veículos
    $pdo_cr = new PDO("mysql:host=$host_cr;dbname=$dbname_cr;charset=utf8", $user_cr, $pass_cr);
    $pdo_cr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmtVeiculos = $pdo_cr->query("SELECT COUNT(*) FROM cadastro");
    $totalVeiculos = $stmtVeiculos->fetchColumn();

    // Conexão 3: Visitantes
    $pdo_vt = new PDO("mysql:host=$host_vt;dbname=$dbname_vt;charset=utf8", $user_vt, $pass_vt);
    $pdo_vt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmtVisitantes = $pdo_vt->query("SELECT COUNT(*) FROM visitantes");
    $totalVisitantes = $stmtVisitantes->fetchColumn();
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco: " . $e->getMessage();
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <link rel="shortcut icon" href="https://www.jng.com.br/site/img/favicon.ico">
    <title>Dashboard de Reservas</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./reuniao/style.css">
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-700 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="bg-white  w-20 flex-shrink-0 flex-col hidden sm:flex">
        <div class="h-16 flex items-center justify-center text-blue-500">
            <img src="https://www.jng.com.br/site/img/logos/logo.svg" width="40" alt="Logo" />
        </div>
        <div class="flex mx-auto flex-grow mt-4 flex-col text-gray-400 space-y-4">
            <a href="./dashboard.php" class="h-10 w-12 flex items-center justify-center bg-blue-500 text-blue-500 dark:bg-gray-700 dark:text-white rounded-md">
                <i class="fa fa-home"></i>
            </a>
            <a href="./reserva_carro/reserva.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 dark:hover:bg-gray-700 rounded-md">
                <i class="fa fa-car"></i>
            </a>
            <a href="./visitantes/visitantes.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 dark:hover:bg-gray-700 rounded-md">
                <i class="fa fa-user-friends"></i>
            </a>
            <a href="./reuniao/reuniao.php" class="h-10 w-12 flex items-center justify-center hover:bg-blue-500 dark:hover:bg-gray-700 rounded-md">
                <i class="fa fa-users"></i>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col">
        <!-- Header -->
        <header class="h-16 flex items-center px-10 bg-white dark:bg-gray-900">
            <div class="nav-links flex h-full text-gray-600 dark:text-gray-400">
                <a href="./dashboard.php" class="inline-flex items-center mr-8">Dashboard</a>
                <a href="./reserva_carro/reserva.php" class="inline-flex items-center mr-8">Reserva de Carro</a>
                <a href="./visitantes/visitantes.php" class="inline-flex items-center mr-8">Reserva de Visitante</a>
                <a href="./reuniao/reuniao.php" class="inline-flex items-center mr-8">Reserva de Reunião</a>
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
        <section class="flex-grow bg-gray-100 dark:bg-gray-900 overflow-y-auto p-8 min-h-screen">
            <main class="max-w-7xl mx-auto">
                <h1 class="text-3xl font-bold mb-8 text-gray-800 dark:text-white">Dashboard de Reservas</h1>

                <!-- Cards de Totais -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 text-center hover:scale-[1.02] transition">
                        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Reuniões</h2>
                        <p id="totalReuniao" class="text-3xl font-bold text-blue-500 mt-2"><?= $totalReuniao ?></p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 text-center hover:scale-[1.02] transition">
                        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Veículos</h2>
                        <p id="totalVeiculos" class="text-3xl font-bold text-green-500 mt-2"><?= $totalVeiculos ?></p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 text-center hover:scale-[1.02] transition">
                        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Visitantes</h2>
                        <p id="totalVisitantes" class="text-3xl font-bold text-orange-500 mt-2"><?= $totalVisitantes ?></p>
                    </div>
                </div>

                <!-- Comparativo de Reservas (Bar Chart em tela cheia) -->
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Comparativo de Reservas</h3>
                    <div class="relative h-96">
                        <canvas id="reservasChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <!-- Gráficos Lado a Lado -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Gráfico de Linhas -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Reservas por Mês</h3>
                        <div class="relative h-72">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico de Pizza -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Proporção de Reservas</h3>
                        <div class="relative h-72">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>

                    <!-- Gráfico Radar -->
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Distribuição por Setor</h3>
                        <div class="relative h-72">
                            <canvas id="radarChart"></canvas>
                        </div>
                    </div>
                </div>
            </main>
        </section>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const dados = {
            reuniao: <?= $totalReuniao ?>,
            veiculos: <?= $totalVeiculos ?>,
            visitantes: <?= $totalVisitantes ?>
        };

        // Cria o gráfico
        const ctx = document.getElementById('reservasChart').getContext('2d');
        const reservasChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Reuniões', 'Veículos', 'Visitantes'],
                datasets: [{
                    label: 'Total de Reservas',
                    data: [dados.reuniao, dados.veiculos, dados.visitantes],
                    backgroundColor: [
                        '#3498db',
                        '#2ecc71',
                        '#e67e22'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Comparativo de Reservas'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
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

    <script>
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago'],
                datasets: [{
                    label: 'Reservas Mensais',
                    data: [12, 19, 10, 15, 22, 30, 25, 18],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                }
            }
        });

        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Reuniões', 'Veículos', 'Visitantes'],
                datasets: [{
                    data: [<?= $totalReuniao ?>, <?= $totalVeiculos ?>, <?= $totalVisitantes ?>],
                    backgroundColor: ['#3498db', '#2ecc71', '#e67e22']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        const radarCtx = document.getElementById('radarChart').getContext('2d');
        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: ['RH', 'TI', 'Financeiro', 'Marketing', 'Jurídico'],
                datasets: [{
                    label: 'Reservas por Setor',
                    data: [15, 20, 12, 18, 10],
                    backgroundColor: 'rgba(46, 204, 113, 0.2)',
                    borderColor: '#2ecc71',
                    pointBackgroundColor: '#2ecc71'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                elements: {
                    line: {
                        borderWidth: 2
                    }
                }
            }
        });
    </script>

</body>

</html>