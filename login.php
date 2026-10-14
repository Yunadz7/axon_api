<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include("conexao.php");

$json_input = json_decode(file_get_contents("php://input"), true);

$cpf = $_POST['cpf'] ?? $json_input['cpf'] ?? '';
$senha = $_POST['senha'] ?? $json_input['senha'] ?? '';

if (empty($cpf) || empty($senha)) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Preencha CPF e senha!"
    ]);
    exit();
}

$cpf = mysqli_real_escape_string($conn, $cpf);
$senha = mysqli_real_escape_string($conn, $senha);

$sql = "SELECT * FROM usuario WHERE cpf = '$cpf' AND senha = '$senha'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {

    $user = mysqli_fetch_assoc($result);
    $usuario_id = intval($user['id_usuario']);

    // Atualiza o ID do usuário ativo na tabela existente
    $sql_ativo = "INSERT INTO dispositivo_ativo (id, usuario_id) VALUES (1, $usuario_id) 
                  ON DUPLICATE KEY UPDATE usuario_id = $usuario_id";
    mysqli_query($conn, $sql_ativo);

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
?>