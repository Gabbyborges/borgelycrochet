<?php
include('../db.php');

if (!isset($_GET['id'])) {
    echo "ID não informado.";
    exit;
}

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST['tipo'];
    $nome = $_POST['nome'];
    $cor = $_POST['cor'];
    $marca = $_POST['marca'];
    $quantidade = $_POST['quantidade'];
    $unidade = $_POST['unidade'];
    $preco = $_POST['preco_custo'];
    $data = $_POST['data_compra'];

    $sql = "UPDATE materiais SET tipo='$tipo', nome='$nome', cor='$cor', marca='$marca', quantidade='$quantidade', unidade='$unidade', preco_custo='$preco', data_compra='$data' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Material atualizado com sucesso!<br><a href='materiais.php'>Voltar</a>";
    } else {
        echo "Erro: " . $conn->error;
    }
    exit;
}

$sql = "SELECT * FROM materiais WHERE id=$id";
$result = $conn->query($sql);
$material = $result->fetch_assoc();
?>

<h2>✏️ Editar Material</h2>
<form method="post" action="">
  Tipo: <input type="text" name="tipo" value="<?= $material['tipo'] ?>" required><br><br>
  Nome: <input type="text" name="nome" value="<?= $material['nome'] ?>" required><br><br>
  Cor: <input type="text" name="cor" value="<?= $material['cor'] ?>"><br><br>
  Marca: <input type="text" name="marca" value="<?= $material['marca'] ?>"><br><br>
  Quantidade: <input type="number" name="quantidade" value="<?= $material['quantidade'] ?>" required><br><br>
  Unidade: <input type="text" name="unidade" value="<?= $material['unidade'] ?>" required><br><br>
  Preço de custo: <input type="number" step="0.01" name="preco_custo" value="<?= $material['preco_custo'] ?>"><br><br>
  Data da compra: <input type="date" name="data_compra" value="<?= $material['data_compra'] ?>"><br><br>
  <button type="submit">Salvar Alterações</button>
</form>
