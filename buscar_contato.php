<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Trata requisição de verificação CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Conexão com o banco local
$conn = new mysqli("localhost", "root", "", "axon");

if ($conn->connect_error) {
    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Erro de conexão com o banco"
    ]);
    exit;
}

$conn->set_charset("utf8mb4");

// Busca o nome_contato e numero da tabela ajuda
$sql = "SELECT id_usuario, nome_contato, numero FROM ajuda";
$result = $conn->query($sql);

$contatos = array();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $contatos[] = $row;
    }
}

// Retorna os contatos encontrados
echo json_encode([
    "sucesso" => true,
    "contatos" => $contatos
]);

$conn->close();
?>