<?php
$conn = new mysqli("localhost", "root", "", "loja_online");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
} else {
    echo "✅ Conectado com sucesso!";
}
