<?php
// Conexão
include("../db.php");
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// --------- Ações para Categorias ---------
if (isset($_POST['add_categoria'])) {
    $nome_cat = $conn->real_escape_string($_POST['nome_categoria']);
    $descricao = $conn->real_escape_string($_POST['descricao_categoria']);
    if ($nome_cat) {
        $conn->query("INSERT INTO categorias_materiais (nome, descricao) VALUES ('$nome_cat', '$descricao')");
    }
}

if (isset($_POST['edit_categoria'])) {
    $id = (int)$_POST['categoria_id'];
    $nome_cat = $conn->real_escape_string($_POST['nome_categoria']);
    $descricao = $conn->real_escape_string($_POST['descricao_categoria']);
    if ($nome_cat && $id) {
        $conn->query("UPDATE categorias_materiais SET nome='$nome_cat', descricao='$descricao' WHERE id=$id");
    }
}

if (isset($_POST['delete_categoria'])) {
    $id = (int)$_POST['categoria_id'];
    if ($id) {
        $conn->query("DELETE FROM categorias_materiais WHERE id=$id");
    }
}

// --------- Ações para Tipos ---------
if (isset($_POST['add_tipo'])) {
    $nome_tipo = $conn->real_escape_string($_POST['nome_tipo']);
    $categoria_id = (int)$_POST['categoria_id'];
    $descricao = $conn->real_escape_string($_POST['descricao_tipo']);
    if ($nome_tipo && $categoria_id) {
        $conn->query("INSERT INTO tipos_materiais (nome, categoria_id, descricao) VALUES ('$nome_tipo', $categoria_id, '$descricao')");
    }
}

if (isset($_POST['edit_tipo'])) {
    $id = (int)$_POST['tipo_id'];
    $nome_tipo = $conn->real_escape_string($_POST['nome_tipo']);
    $categoria_id = (int)$_POST['categoria_id'];
    $descricao = $conn->real_escape_string($_POST['descricao_tipo']);
    if ($nome_tipo && $categoria_id && $id) {
        $conn->query("UPDATE tipos_materiais SET nome='$nome_tipo', categoria_id=$categoria_id, descricao='$descricao' WHERE id=$id");
    }
}

if (isset($_POST['delete_tipo'])) {
    $id = (int)$_POST['tipo_id'];
    if ($id) {
        $conn->query("DELETE FROM tipos_materiais WHERE id=$id");
    }
}

