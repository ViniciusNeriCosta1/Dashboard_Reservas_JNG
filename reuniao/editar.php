<?php
include 'conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefone = $conn->real_escape_string($_POST['telefone']);
    $sala = $conn->real_escape_string($_POST['sala']);
    $data = $conn->real_escape_string($_POST['data']);
    $hora_inicio = $conn->real_escape_string($_POST['hora_inicio']);
    $hora_fim = $conn->real_escape_string($_POST['hora_fim']);
    $participantes = intval($_POST['participantes']);
    $assunto = $conn->real_escape_string($_POST['assunto']);

    $sql = "UPDATE reservas_salas SET 
        nome='$nome', email='$email', telefone='$telefone', sala='$sala', 
        data='$data', hora_inicio='$hora_inicio', hora_fim='$hora_fim', 
        participantes=$participantes, assunto='$assunto'
        WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
}
?>
