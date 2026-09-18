<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$db_name = "axon"; // Subtitua pelo nome do seu banco
$username = "root";              // Substitua pelo usuário
$password = "";                  // Substitua pela senha

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Erro na conexão: " . $e->getMessage()]);
    exit();
}

$especialidade = isset($_GET['especialidade']) ? trim($_GET['especialidade']) : '';

if (empty($especialidade)) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT id_medico, nome FROM medico WHERE especialidade = :especialidade ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':especialidade', $especialidade);
$stmt->execute();

$medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($medicos);
?>