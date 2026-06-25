<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli("localhost", "root", "", "axon");

if ($conn->connect_error) {
    echo json_encode(["sucesso" => false, "erro" => "Erro conexão"]);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if (!$dados) {
    echo json_encode(["sucesso" => false, "erro" => "Sem dados recebidos"]);
    exit;
}

$id_usuario = $dados["id_usuario"];
$emocoes = $dados["emocoes"];
$sono = $dados["sono"];
$ansiedade = $dados["ansiedade"];
$energia = $dados["energia"];
$agua = $dados["agua"];
$medicacao = $dados["medicacao"];
$observacoes = $dados["observacoes"];

$sql = "INSERT INTO registro 
(id_usuario, emocoes, sono, ansiedade, energia, agua, medicacao, observacoes)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "isiiiiis",
    $id_usuario,
    $emocoes,
    $sono,
    $ansiedade,
    $energia,
    $agua,
    $medicacao,
    $observacoes
);

if ($stmt->execute()) {
    echo json_encode(["sucesso" => true]);
} else {
    echo json_encode(["sucesso" => false, "erro" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>