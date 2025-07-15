<?php
include('../db.php');

if (!isset($_GET['id'])) {
    echo "ID não informado.";
    exit;
}

$id = $_GET['id'];

$sql = "DELETE FROM materiais WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Material excluído com sucesso!<br><a href='materiais.php'>Voltar</a>";
} else {
    echo "Erro ao excluir: " . $conn->error;
}
?>
