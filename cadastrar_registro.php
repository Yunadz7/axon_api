<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

include "conexao.php";

$dados = json_decode(file_get_contents("php://input"), true);

$id_usuario = $dados['id_usuario'];
$emocoes = $dados['humor'];
$sono = $dados['sono'];
$ansiedade = $dados['ansiedade'];
$energia = $dados['energia'];
$agua = $dados['agua'];
$medicacao = $dados['medicacao'];
$observacoes = $dados['observacao'];

$sql = "INSERT INTO registro
(id_usuario, emocoes, sono, ansiedade, energia, agua, medicacao, observacoes)
VALUES
('$id_usuario', '$emocoes', '$sono', '$ansiedade', '$energia', '$agua', '$medicacao', '$observacoes')";

if(mysqli_query($conexao, $sql)){
    echo json_encode([
        "sucesso" => true
    ]);
}else{
    echo json_encode([
        "sucesso" => false,
        "erro" => mysqli_error($conexao)
    ]);
}
?>