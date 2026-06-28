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

$sql = "SELECT id_ajuda, nome_contato, numero
        FROM ajuda
        WHERE id_usuario = $id_usuario
        ORDER BY id_ajuda DESC";

$result = $conn->query($sql);

$contatos = [];

while ($row = $result->fetch_assoc()) {
    $contatos[] = $row;
}

echo json_encode([
    "sucesso" => true,
    "contatos" => $contatos
]);

$conn->close();
?>