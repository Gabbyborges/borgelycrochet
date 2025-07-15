<?php
session_start();
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.php"); // ajuste o nome do arquivo de login
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Área do Cliente</title></head>
<body>
  <h1>Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?>!</h1>
  <p>Bem-vindo(a) à sua área do cliente.</p>
  <p><a href="logout.php">Sair</a></p>
</body>
</html>


