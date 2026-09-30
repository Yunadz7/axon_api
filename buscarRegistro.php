<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

$conn = new mysqli("localhost", "root", "", "axon");

if ($conn->connect_error) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro de conexão com o banco de dados."
    ]);
    exit;
}

$id_usuario = isset($_GET["id_usuario"]) ? intval($_GET["id_usuario"]) : 0;

if ($id_usuario <= 0) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Informe um id_usuario válido."
    ]);
    $conn->close();
    exit;
}

// Seleção sem os campos 'sono' e 'agua'
$sql = "SELECT
            id_registro,
            emocoes,
            ansiedade,
            energia,
            medicacao,
            observacoes,
            DATE_FORMAT(data_registro, '%d/%m/%Y %H:%i') AS data_formatada
        FROM registro
        WHERE id_usuario = ?
        ORDER BY data_registro DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$result = $stmt->get_result();

$registros = [];
while ($row = $result->fetch_assoc()) {
    $registros[] = $row;
}

echo json_encode([
    "sucesso" => true,
    "registros" => $registros
], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>