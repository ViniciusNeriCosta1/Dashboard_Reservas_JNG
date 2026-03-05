<?php
require_once 'conexao.php';
header('Content-Type: application/json');

$acao = $_POST['acao'] ?? '';

/* =========================
   INDISPONÍVEL (STATUS 0)
========================= */
if ($acao === 'indisponivel') {

    $carroId = intval($_POST['id'] ?? 0);

    if (!$carroId) {
        echo json_encode(['success' => false, 'msg' => 'Veículo inválido']);
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE agendamentos.carros
        SET status = 0,
            dt_manu_inicio = NULL,
            dt_manu_fim = NULL
        WHERE id = ?
    ");
    $stmt->bind_param("i", $carroId);
    $stmt->execute();

    echo json_encode(['success' => true]);
    exit;
}

/* =========================
   VOLTAR DISPONÍVEL (STATUS 1)
========================= */
if ($acao === 'disponivel') {

    $carroId = intval($_POST['id'] ?? 0);

    if (!$carroId) {
        echo json_encode(['success' => false, 'msg' => 'Veículo inválido']);
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE agendamentos.carros
        SET status = 1,
            dt_manu_inicio = NULL,
            dt_manu_fim = NULL
        WHERE id = ?
    ");
    $stmt->bind_param("i", $carroId);
    $stmt->execute();

    echo json_encode(['success' => true]);
    exit;
}


/* =========================
   MANUTENÇÃO (STATUS 2)
========================= */
if ($acao === 'manutencao') {

    $carroId = intval($_POST['id'] ?? 0);
    $inicio  = $_POST['data_inicio'] ?? '';
    $fim     = $_POST['data_fim'] ?? '';

    if (!$carroId || !$inicio || !$fim) {
        echo json_encode(['success' => false, 'msg' => 'Dados incompletos']);
        exit;
    }

    if (strtotime($fim) < strtotime($inicio)) {
        echo json_encode(['success' => false, 'msg' => 'Período inválido']);
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE agendamentos.carros
        SET status = 2,
            dt_manu_inicio = ?,
            dt_manu_fim = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssi", $inicio, $fim, $carroId);
    $stmt->execute();

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'msg' => 'Ação inválida']);
