<?php

$servername = "localhost";
$username = "gaby";
$password = "123456";
$dbname = "loja_online";
$port = 3307; // ← SUA PORTA CORRETA

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
