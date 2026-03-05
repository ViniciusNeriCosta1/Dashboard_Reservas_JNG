<?php

$servidor = 'localhost';  // IP do servidor MySQL
$usuario = 'root';              // Nome de usuário do banco de dados
$senha = '';                    // Senha do banco de dados (deixe em branco se não houver senha)
$dbname = 'agendamentos';       // Nome do banco de dados

// GRPJNG011204080

$conn = mysqli_connect($servidor, $usuario, $senha, $dbname);

// Teste se a conexão foi bem-sucedida
if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}
