<?php
include 'conexao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id      = intval($_POST['id']);
    $nome    = $conn->real_escape_string($_POST['nome'] ?? '');
    $carro   = $conn->real_escape_string($_POST['carro'] ?? '');
    $ramal   = $conn->real_escape_string($_POST['ramal'] ?? '');
    $data    = $conn->real_escape_string($_POST['data'] ?? '');
    $motivo  = $conn->real_escape_string($_POST['motivo'] ?? '');
    $periodo = $conn->real_escape_string($_POST['periodo'] ?? '');
    $email   = $conn->real_escape_string($_POST['email'] ?? '');

    // Novas colunas (tratadas como opcionais)
    $ativo         = isset($_POST['ativo']) ? intval($_POST['ativo']) : 0;
    $aprovado      = isset($_POST['aprovado']) ? intval($_POST['aprovado']) : 0;
    $id_gestores   = isset($_POST['id_gestores']) ? intval($_POST['id_gestores']) : null;
    $arquivo_cnh   = isset($_POST['arquivo_cnh']) ? $conn->real_escape_string($_POST['arquivo_cnh']) : '';
    $token_aprovar = isset($_POST['token_aprovar']) ? $conn->real_escape_string($_POST['token_aprovar']) : '';
    $token_reprovar = isset($_POST['token_reprovar']) ? $conn->real_escape_string($_POST['token_reprovar']) : '';

    $sql = "UPDATE cadastro SET 
        nome='$nome',
        carro='$carro',
        ramal='$ramal',
        data='$data',
        motivo='$motivo',
        periodo='$periodo',
        email='$email',
        ativo=$ativo,
        arquivo_cnh='$arquivo_cnh',
        aprovado=$aprovado,
        token_aprovar='$token_aprovar',
        token_reprovar='$token_reprovar',
        id_gestores=" . ($id_gestores !== null ? $id_gestores : "NULL") . "
        WHERE id=$id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
}
