<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli("localhost", "root", "", "axon");

if ($conn->connect_error) {
    echo json_encode([
        "sucesso" => false,
        "erro" => "Erro conexão: " . $conn->connect_error
    ]);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if (!$dados) {
    echo json_encode([
        "sucesso" => false,
        "erro" => "Sem dados recebidos"
    ]);
    exit;
}

$id_usuario = $dados["id_usuario"];
$emocoes = $dados["emocoes"];
$ansiedade = $dados["ansiedade"];
$energia = $dados["energia"];
$medicacao = $dados["medicacao"];
$observacoes = $dados["observacoes"];

$sql = "INSERT INTO registro 
(id_usuario, emocoes, ansiedade, energia, medicacao, observacoes)
VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "sucesso" => false,
        "erro" => "Erro na preparação da SQL: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "isiiis",
    $id_usuario,
    $emocoes,
    $ansiedade,
    $energia,
    $medicacao,
    $observacoes
);

if ($stmt->execute()) {
    echo json_encode([
        "sucesso" => true
    ]);
} else {
    echo json_encode([
        "sucesso" => false,
        "erro" => $stmt->error
    ]);
}

$stmt->close();
$conn->close();

?>