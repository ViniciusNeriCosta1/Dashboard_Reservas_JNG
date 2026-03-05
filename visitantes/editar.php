<?php
include 'conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $nome = $conn->real_escape_string($_POST['nome']);
    $tipo_documento = $conn->real_escape_string($_POST['tipo_documento']);
    $numero_documento = $conn->real_escape_string($_POST['numero_documento']);
    $data_visita = $conn->real_escape_string($_POST['data_visita']);
    $visitado = $conn->real_escape_string($_POST['visitado']);
    $ramal = $conn->real_escape_string($_POST['ramal']);
    $checkin = $conn->real_escape_string($_POST['checkin']);

    $sql = "UPDATE visitantes SET 
        nome='$nome',
        tipo_documento='$tipo_documento',
        numero_documento='$numero_documento',
        data_visita='$data_visita',
        visitado='$visitado',
        ramal='$ramal',
        checkin='$checkin'
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
