<?php
session_start();

// Destrói todas as variáveis da sessão
$_SESSION = [];

// Destroi a sessão
session_destroy();

// Redireciona para a página inicial ou login, conforme sua preferência
header("Location: ../index.php"); // ou login_cliente.php, ou login_admin.php
exit;
