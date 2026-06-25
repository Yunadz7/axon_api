<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("conexao.php");

$id_usuario = $_POST['id_usuario'] ?? null;
$id_atividade = $_POST['id_atividade'] ?? null;
$concluida = $_POST['concluida'] ?? null;

if (!$id_usuario || !$id_atividade || $concluida === null) {
    echo json_encode(["status" => "erro", "msg" => "dados incompletos"]);
    exit;
}

$id_usuario = intval($id_usuario);
$id_atividade = intval($id_atividade);
$concluida = intval($concluida);

// verifica se já existe
$check = mysqli_query($conn, "
    SELECT id FROM atividades_usuario 
    WHERE id_usuario = $id_usuario AND id_atividade = $id_atividade
");

if (mysqli_num_rows($check) > 0) {
    // atualiza
    mysqli_query($conn, "
        UPDATE atividades_usuario 
        SET concluida = $concluida
        WHERE id_usuario = $id_usuario AND id_atividade = $id_atividade
    ");
} else {
    // insere
    mysqli_query($conn, "
        INSERT INTO atividades_usuario (id_usuario, id_atividade, concluida)
        VALUES ($id_usuario, $id_atividade, $concluida)
    ");
}

echo json_encode(["status" => "ok"]);