// --------- Ações para Materiais ---------
if (isset($_POST['add_material'])) {
    $categoria_id = (int)$_POST['categoria_id_material'];
    $tipo_id = (int)$_POST['tipo_id_material'];
    $cor = $conn->real_escape_string($_POST['cor']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $quantidade = (int)$_POST['quantidade'];
    $unidade = $conn->real_escape_string($_POST['unidade']);
    $preco_custo = (float)$_POST['preco_custo'];
    $data_compra = $_POST['data_compra'];
    $observacoes = $conn->real_escape_string($_POST['observacoes']);

    // Puxar nome do tipo para salvar no materiais.nome
    $resTipo = $conn->query("SELECT nome FROM tipos_materiais WHERE id = $tipo_id");
    $rowTipo = $resTipo->fetch_assoc();
    $nome_tipo = $rowTipo ? $rowTipo['nome'] : '';

    if ($categoria_id && $tipo_id && $nome_tipo) {
        $sql = "INSERT INTO materiais (tipo, nome, cor, marca, quantidade, unidade, preco_custo, data_compra, observacoes)
                VALUES ((SELECT nome FROM categorias_materiais WHERE id = $categoria_id), '$nome_tipo', '$cor', '$marca', $quantidade, '$unidade', $preco_custo, '$data_compra', '$observacoes')";
        $conn->query($sql);
    }
}

if (isset($_POST['edit_material'])) {
    $id = (int)$_POST['material_id'];
    $categoria_id = (int)$_POST['categoria_id_material'];
    $tipo_id = (int)$_POST['tipo_id_material'];
    $cor = $conn->real_escape_string($_POST['cor']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $quantidade = (int)$_POST['quantidade'];
    $unidade = $conn->real_escape_string($_POST['unidade']);
    $preco_custo = (float)$_POST['preco_custo'];
    $data_compra = $_POST['data_compra'];
    $observacoes = $conn->real_escape_string($_POST['observacoes']);

    $resTipo = $conn->query("SELECT nome FROM tipos_materiais WHERE id = $tipo_id");
    $rowTipo = $resTipo->fetch_assoc();
    $nome_tipo = $rowTipo ? $rowTipo['nome'] : '';

    if ($categoria_id && $tipo_id && $nome_tipo && $id) {
        $sql = "UPDATE materiais SET 
                tipo=(SELECT nome FROM categorias_materiais WHERE id = $categoria_id), 
                nome='$nome_tipo', cor='$cor', marca='$marca', quantidade=$quantidade, 
                unidade='$unidade', preco_custo=$preco_custo, data_compra='$data_compra', 
                observacoes='$observacoes' WHERE id=$id";
        $conn->query($sql);
    }
}

if (isset($_POST['delete_material'])) {
    $id = (int)$_POST['material_id'];
    if ($id) {
        $conn->query("DELETE FROM materiais WHERE id=$id");
    }
}

// ------- Buscar dados para exibir --------
$categorias = $conn->query("SELECT * FROM categorias_materiais ORDER BY nome");
$tipos = $conn->query("SELECT t.*, c.nome AS categoria_nome FROM tipos_materiais t LEFT JOIN categorias_materiais c ON t.categoria_id = c.id ORDER BY c.nome, t.nome");
$materiais = $conn->query("SELECT m.*, c.nome AS categoria_nome FROM materiais m LEFT JOIN categorias_materiais c ON c.nome = m.tipo ORDER BY m.criado_em DESC");

// Script para popular categorias iniciais
if (isset($_POST['popular_categorias'])) {
    $categorias_iniciais = [
        ['nome' => 'Agulha', 'descricao' => 'Agulhas para crochê e tricô'],
        ['nome' => 'Linha', 'descricao' => 'Linhas e fios para trabalhos manuais'],
        ['nome' => 'Enchimento', 'descricao' => 'Materiais para enchimento (fibra, algodão, etc.)'],
        ['nome' => 'Olhos de Segurança', 'descricao' => 'Olhos de segurança para amigurumi'],
        ['nome' => 'Tag Personaliza', 'descricao' => 'Tags e etiquetas personalizadas'],
        ['nome' => 'Papelaria', 'descricao' => 'Materiais de papelaria e escritório']
    ];

    foreach ($categorias_iniciais as $cat) {
        $nome = $conn->real_escape_string($cat['nome']);
        $desc = $conn->real_escape_string($cat['descricao']);
        $conn->query("INSERT IGNORE INTO categorias_materiais (nome, descricao) VALUES ('$nome', '$desc')");
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Materiais - Painel Admin</title>
    <style>
        :root {
            --primary-color: #b86f87;
            --primary-light: #b86f87;
            --primary-dark: #b86f87;
            --secondary-color: #f8bbd9;
            --background: #fafafa;
            --surface: #ffffff;
            --text-primary: #333333;
            --text-secondary: #666666;
            --border: #e0e0e0;
            --error: #d32f2f;
            --success: #2e7d32;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(184, 111, 135, 0.3);
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .card {
            background: var(--surface);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--secondary-color);
        }

        .card-title {
            font-size: 1.5em;
            color: var(--primary-color);
            font-weight: 600;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: var(--text-primary);
        }

        .btn-danger {
            background: var(--error);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text-primary);
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--surface);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background: var(--secondary-color);
            color: var(--text-primary);
            font-weight: 600;
        }

        tr:hover {
            background: rgba(184, 111, 135, 0.05);
        }

        .actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: var(--surface);
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: var(--text-secondary);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: var(--text-primary);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--surface);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: var(--primary-color);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #e8f5e8;
            border-color: var(--success);
            color: var(--success);
        }

        .alert-error {
            background: #fdeaea;
            border-color: var(--error);
            color: var(--error);
        }

        .collapsible-section {
            margin-bottom: 30px;
        }

        .collapsible-header {
            background: var(--primary-color);
            color: white;
            padding: 20px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(184, 111, 135, 0.3);
        }

        .collapsible-header:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .collapsible-header h2 {
            margin: 0;
            font-size: 1.4em;
            color: white;
        }

        .toggle-icon {
            font-size: 1.2em;
            transition: transform 0.3s ease;
        }

        .collapsible-content {
            display: none;
            padding: 20px 0;
            animation: slideDown 0.3s ease;
        }

        .collapsible-content.active {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .material-section {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .material-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px;
            text-align: center;
        }

        .material-header h2 {
            margin: 0;
            font-size: 2em;
            color: white;
        }

        .search-bar {
            background: white;
            padding: 20px;
            border-bottom: 1px solid var(--border);
        }

        .search-input {
            width: 100%;
            padding: 15px;
            border: 2px solid var(--border);
            border-radius: 25px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
        }
           @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧵 Gestão de Materiais</h1>
            <p>Sistema completo para organização de materiais de crochê e papelaria</p>
        </div>

        <!-- Estatísticas -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $categorias->num_rows ?></div>
                <div>Categorias</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $tipos->num_rows ?></div>
                <div>Tipos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $materiais->num_rows ?></div>
                <div>Materiais</div>
            </div>
        </div>

        <!-- Seções Recolhíveis para Configuração -->
        <div class="collapsible-section">
            <div class="collapsible-header" onclick="toggleSection('configSection')">
                <h2>⚙️ Configuração do Sistema</h2>
                <span class="toggle-icon">▼</span>
            </div>
            <div id="configSection" class="collapsible-content">
                <!-- Botão para popular categorias iniciais -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">🚀 Configuração Inicial</h2>
                    </div>
                    <form method="post" action="">
                        <p>Clique no botão abaixo para adicionar as categorias básicas (Agulha, Linha, Enchimento, Olhos de Segurança, Tag Personaliza, Papelaria):</p>
                        <button type="submit" name="popular_categorias" class="btn btn-success">Popular Categorias Iniciais</button>
                    </form>
                </div>

                <!-- Gestão de Categorias -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">📂 Categorias de Materiais</h2>
                        <button class="btn btn-primary" onclick="openModal('modalAddCategoria')">Adicionar Categoria</button>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $categorias->data_seek(0);
                                while($cat = $categorias->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><?= $cat['id'] ?></td>
                                    <td><?= htmlspecialchars($cat['nome']) ?></td>
                                    <td><?= htmlspecialchars($cat['descricao'] ?? '') ?></td>
                                    <td class="actions">
                                        <button class="btn btn-secondary btn-sm" onclick="editCategoria(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['nome']) ?>', '<?= htmlspecialchars($cat['descricao'] ?? '') ?>')">Editar</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteCategoria(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['nome']) ?>')">Excluir</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Gestão de Tipos -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">🧵 Tipos de Materiais</h2>
                        <button class="btn btn-primary" onclick="openModal('modalAddTipo')">Adicionar Tipo</button>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Categoria</th>
                                    <th>Descrição</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $tipos->data_seek(0);
                                while($tipo = $tipos->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><?= $tipo['id'] ?></td>
                                    <td><?= htmlspecialchars($tipo['nome']) ?></td>
                                    <td><?= htmlspecialchars($tipo['categoria_nome']) ?></td>
                                    <td><?= htmlspecialchars($tipo['descricao'] ?? '') ?></td>
                                    <td class="actions">
                                        <button class="btn btn-secondary btn-sm" onclick="editTipo(<?= $tipo['id'] ?>, '<?= htmlspecialchars($tipo['nome']) ?>', <?= $tipo['categoria_id'] ?>, '<?= htmlspecialchars($tipo['descricao'] ?? '') ?>')">Editar</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteTipo(<?= $tipo['id'] ?>, '<?= htmlspecialchars($tipo['nome']) ?>')">Excluir</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestão de Materiais - Seção Principal -->
        <div class="material-section">
            <div class="material-header">
                <h2>📦 Materiais em Estoque</h2>
                <p>Gerencie todos os seus materiais de forma organizada</p>
            </div>
            
            <div class="search-bar">
                <input type="text" class="search-input" id="searchInput" placeholder="🔍 Buscar materiais por nome, cor, marca..." onkeyup="buscarMateriais()">
            </div>
            
            <div class="card" style="border-radius: 0; box-shadow: none; margin: 0;">
                <div class="card-header">
                    <h3 class="card-title">Lista de Materiais</h3>
                    <button class="btn btn-primary" onclick="openModal('modalAddMaterial')">➕ Adicionar Material</button>
                </div>
                
                <div class="table-container">
                    <table id="materialsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th>Cor</th>
                                <th>Marca</th>
                                <th>Quantidade</th>
                                <th>Preço</th>
                                <th>Data Compra</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($material = $materiais->fetch_assoc()): ?>
                            <tr>
                                <td><?= $material['id'] ?></td>
                                <td><?= htmlspecialchars($material['tipo']) ?></td>
                                <td><?= htmlspecialchars($material['nome']) ?></td>
                                <td><?= htmlspecialchars($material['cor']) ?></td>
                                <td><?= htmlspecialchars($material['marca']) ?></td>
                                <td><?= $material['quantidade'] ?> <?= htmlspecialchars($material['unidade']) ?></td>
                                <td>R$ <?= number_format($material['preco_custo'], 2, ',', '.') ?></td>
                                <td><?= $material['data_compra'] ? date('d/m/Y', strtotime($material['data_compra'])) : '' ?></td>
                                <td class="actions">
                                    <button class="btn btn-secondary btn-sm" onclick="editMaterial(<?= $material['id'] ?>, '<?= htmlspecialchars($material['tipo']) ?>', '<?= htmlspecialchars($material['nome']) ?>', '<?= htmlspecialchars($material['cor']) ?>', '<?= htmlspecialchars($material['marca']) ?>', <?= $material['quantidade'] ?>, '<?= htmlspecialchars($material['unidade']) ?>', <?= $material['preco_custo'] ?>, '<?= $material['data_compra'] ?>', '<?= htmlspecialchars($material['observacoes'] ?? '') ?>')">✏️</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteMaterial(<?= $material['id'] ?>, '<?= htmlspecialchars($material['nome']) ?>')">🗑️</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>

    <!-- Modais -->
    
    <!-- Modal Adicionar Categoria -->
    <div id="modalAddCategoria" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modalAddCategoria')">&times;</span>
            <h2>Adicionar Nova Categoria</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label>Nome da Categoria:</label>
                    <input type="text" name="nome_categoria" required>
                </div>
                <div class="form-group">
                    <label>Descrição:</label>
                    <textarea name="descricao_categoria"></textarea>
                </div>
                <button type="submit" name="add_categoria" class="btn btn-primary">Adicionar Categoria</button>
            </form>
        </div>
    </div>

    <!-- Modal Editar Categoria -->
    <div id="modalEditCategoria" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modalEditCategoria')">&times;</span>
            <h2>Editar Categoria</h2>
            <form method="post" action="">
                <input type="hidden" name="categoria_id" id="editCategoriaId">
                <div class="form-group">
                    <label>Nome da Categoria:</label>
                    <input type="text" name="nome_categoria" id="editCategoriaNome" required>
                </div>
                <div class="form-group">
                    <label>Descrição:</label>
                    <textarea name="descricao_categoria" id="editCategoriaDescricao"></textarea>
                </div>
                <button type="submit" name="edit_categoria" class="btn btn-primary">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <!-- Modal Adicionar Tipo -->
    <div id="modalAddTipo" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modalAddTipo')">&times;</span>
            <h2>Adicionar Novo Tipo</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label>Categoria:</label>
                    <select name="categoria_id" required>
                        <option value="">Selecione a Categoria</option>
                        <?php
                        $categorias->data_seek(0);
                        while($cat = $categorias->fetch_assoc()):
                        ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nome do Tipo:</label>
                    <input type="text" name="nome_tipo" required>
                </div>
                <div class="form-group">
                    <label>Descrição:</label>
                    <textarea name="descricao_tipo"></textarea>
                </div>
                <button type="submit" name="add_tipo" class="btn btn-primary">Adicionar Tipo</button>
            </form>
        </div>
    </div>

    <!-- Modal Adicionar Material -->
    <div id="modalAddMaterial" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modalAddMaterial')">&times;</span>
            <h2>Adicionar Material ao Estoque</h2>
            <form method="post" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label>Categoria:</label>
                        <select id="categoria_id_material" name="categoria_id_material" required onchange="carregarTipos()">
                            <option value="">Selecione a Categoria</option>
                            <?php
                            $categorias->data_seek(0);
                            while($cat = $categorias->fetch_assoc()):
                            ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipo:</label>
                        <select id="tipo_id_material" name="tipo_id_material" required>
                            <option value="">Selecione a Categoria Primeiro</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Cor:</label>
                        <input type="text" name="cor">
                    </div>
                    <div class="form-group">
                        <label>Marca:</label>
                        <input type="text" name="marca">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Quantidade:</label>
                        <input type="number" name="quantidade" required>
                    </div>
                    <div class="form-group">
                        <label>Unidade:</label>
                        <input type="text" name="unidade" placeholder="ex: un, gr, m" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Preço de Custo (R$):</label>
                        <input type="number" step="0.01" name="preco_custo">
                    </div>
                    <div class="form-group">
                        <label>Data da Compra:</label>
                        <input type="date" name="data_compra">
                    </div>
                </div>
                <div class="form-group">
                    <label>Observações:</label>
                    <textarea name="observacoes"></textarea>
                </div>
                <button type="submit" name="add_material" class="btn btn-primary">Adicionar Material</button>
            </form>
        </div>
    </div>

    <script>
        // Dados dos tipos para filtro por categoria
        const tipos = <?= json_encode($tipos->fetch_all(MYSQLI_ASSOC)) ?>;

        function carregarTipos() {
            const categoriaSelect = document.getElementById('categoria_id_material');
            const tipoSelect = document.getElementById('tipo_id_material');
            const categoriaId = categoriaSelect.value;

            tipoSelect.innerHTML = '<option value="">Selecione o Tipo</option>';

            tipos.forEach(tipo => {
                if(tipo.categoria_id == categoriaId) {
                    const option = document.createElement('option');
                    option.value = tipo.id;
                    option.text = tipo.nome;
                    tipoSelect.appendChild(option);
                }
            });
        }

        // Funções para modais
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for(let i = 0; i < modals.length; i++) {
                if (event.target === modals[i]) {
                    modals[i].style.display = 'none';
                }
            }
        }

        // Funções para edição de categorias
        function editCategoria(id, nome, descricao) {
            document.getElementById('editCategoriaId').value = id;
            document.getElementById('editCategoriaNome').value = nome;
            document.getElementById('editCategoriaDescricao').value = descricao;
            openModal('modalEditCategoria');
        }

        function deleteCategoria(id, nome) {
            if(confirm(`Tem certeza que deseja excluir a categoria "${nome}"? Esta ação não pode ser desfeita.`)) {
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = `
                    <input type="hidden" name="categoria_id" value="${id}">
                    <input type="hidden" name="delete_categoria" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Funções para edição de tipos
        function editTipo(id, nome, categoriaId, descricao) {
            const form = document.createElement('form');
            form.method = 'post';
            form.innerHTML = `
                <input type="hidden" name="tipo_id" value="${id}">
                <input type="hidden" name="nome_tipo" value="${nome}">
                <input type="hidden" name="categoria_id" value="${categoriaId}">
                <input type="hidden" name="descricao_tipo" value="${descricao}">
                <input type="hidden" name="edit_tipo" value="1">
            `;
            document.body.appendChild(form);
            // Para simplificar, vamos apenas redirecionar para edição
            alert('Funcionalidade de edição de tipos será implementada em breve!');
        }

        function deleteTipo(id, nome) {
            if(confirm(`Tem certeza que deseja excluir o tipo "${nome}"? Esta ação não pode ser desfeita.`)) {
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = `
                    <input type="hidden" name="tipo_id" value="${id}">
                    <input type="hidden" name="delete_tipo" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Funções para edição de materiais
        function editMaterial(id, tipo, nome, cor, marca, quantidade, unidade, preco, data, observacoes) {
            // Para simplificar, vamos apenas mostrar um alert
            alert('Funcionalidade de edição de materiais será implementada em breve!');
        }

        function deleteMaterial(id, nome) {
            if(confirm(`Tem certeza que deseja excluir o material "${nome}"? Esta ação não pode ser desfeita.`)) {
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = `
                    <input type="hidden" name="material_id" value="${id}">
                    <input type="hidden" name="delete_material" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Adicionar exemplos de tipos por categoria
        function adicionarExemplosTipos() {
            const exemplosTipos = {
                'Linha': [
                    'Linha Anne',
                    'Linha Amigurumi',
                    'Linha Charme',
                    'Linha Duna',
                    'Linha Barroco',
                    'Linha Bella',
                    'Barbante',
                    'Fio de Malha'
                ],
                'Agulha': [
                    'Agulha 2.0mm',
                    'Agulha 2.5mm',
                    'Agulha 3.0mm',
                    'Agulha 3.5mm',
                    'Agulha 4.0mm',
                    'Agulha 4.5mm',
                    'Agulha 5.0mm',
                    'Agulha 5.5mm',
                    'Agulha 6.0mm'
                ],
                'Enchimento': [
                    'Fibra Siliconada',
                    'Algodão',
                    'Manta Acrílica',
                    'Espuma'
                ],
                'Olhos de Segurança': [
                    'Olhos 6mm',
                    'Olhos 8mm',
                    'Olhos 10mm',
                    'Olhos 12mm',
                    'Olhos 15mm',
                    'Olhos 18mm',
                    'Olhos 20mm'
                ],
                'Tag Personaliza': [
                    'Tag Papel',
                    'Tag Tecido',
                    'Tag Plástica',
                    'Etiqueta Adesiva'
                ],
                'Papelaria': [
                    'Caneta',
                    'Lápis',
                    'Borracha',
                    'Régua',
                    'Tesoura',
                    'Cola',
                    'Papel Sulfite',
                    'Papel Colorido',
                    'Marcador de Texto'
                ]
            };

            return exemplosTipos;
        }

        // Função para sugerir tipos baseado na categoria selecionada
        function sugerirTipos() {
            const categoriaSelect = document.getElementById('categoria_id_material');
            const tipoInput = document.querySelector('input[name="nome_tipo"]');
            
            if (categoriaSelect && tipoInput) {
                const categoriaSelecionada = categoriaSelect.options[categoriaSelect.selectedIndex].text;
                const exemplos = adicionarExemplosTipos();
                
                if (exemplos[categoriaSelecionada]) {
                    const sugestoes = exemplos[categoriaSelecionada].join(', ');
                    tipoInput.placeholder = `Sugestões: ${sugestoes}`;
                }
            }
        }

        // Validação de formulários
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar validação aos formulários
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.style.borderColor = 'var(--error)';
                            isValid = false;
                        } else {
                            field.style.borderColor = 'var(--border)';
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        alert('Por favor, preencha todos os campos obrigatórios.');
                    }
                });
            });
        });

        // Função para alternar seções recolhíveis
        function toggleSection(sectionId) {
            const content = document.getElementById(sectionId);
            const header = content.previousElementSibling;
            const icon = header.querySelector('.toggle-icon');
            
            if (content.classList.contains('active')) {
                content.classList.remove('active');
                content.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            } else {
                content.classList.add('active');
                content.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            }
        }

        // Função para buscar materiais
        function buscarMateriais() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('materialsTable');
            const tbody = table.getElementsByTagName('tbody')[0];
            const rows = tbody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                // Buscar em todas as colunas exceto a de ações (última)
                for (let j = 0; j < cells.length - 1; j++) {
                    if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html>