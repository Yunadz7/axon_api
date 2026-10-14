<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include("conexao.php");

// Reseta o usuário ativo na tabela dispositivo_ativo para 0
$sql = "UPDATE dispositivo_ativo SET usuario_id = 0 WHERE id = 1";
mysqli_query($conn, $sql);

echo json_encode([
    "status" => "sucesso",
    "mensagem" => "Logoff realizado com sucesso"
]);
?>