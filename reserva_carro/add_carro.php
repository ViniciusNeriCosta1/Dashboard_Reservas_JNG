<?php
require_once 'conexao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$acao = $_POST['acao'] ?? '';

/* =========================
   ADICIONAR CARRO
========================= */
if ($acao === 'add') {

    $nome   = $_POST['nome_carro'] ?? '';
    $display = $_POST['display_carro'] ?? '';

    if (!$nome) {
        echo json_encode(['success' => false, 'msg' => 'Nome inválido']);
        exit;
    }

    $status = 1;

    $stmt = $conn->prepare("INSERT INTO carros
        (nome_carro, display_carro, status)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("ssi", $nome, $display, $status);

    echo json_encode([
        'success' => $stmt->execute()
    ]);
    exit;
}

/* =========================
   EDITAR CARRO
========================= */
if ($acao === 'edit') {

    $id     = intval($_POST['id'] ?? 0);
    $nome   = $_POST['nome_carro'] ?? '';
    $display = $_POST['display_carro'] ?? '';

    if (!$id || !$nome) {
        echo json_encode(['success' => false, 'msg' => 'Dados inválidos']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE carros
        SET nome_carro = ?, display_carro = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssi", $nome, $display, $id);

    echo json_encode([
        'success' => $stmt->execute()
    ]);
    exit;
}

/* =========================
   APAGAR CARRO
========================= */
if ($acao === 'delete') {

    $id = intval($_POST['id'] ?? 0);

    if (!$id) {
        echo json_encode(['success' => false, 'msg' => 'ID inválido']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM carros
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);

    echo json_encode([
        'success' => $stmt->execute()
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'msg' => 'Ação inválida'
]);
