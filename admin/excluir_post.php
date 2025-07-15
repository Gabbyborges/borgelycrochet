<?php
include("../db.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar a imagem do post antes de excluir
$stmt = $conn->prepare("SELECT imagem FROM blog WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$post = $resultado->fetch_assoc();

// Apagar imagem do servidor
if ($post && file_exists("../imagens/" . $post['imagem'])) {
  unlink("../imagens/" . $post['imagem']);
}

// Excluir post do banco
$stmt = $conn->prepare("DELETE FROM blog WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirecionar de volta para a listagem
header("Location: gerenciar_posts.php");
exit();
