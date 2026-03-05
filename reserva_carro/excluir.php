<?php
include 'conexao.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Executa exclusão
    $sql = "DELETE FROM cadastro WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'ID não fornecido'
    ]);
}
