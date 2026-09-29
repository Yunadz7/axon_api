<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("conexao.php");

$nome = $_POST['nome'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$data_nascimento = $_POST['data_nascimento'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($nome == '' || $cpf == '' || $senha == '') {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Campos obrigatórios faltando"
    ]);
    exit;
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuario (nome, cpf, data_nascimento, senha)
        VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $nome,
    $cpf,
    $data_nascimento,
    $senha
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "status" => "sucesso",
        "mensagem" => "Usuário cadastrado com sucesso"
    ]);

} else {

    echo json_encode([
        "status" => "erro",
        "mensagem" => mysqli_error($conn)
    ]);
}
?>