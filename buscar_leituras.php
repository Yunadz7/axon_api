<?php
// CABEÇALHOS PARA LIBERAR CORS E PERMITIR REQUISIÇÕES DA WEB
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Trata requisições pré-flight (OPTIONS) do navegador
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include("conexao.php");

// 1. Pega o usuario_id do dispositivo ativo
$sql_ativo = "SELECT usuario_id FROM dispositivo_ativo WHERE id = 1 LIMIT 1";
$res_ativo = mysqli_query($conn, $sql_ativo);

if ($res_ativo && mysqli_num_rows($res_ativo) > 0) {
    $row_ativo = mysqli_fetch_assoc($res_ativo);
    $usuario_id = $row_ativo['usuario_id'];

    // 2. Busca a última leitura desse usuário
    $sql_leitura = "SELECT bpm, spo2, data_hora FROM leituras_sensor 
                    WHERE usuario_id = '$usuario_id' 
                    ORDER BY id DESC LIMIT 1";
    $res_leitura = mysqli_query($conn, $sql_leitura);

    if ($res_leitura && mysqli_num_rows($res_leitura) > 0) {
        $leitura = mysqli_fetch_assoc($res_leitura);
        $bpm = intval($leitura['bpm']);

        // Lógica de Ansiedade
        $status_ansiedade = "Calmo";
        if ($bpm >= 80 && $bpm <= 100) {
            $status_ansiedade = "Ansiedade Média";
        } elseif ($bpm > 100) {
            $status_ansiedade = "Ansiedade Alta";
        }

        echo json_encode([
            "status" => "sucesso",
            "bpm" => $bpm,
            "spo2" => intval($leitura['spo2']),
            "ansiedade" => $status_ansiedade,
            "usuario_id" => $usuario_id
        ]);
        exit();
    }
}

// Fallback caso ainda não haja leituras
echo json_encode([
    "status" => "aguardando",
    "bpm" => "--",
    "spo2" => "--",
    "ansiedade" => "Aguardando leitura...",
    "usuario_id" => 0
]);
?>