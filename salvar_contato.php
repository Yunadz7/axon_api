<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Trata requisição de verificação CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 1. Tenta ler do FormData ($_POST)
$id_usuario = $_POST["id_usuario"] ?? "";
$nome_contato = trim($_POST["nome_contato"] ?? "");
$numero = trim($_POST["numero"] ?? "");

// 2. Se $_POST estiver vazio, tenta ler via JSON (file_get_contents)
if (empty($nome_contato) && empty($numero)) {
    $dados = json_decode(file_get_contents("php://input"), true);
    $id_usuario = $dados["id_usuario"] ?? "";
    $nome_contato = trim($dados["nome_contato"] ?? "");
    $numero = trim($dados["numero"] ?? "");
}

if (empty($nome_contato) || empty($numero)) {
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
        "mensagem" => "Erro de conexão com o banco de dados"
    ]);
    exit;
}

$conn->set_charset("utf8mb4");

$sql = "INSERT INTO ajuda (id_usuario, nome_contato, numero) VALUES ('$id_usuario', '$nome_contato', '$numero')";

if ($conn->query($sql)) {
    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Contato salvo com sucesso"
    ]);
} else {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro ao salvar contato no banco"
    ]);
}
#include("buscar_contato.php");
$conn->close();
?>