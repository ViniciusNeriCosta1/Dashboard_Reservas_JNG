<?php
$servidor = 'GRPJNG011204080';  // IP do servidor MySQL
$usuario = 'root';              // Nome de usuário do banco de dados
$senha = '';                    // Senha do banco de dados (deixe em branco se não houver senha)
$dbname = 'visitante';       // Nome do banco de dados

$conn = mysqli_connect($servidor, $usuario, $senha, $dbname);

$sql = "SELECT * FROM visitantes ORDER BY id DESC"; // ou o nome real da tabela


// Teste se a conexão foi bem-sucedida
if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}
?>
