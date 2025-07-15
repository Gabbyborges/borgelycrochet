<?php
include('../db.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST['tipo'];
    $nome = $_POST['nome'] ?? '';
    $cor = $_POST['cor'];
    $marca = $_POST['marca'];
    $quantidade = $_POST['quantidade'];
    $unidade = $_POST['unidade'];
    $preco = $_POST['preco_custo'];
    $data = $_POST['data_compra'];

    $sql = "INSERT INTO materiais (tipo, nome, cor, marca, quantidade, unidade, preco_custo, data_compra)
            VALUES ('$tipo', '$nome', '$cor', '$marca', '$quantidade', '$unidade', '$preco', '$data')";

    if ($conn->query($sql) === TRUE) {
        echo "Material adicionado com sucesso!<br><a href='materiais.php'>Voltar</a>";
    } else {
        echo "Erro: " . $conn->error;
    }
    exit;
}
?>

<h2>📦 Adicionar Material</h2>
<form method="post" action="">
  <label>Categoria (tipo):</label><br>
  <select name="tipo" id="tipo" onchange="mostrarOpcoes()" required>
    <option value="">Selecione</option>
    <option value="Linha">Linha</option>
    <option value="Agulha">Agulha</option>
    <option value="Enchimento">Enchimento</option>
    <option value="Olhos">Olhos</option>
    <option value="Outros">Outros</option>
  </select><br><br>

  <div id="opcoesLinha" style="display:none;">
    <label>Tipo de Linha:</label><br>
    <select name="nome">
      <option value="Linha Amigurumi">Linha Amigurumi</option>
      <option value="Linha Amigurumi Soft">Linha Amigurumi Soft</option>
      <option value="Linha Amigurumi Pelúcia">Linha Amigurumi Pelúcia</option>
      <option value="Linha Charme">Linha Charme</option>
      <option value="Linha Cléia Duplo">Linha Cléia Duplo</option>
      <option value="Linha Anne">Linha Anne</option>
      <option value="Linha Cléa 5">Linha Cléa 5</option>
    </select><br><br>
  </div>

  <div id="campoNome" style="display:block;">
    <label>Nome do Material:</label><br>
    <input type="text" name="nome"><br><br>
  </div>

  <label>Cor:</label><br>
  <input type="text" name="cor"><br><br>

  <label>Marca:</label><br>
  <input type="text" name="marca"><br><br>

  <label>Quantidade:</label><br>
  <input type="number" name="quantidade" required><br><br>

  <label>Unidade (ex: un, gr):</label><br>
  <input type="text" name="unidade" required><br><br>

  <label>Preço de custo (R$):</label><br>
  <input type="number" step="0.01" name="preco_custo"><br><br>

  <label>Data da compra:</label><br>
  <input type="date" name="data_compra"><br><br>

  <button type="submit">Salvar</button>
</form>

<script>
function mostrarOpcoes() {
  const tipo = document.getElementById("tipo").value;
  const opcoesLinha = document.getElementById("opcoesLinha");
  const campoNome = document.getElementById("campoNome");

  if (tipo === "Linha") {
    opcoesLinha.style.display = "block";
    campoNome.style.display = "none";
  } else {
    opcoesLinha.style.display = "none";
    campoNome.style.display = "block";
  }
}
</script>
