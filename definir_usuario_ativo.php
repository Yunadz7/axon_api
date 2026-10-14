<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

$host = "localhost";
$db_name = "axon";
$username = "root";
$password = "";

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->usuario_id)) {
    try {
        $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Atualiza quem é o usuário ativo
        $query = "UPDATE dispositivo_ativo SET usuario_id = :usuario_id WHERE id = 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":usuario_id", $data->usuario_id);
        $stmt->execute();

        echo json_encode(["status" => "success", "usuario_id" => $data->usuario_id]);
    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>