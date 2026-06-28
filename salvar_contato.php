<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

$dados = json_decode(file_get_contents("php://input"), true);

$id_usuario = $dados["id_usuario"] ?? 1;
$nome_contato = $dados["nome_contato"] ?? "";
$numero = $dados["numero"] ?? "";
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

if ($nome_contato == "" || $numero == "") {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Preencha todos os campos"
    ]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "axon");

if ($conn->connect_error) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro de conexão"
    ]);
    exit;
}

$sql = "INSERT INTO ajuda (id_usuario, nome_contato, numero)
        VALUES ('$id_usuario', '$nome_contato', '$numero')";

if ($conn->query($sql)) {
    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Contato salvo com sucesso"
    ]);
} else {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao salvar contato"
    ]);
}

$conn->close();
