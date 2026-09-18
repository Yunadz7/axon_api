<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// ========================================
// TRATAR OPTIONS
// ========================================

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ========================================
// BANCO
// ========================================

$host = "localhost";
$db_name = "axon";
$username = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    echo json_encode([
        "status" => "error",
        "message" => "Erro na conexão com o banco."
    ]);

    exit();
}


// ======================================================
// GET = BUSCAR CONSULTAS DO USUÁRIO
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $id_usuario = $_GET['id_usuario'] ?? '';

    if (empty($id_usuario)) {

        echo json_encode([
            "status" => "error",
            "message" => "ID do usuário não informado."
        ]);

        exit();
    }

    try {

        $sql = "
            SELECT
                c.id_consulta,
                c.id_usuario,
                c.id_medico,
                c.data_consulta,
                c.horario,
                m.nome AS medico

            FROM consulta c

            INNER JOIN medico m
                ON c.id_medico = m.id_medico

            WHERE c.id_usuario = :id_usuario

            ORDER BY
                c.data_consulta ASC,
                c.horario ASC
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(
            ':id_usuario',
            $id_usuario,
            PDO::PARAM_INT
        );

        $stmt->execute();

        $consultas = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        echo json_encode(
            $consultas,
            JSON_UNESCAPED_UNICODE
        );

        exit();

    } catch (PDOException $e) {

        echo json_encode([
            "status" => "error",
            "message" => "Erro ao buscar consultas: " . $e->getMessage()
        ]);

        exit();
    }
}


// ======================================================
// POST = CADASTRAR CONSULTA
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ========================================
    // RECEBER JSON
    // ========================================

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    // ========================================
    // PEGAR DADOS
    // ========================================

    $id_usuario = $data['id_usuario'] ?? '';
    $id_medico = $data['id_medico'] ?? '';
    $data_consulta = $data['data_consulta'] ?? '';
    $horario = $data['horario'] ?? '';

    // ========================================
    // VALIDAR
    // ========================================

    if (
        empty($id_usuario) ||
        empty($id_medico) ||
        empty($data_consulta) ||
        empty($horario)
    ) {

        echo json_encode([
            "status" => "error",
            "message" => "Preencha todos os campos."
        ]);

        exit();
    }

    try {

        // ========================================
        // VERIFICAR USUÁRIO
        // ========================================

        $stmtUsuario = $pdo->prepare(
            "SELECT id_usuario
             FROM usuario
             WHERE id_usuario = :id_usuario
             LIMIT 1"
        );

        $stmtUsuario->bindParam(
            ':id_usuario',
            $id_usuario,
            PDO::PARAM_INT
        );

        $stmtUsuario->execute();

        $usuario = $stmtUsuario->fetch(
            PDO::FETCH_ASSOC
        );

        if (!$usuario) {

            echo json_encode([
                "status" => "error",
                "message" => "Usuário não encontrado."
            ]);

            exit();
        }


        // ========================================
        // VERIFICAR MÉDICO
        // ========================================

        $stmtMedico = $pdo->prepare(
            "SELECT id_medico
             FROM medico
             WHERE id_medico = :id_medico
             LIMIT 1"
        );

        $stmtMedico->bindParam(
            ':id_medico',
            $id_medico,
            PDO::PARAM_INT
        );

        $stmtMedico->execute();

        $medico = $stmtMedico->fetch(
            PDO::FETCH_ASSOC
        );

        if (!$medico) {

            echo json_encode([
                "status" => "error",
                "message" => "Médico não encontrado."
            ]);

            exit();
        }


        // ========================================
        // VERIFICAR HORÁRIO
        // ========================================

        $stmtExiste = $pdo->prepare(
            "SELECT id_consulta
             FROM consulta
             WHERE id_medico = :id_medico
             AND data_consulta = :data_consulta
             AND horario = :horario
             LIMIT 1"
        );

        $stmtExiste->bindParam(
            ':id_medico',
            $id_medico,
            PDO::PARAM_INT
        );

        $stmtExiste->bindParam(
            ':data_consulta',
            $data_consulta
        );

        $stmtExiste->bindParam(
            ':horario',
            $horario
        );

        $stmtExiste->execute();

        if ($stmtExiste->fetch()) {

            echo json_encode([
                "status" => "error",
                "message" => "Este horário já está ocupado para este médico."
            ]);

            exit();
        }


        // ========================================
        // SALVAR CONSULTA
        // ========================================

        $sql = "
            INSERT INTO consulta
            (
                id_medico,
                id_usuario,
                data_consulta,
                horario
            )
            VALUES
            (
                :id_medico,
                :id_usuario,
                :data_consulta,
                :horario
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(
            ':id_medico',
            $id_medico,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ':id_usuario',
            $id_usuario,
            PDO::PARAM_INT
        );

        $stmt->bindParam(
            ':data_consulta',
            $data_consulta
        );

        $stmt->bindParam(
            ':horario',
            $horario
        );

        $stmt->execute();


        // ========================================
        // RESPOSTA
        // ========================================

        echo json_encode([
            "status" => "success",
            "message" => "Consulta agendada com sucesso!"
        ]);

        exit();

    } catch (PDOException $e) {

        echo json_encode([
            "status" => "error",
            "message" => "Erro ao agendar consulta: " . $e->getMessage()
        ]);

        exit();
    }
}


// ======================================================
// MÉTODO NÃO PERMITIDO
// ======================================================

echo json_encode([
    "status" => "error",
    "message" => "Método não permitido."
]);

?>
