<?php
include 'conexao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$acao = $_POST['acao'] ?? '';

/* =========================
   ADICIONAR GESTOR
========================= */
if ($acao === 'add') {

    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$nome || !$email) {
        echo json_encode(['success' => false, 'msg' => 'Nome e e-mail são obrigatórios']);
        exit;
    }

    $apelido = explode('@', $email)[0];

    $stmt = $conn->prepare("
        INSERT INTO gestores (nome_gestor, email_gestor, apelido)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("sss", $nome, $email, $apelido);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'id' => $stmt->insert_id,
            'nome' => $nome,
            'apelido' => $apelido
        ]);
    } else {
        echo json_encode(['success' => false, 'msg' => $stmt->error]);
    }
    exit;
}

/* =========================
   EDITAR GESTOR
========================= */
if ($acao === 'edit') {

    $id    = intval($_POST['id'] ?? 0);
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$id || !$nome || !$email) {
        echo json_encode(['success' => false, 'msg' => 'Dados inválidos']);
        exit;
    }

    $apelido = explode('@', $email)[0];

    $stmt = $conn->prepare("
        UPDATE gestores
        SET nome_gestor = ?, email_gestor = ?, apelido = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssi", $nome, $email, $apelido, $id);

    echo json_encode(['success' => $stmt->execute()]);
    exit;
}

/* =========================
   APAGAR GESTOR
========================= */
if ($acao === 'delete') {

    $id = intval($_POST['id'] ?? 0);

    if (!$id) {
        echo json_encode(['success' => false, 'msg' => 'ID inválido']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM gestores WHERE id = ?");
    $stmt->bind_param("i", $id);

    echo json_encode(['success' => $stmt->execute()]);
    exit;
}

/* =========================
   AÇÃO INVÁLIDA
========================= */
echo json_encode(['success' => false, 'msg' => 'Ação inválida']);
