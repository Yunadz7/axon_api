<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("conexao.php");

$nome = $_POST['nome'] ?? null;
$codigo = $_POST['codigo'] ?? null;

if (!$nome || !$codigo) {
    echo json_encode([
        "status" => "erro",
        "msg" => "Dados não recebidos"
    ]);
    exit;
}

$nome = mysqli_real_escape_string($conn, $nome);
$codigo = mysqli_real_escape_string($conn, $codigo);

// checa duplicado
$check = mysqli_query($conn, "SELECT id_dispositivo FROM dispositivo WHERE codigo='$codigo'");

if (!$check) {
    echo json_encode([
        "status" => "erro",
        "msg" => mysqli_error($conn)
    ]);
    exit;
}

if (mysqli_num_rows($check) > 0) {
    echo json_encode([
        "status" => "erro",
        "msg" => "Já existe esse dispositivo"
    ]);
    exit;
}

$sql = "INSERT INTO dispositivo (nome, codigo) VALUES ('$nome', '$codigo')";

if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "status" => "ok",
        "msg" => "Salvo com sucesso"
    ]);
} else {
    echo json_encode([
        "status" => "erro",
        "msg" => mysqli_error($conn)
    ]);
}