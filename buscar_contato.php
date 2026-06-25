<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$id_usuario = $_GET["id_usuario"] ?? 1;

$conn = new mysqli("localhost", "root", "", "axon");

if ($conn->connect_error) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro de conexão"
    ]);
    exit;
}

$sql = "SELECT nome_contato, numero 
        FROM ajuda 
        WHERE id_usuario = $id_usuario
        ORDER BY id_ajuda DESC
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    echo json_encode([
        "sucesso" => true,
        "nome_contato" => $row["nome_contato"],
        "numero" => $row["numero"]
    ]);
} else {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Nenhum contato encontrado"
    ]);
}

$conn->close();
?>