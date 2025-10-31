<?php

$host     = "localhost";
$usuario  = "root";
$senha    = "root";
$banco    = "tasks_db";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8");

date_default_timezone_set("America/Sao_Paulo");
