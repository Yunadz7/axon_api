<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "axon";

$conn = mysqli_connect($host, $usuario, $senha, $banco);

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}