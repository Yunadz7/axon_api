<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("conexao.php");

$cpf = $_POST['cpf'] ?? '';
$senha = $_POST['senha'] ?? '';

$sql = "SELECT * FROM usuario WHERE cpf = '$cpf' AND senha = '$senha'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {

    $user = mysqli_fetch_assoc($result);

    echo json_encode([
        "status" => "sucesso",
        "mensagem" => "Login realizado",
        "usuario" => $user
    ]);

} else {

    echo json_encode([
        "status" => "erro",
        "mensagem" => "CPF ou senha inválidos"
    ]);
}