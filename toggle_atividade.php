<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("conexao.php");

$id_usuario = $_POST['id_usuario'] ?? null;
$id_atividade = $_POST['id_atividade'] ?? null;
$concluida = $_POST['concluida'] ?? null;

if ($id_usuario === null || $id_atividade === null || $concluida === null) {

    echo json_encode([
        "status" => "erro",
        "msg" => "Dados incompletos",
        "recebido" => $_POST
    ]);

    exit;
}

$id_usuario = intval($id_usuario);
$id_atividade = intval($id_atividade);
$concluida = intval($concluida);


// verifica se já existe
$sqlCheck = "
    SELECT id
    FROM atividades_usuario
    WHERE id_usuario = $id_usuario
    AND id_atividade = $id_atividade
";

$check = mysqli_query($conn, $sqlCheck);

if (!$check) {

    echo json_encode([
        "status" => "erro",
        "msg" => "Erro no SELECT",
        "erro_mysql" => mysqli_error($conn)
    ]);

    exit;
}


// se já existe
if (mysqli_num_rows($check) > 0) {

    $sqlUpdate = "
        UPDATE atividades_usuario
        SET concluida = $concluida
        WHERE id_usuario = $id_usuario
        AND id_atividade = $id_atividade
    ";

    $resultado = mysqli_query($conn, $sqlUpdate);

    if (!$resultado) {

        echo json_encode([
            "status" => "erro",
            "msg" => "Erro no UPDATE",
            "erro_mysql" => mysqli_error($conn)
        ]);

        exit;
    }

    echo json_encode([
        "status" => "ok",
        "acao" => "atualizado",
        "id_usuario" => $id_usuario,
        "id_atividade" => $id_atividade,
        "concluida" => $concluida
    ]);


// se não existe
} else {

    $sqlInsert = "
        INSERT INTO atividades_usuario
        (id_usuario, id_atividade, concluida)
        VALUES
        ($id_usuario, $id_atividade, $concluida)
    ";

    $resultado = mysqli_query($conn, $sqlInsert);

    if (!$resultado) {

        echo json_encode([
            "status" => "erro",
            "msg" => "Erro no INSERT",
            "erro_mysql" => mysqli_error($conn)
        ]);

        exit;
    }

    echo json_encode([
        "status" => "ok",
        "acao" => "inserido",
        "id_usuario" => $id_usuario,
        "id_atividade" => $id_atividade,
        "concluida" => $concluida
    ]);
}
?>