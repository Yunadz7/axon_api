<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("conexao.php");

$id = isset($_POST['id']) ? $_POST['id'] : null;

if (!$id) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "ID não recebido"
    ]);
    exit;
}

$sql = "SELECT concluida FROM atividades WHERE id=$id";
$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo json_encode([
        "status" => "erro",
        "mensagem" => "Atividade não encontrada"
    ]);
    exit;
}

$novo = $row['concluida'] == 1 ? 0 : 1;

mysqli_query($conn, "UPDATE atividades SET concluida=$novo WHERE id=$id");

echo json_encode([
    "status" => "ok",
    "novo" => $novo
]);