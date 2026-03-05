<?php

$servidor = '';  // IP do servidor MySQL
$usuario = '';   // Nome de usuário do banco de dados
$senha = '';     // Senha do banco de dados (deixe em branco se não houver senha)
$dbname = '';    // Nome do banco de dados

$conn = mysqli_connect($servidor, $usuario, $senha, $dbname);

// Teste se a conexão foi bem-sucedida
if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}
