<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

include("../db.php");

$mensagem = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: listar_produtos_admin.php");
    exit;
}

$res = $conn->query("SELECT * FROM produtos WHERE id=$id");
if ($res->num_rows == 0) {
    header("Location: listar_produtos_admin.php");
    exit;
}
$produto = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $preco = floatval(str_replace(',', '.', $_POST['preco']));
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $categoria = $conn->real_escape_string($_POST['categoria']);

    // Atualizar imagem se enviar nova
    $caminho = $produto['imagem'];
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (in_array($extensao, $extensoesPermitidas)) {
            // Deletar imagem antiga, se existir e for um arquivo válido
            if (file_exists('../' . $produto['imagem'])) {
                unlink('../' . $produto['imagem']);
            }
            $nomeArquivo = uniqid() . '.' . $extensao;
            $caminho = 'uploads/' . $nomeArquivo;
            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
            move_uploaded_file($_FILES['imagem']['tmp_name'], '../' . $caminho);
        } else {
            $mensagem = "Extensão de imagem não permitida.";
        }
    }

    if (!$mensagem) {
        $sql = "UPDATE produtos SET nome='$nome', preco=$preco, descricao='$descricao', categoria='$categoria', imagem='$caminho' WHERE id=$id";
        if ($conn->query($sql)) {
            $mensagem = "Produto atualizado com sucesso!";
            // Atualiza os dados locais para mostrar no formulário
            $produto['nome'] = $nome;
            $produto['preco'] = $preco;
            $produto['descricao'] = $descricao;
            $produto['categoria'] = $categoria;
            $produto['imagem'] = $caminho;
        } else {
            $mensagem = "Erro ao atualizar produto: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Editar Produto</title>
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&family=Quicksand:wght@400;600&family=La+Belle+Aurore&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Quicksand', sans-serif;
      background-color: #F5E6E9;
      margin: 0;
      padding: 20px;
    }
    h2 {
      color: #af203f;
      text-align: center;
      margin: 30px 0 10px;
    }
    a {
      color: #af203f;
      text-decoration: none;
      font-weight: 600;
      margin: 20px;
      display: inline-block;
    }
    a:hover {
      text-decoration: underline;
    }
    form {
      background-color: #ffe4e7;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      max-width: 500px;
      margin: 0 auto 40px;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    label {
      color: #af203f;
      font-weight: 500;
    }
    input[type="text"],
    textarea,
    select {
      padding: 10px;
      border: 1px solid #af203f;
      border-radius: 10px;
      background-color: #fff;
      font-size: 16px;
      width: 100%;
      font-family: 'Quicksand', sans-serif;
    }
    textarea {
      resize: vertical;
    }
    input:focus,
    textarea:focus,
    select:focus {
      border-color: #af203f;
      box-shadow: 0 0 5px #af203f55;
      outline: none;
    }
    button {
      background-color: #af203f;
      color: #fff;
      padding: 12px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s;
      font-family: 'Quicksand', sans-serif;
    }
    button:hover {
      background-color: #cc2a50;
    }
    /* Esconde o input file original */
    input[type="file"] {
      opacity: 0;
      position: absolute;
      width: 0;
      height: 0;
      z-index: -1;
    }
    /* Label estilizado como botão para upload */
    .upload-btn {
      background-color: #af203f;
      color: #fff;
      padding: 10px 20px;
      border-radius: 12px;
      cursor: pointer;
      text-align: center;
      display: inline-block;
      font-family: 'Quicksand', sans-serif;
      font-weight: bold;
      transition: background-color 0.3s;
      user-select: none;
      width: fit-content;
      margin-top: -5px;
    }
    .upload-btn:hover {
      background-color: #cc2a50;
    }
    /* Texto que mostra o nome do arquivo escolhido */
    #nome-arquivo {
      margin-top: 10px;
      font-family: 'Quicksand', sans-serif;
      color: #af203f;
      font-weight: 600;
      font-size: 14px;
      min-height: 20px;
    }
    /* Imagem atual */
    img {
      max-width: 100%;
      border-radius: 15px;
      margin-bottom: 15px;
      border: 2px solid #af203f;
    }
    /* Mensagem de sucesso ou erro */
    .mensagem {
      max-width: 500px;
      margin: 0 auto 30px;
      background-color: #fff2f5;
      padding: 15px 20px;
      border-radius: 15px;
      color: #af203f;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      font-size: 16px;
      text-align: center;
    }
  </style>
</head>
<body>
  <h2>Editar Produto</h2>

  <?php if ($mensagem): ?>
    <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Nome:</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>

    <label>Preço (ex: 250,00):</label>
    <input type="text" name="preco" value="<?= number_format($produto['preco'], 2, ',', '.') ?>" required>

    <label>Descrição:</label>
    <textarea name="descricao" rows="4" required><?= htmlspecialchars($produto['descricao']) ?></textarea>

    <label>Categoria:</label>
    <select name="categoria" required>
      <option value="">-- Selecione uma categoria --</option>
      <option value="Decoração" <?= $produto['categoria'] == 'Decoração' ? 'selected' : '' ?>>Decoração</option>
      <option value="Acessórios" <?= $produto['categoria'] == 'Acessórios' ? 'selected' : '' ?>>Acessórios</option>
      <option value="Infantil" <?= $produto['categoria'] == 'Infantil' ? 'selected' : '' ?>>Infantil</option>
      <option value="Crochê" <?= $produto['categoria'] == 'Crochê' ? 'selected' : '' ?>>Crochê</option>
      <option value="Outros" <?= $produto['categoria'] == 'Outros' ? 'selected' : '' ?>>Outros</option>
    </select>

    <label>Imagem atual:</label>
    <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="Imagem do produto" style="max-width:200px; display:block; margin-bottom: 10px;">

    <label for="fileInput" class="upload-btn">Trocar imagem</label>
    <input type="file" id="fileInput" name="imagem" accept="image/*">

    <div id="nome-arquivo"></div>

    <button type="submit">Salvar Alterações</button>
  </form>

  <p><a href="listar_produtos_admin.php">Voltar para lista</a></p>

  <script>
    const inputFile = document.getElementById('fileInput');
    const nomeArquivo = document.getElementById('nome-arquivo');

    inputFile.addEventListener('change', () => {
      if (inputFile.files.length > 0) {
        nomeArquivo.textContent = "Arquivo selecionado: " + inputFile.files[0].name;
      } else {
        nomeArquivo.textContent = "";
      }
    });
  </script>
</body>
</html>
