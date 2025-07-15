<?php
include("../db.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $titulo = $_POST['titulo'];
  $slug = $_POST['slug'];
  $resumo = $_POST['resumo'];
  $conteudo = $_POST['conteudo'];
  $imagemAtual = $_POST['imagem_atual'];

  // Verifica se uma nova imagem foi enviada
  if (!empty($_FILES['imagem']['name'])) {
    $nomeOriginal = $_FILES['imagem']['name'];
    $nomeSeguro = preg_replace('/[^\w.-]/', '_', $nomeOriginal);
    $caminho = "../imagens/" . $nomeSeguro;
    move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho);
  } else {
    $nomeSeguro = $imagemAtual;
  }

  $stmt = $conn->prepare("UPDATE blog SET titulo=?, slug=?, resumo=?, conteudo=?, imagem=? WHERE id=?");
  $stmt->bind_param("sssssi", $titulo, $slug, $resumo, $conteudo, $nomeSeguro, $id);
  $stmt->execute();

  echo "<p style='color:green;'>Post atualizado com sucesso!</p>";
}

// Buscar os dados do post
$stmt = $conn->prepare("SELECT * FROM blog WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$post = $resultado->fetch_assoc();
?>

<h2 style="color:#af203f;">📝 Editar Post do Blog</h2>

<form method="POST" enctype="multipart/form-data" style="max-width: 600px; margin: 0 auto; background: #fff2f5; padding: 30px; border-radius: 20px;">
  <input type="hidden" name="imagem_atual" value="<?= $post['imagem'] ?>">

  <label style="font-weight: bold;">Título do Post:</label>
  <input type="text" name="titulo" value="<?= htmlspecialchars($post['titulo']) ?>" required style="width:100%; padding:10px; border-radius:12px;"><br><br>

  <label style="font-weight: bold;">Slug:</label>
  <input type="text" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" required style="width:100%; padding:10px; border-radius:12px;"><br><br>

  <label style="font-weight: bold;">Resumo:</label>
  <textarea name="resumo" rows="3" required style="width:100%; padding:10px; border-radius:12px;"><?= htmlspecialchars($post['resumo']) ?></textarea><br><br>

  <label style="font-weight: bold;">Conteúdo Completo:</label>
  <textarea name="conteudo" rows="10" required style="width:100%; padding:10px; border-radius:12px;"><?= htmlspecialchars($post['conteudo']) ?></textarea><br><br>

  <label style="font-weight: bold;">Imagem Atual:</label><br>
  <img src="../imagens/<?= $post['imagem'] ?>" width="200" style="border-radius: 12px;"><br><br>

  <label style="font-weight: bold;">Trocar Imagem (opcional):</label>
  <input type="file" name="imagem"><br><br>

  <button type="submit" style="background-color:#af203f; color:white; padding:10px 20px; border:none; border-radius:12px;">Salvar Alterações</button>
</form>
