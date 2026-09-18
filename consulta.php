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
    echo json_encode(["status" => "error", "message" => "Erro na conexão"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$nome = $data['nome'] ?? '';
$id_medico = $data['id_medico'] ?? '';
$data_consulta = $data['data_consulta'] ?? '';
$horario = $data['horario'] ?? '';

if (empty($nome) || empty($id_medico) || empty($data_consulta) || empty($horario)) {
    echo json_encode(["status" => "error", "message" => "Preencha todos os campos."]);
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Cadastra ou busca o usuário pelo nome
    $stmtUser = $pdo->prepare("SELECT id_usuario FROM usuario WHERE nome = :nome LIMIT 1");
    $stmtUser->bindParam(':nome', $nome);
    $stmtUser->execute();
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $id_usuario = $usuario['id_usuario'];
    } else {
        $stmtInsertUser = $pdo->prepare("INSERT INTO usuario (nome) VALUES (:nome)");
        $stmtInsertUser->bindParam(':nome', $nome);
        $stmtInsertUser->execute();
        $id_usuario = $pdo->lastInsertId();
    }

    // 2. Insere a consulta
    $sqlConsulta = "INSERT INTO consulta (id_medico, id_usuario, data_consulta, horario) VALUES (:id_medico, :id_usuario, :data_consulta, :horario)";
    $stmtConsulta = $pdo->prepare($sqlConsulta);
    $stmtConsulta->bindParam(':id_medico', $id_medico);
    $stmtConsulta->bindParam(':id_usuario', $id_usuario);
    $stmtConsulta->bindParam(':data_consulta', $data_consulta);
    $stmtConsulta->bindParam(':horario', $horario);
    $stmtConsulta->execute();

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Consulta agendada com sucesso!"]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Erro ao agendar: " . $e->getMessage()]);
}
?>