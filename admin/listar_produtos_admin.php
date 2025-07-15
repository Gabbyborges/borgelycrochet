<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

include("../db.php"); // Caminho ajustado

// Excluir produto
if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);
    
    // Buscar imagem para deletar
    $res = $conn->query("SELECT imagem FROM produtos WHERE id=$idExcluir");
    if ($res && $res->num_rows == 1) {
        $produto = $res->fetch_assoc();
        $caminhoImagem = "../" . $produto['imagem']; // Caminho completo para deletar o arquivo

        if (file_exists($caminhoImagem)) {
            unlink($caminhoImagem);
        }
    }

    $conn->query("DELETE FROM produtos WHERE id=$idExcluir");
    header("Location: listar_produtos_admin.php");
    exit;
}

$result = $conn->query("SELECT * FROM produtos ORDER BY id DESC");

// Buscar estatísticas
$totalProdutos = $result->num_rows;
$resultPedidos = $conn->query("SELECT COUNT(*) as total FROM pedidos");
$totalPedidos = $resultPedidos ? $resultPedidos->fetch_assoc()['total'] : 0;
$resultClientes = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$totalClientes = $resultClientes ? $resultClientes->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=La+Belle+Aurore&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #b86f87;             
            --primary-light: #b86f87;             
            --primary-dark: #b86f87;             
            --secondary-color: #f8bbd9;             
            --soft-pink: #fce4ec;             
            --accent-color: #ff6b9d;             
            --white: #ffffff;             
            --gray-50: #f9fafb;             
            --gray-100: #f3f4f6;             
            --gray-200: #e5e7eb;             
            --gray-300: #d1d5db;             
            --gray-400: #9ca3af;             
            --gray-500: #6b7280;             
            --gray-600: #4b5563;             
            --gray-700: #374151;             
            --gray-800: #1f2937;             
            --gray-900: #111827;             
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);             
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);             
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);             
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);             
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(135deg, var(--soft-pink) 0%, var(--secondary-color) 50%, var(--primary-color) 100%);
            min-height: 100vh;
            color: var(--gray-800);
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--white);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 10px;
        }

        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            font-weight: 400;
        }

        /* Action Cards */
        .action-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            background: rgba(255, 255, 255, 1);
            text-decoration: none;
            color: inherit;
        }

        .action-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .action-card h3 {
            color: var(--gray-800);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        /* Stats Section */
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--white);
            font-size: 2rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .stat-sublabel {
            font-size: 0.9rem;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 5px;
        }

        /* Products Section */
        .products-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--gray-200);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            box-shadow: var(--shadow-md);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            text-decoration: none;
            color: var(--white);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-2px);
            text-decoration: none;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: var(--white);
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: 1px solid var(--gray-200);
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
        }

        .product-image {
            width: 100%;
            height: 180px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 15px;
            border: 2px solid var(--gray-200);
        }

        .no-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, var(--soft-pink) 0%, var(--secondary-color) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1rem;
            margin-bottom: 15px;
            border: 2px solid var(--gray-200);
        }

        .product-id {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary-color);
            color: var(--white);
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .product-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .product-description {
            color: var(--gray-600);
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 0.8rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-edit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: var(--white);
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            text-decoration: none;
            color: var(--white);
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--accent-color) 0%, #e11d48 100%);
            color: var(--white);
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            text-decoration: none;
            color: var(--white);
        }

        .exit-btn {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: var(--white);
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .exit-btn:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-xl);
            text-decoration: none;
            color: var(--white);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-600);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--gray-400);
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--gray-700);
        }

        .empty-state p {
            font-size: 1.1rem;
            color: var(--gray-500);
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .action-section {
                grid-template-columns: 1fr;
            }

            .stats-section {
                grid-template-columns: 1fr;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .section-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .exit-btn {
                bottom: 20px;
                left: 20px;
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-box-open"></i> Gerenciar Produtos</h1>
            <p>Painel de Gerenciamento de Produtos</p>
        </div>

        <!-- Action Cards -->
        <div class="action-section">
            <a href="cadastro_produto.php" class="action-card">
                <i class="fas fa-plus-circle"></i>
                <h3>Cadastrar Produto</h3>
            </a>
            <a href="listar_produtos_admin.php" class="action-card">
                <i class="fas fa-list-ul"></i>
                <h3>Gerenciar Produtos</h3>
            </a>
            <a href="categorias.php" class="action-card">
                <i class="fas fa-tags"></i>
                <h3>Gerenciar Categorias</h3>
            </a>
            <a href="estoque.php" class="action-card">
                <i class="fas fa-warehouse"></i>
                <h3>Gerenciar Estoque</h3>
            </a>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-number"><?= $totalProdutos ?></div>
                <div class="stat-label">Total de Produtos</div>
                <div class="stat-sublabel">PRODUTOS CADASTRADOS</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-number"><?= $totalPedidos ?></div>
                <div class="stat-label">Total de Pedidos</div>
                <div class="stat-sublabel">PEDIDOS REALIZADOS</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?= $totalClientes ?></div>
                <div class="stat-label">Total de Clientes</div>
                <div class="stat-sublabel">CLIENTES CADASTRADOS</div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="products-section">
            <div class="section-header">
                <h2 class="section-title">Produtos Cadastrados</h2>
                <a href="cadastro_produto.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Produto
                </a>
            </div>

            <?php if ($totalProdutos > 0): ?>
                <div class="products-grid">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="product-card">
                            <div class="product-id">#<?= $row['id'] ?></div>
                            
                            <?php if (!empty($row['imagem']) && file_exists("../" . $row['imagem'])): ?>
                                <img src="../<?= htmlspecialchars($row['imagem']) ?>" 
                                     alt="<?= htmlspecialchars($row['nome']) ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                    <span style="margin-left: 10px;">Sem imagem</span>
                                </div>
                            <?php endif; ?>

                            <div class="product-name"><?= htmlspecialchars($row['nome']) ?></div>
                            <div class="product-price">R$ <?= number_format($row['preco'], 2, ',', '.') ?></div>
                            <div class="product-description"><?= htmlspecialchars($row['descricao']) ?></div>

                            <div class="product-actions">
                                <a href="editar_produto.php?id=<?= $row['id'] ?>" class="btn-small btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="javascript:void(0)" 
                                   class="btn-small btn-delete" 
                                   onclick="confirmarExclusao(<?= $row['id'] ?>, '<?= addslashes($row['nome']) ?>')">
                                    <i class="fas fa-trash"></i> Excluir
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>Nenhum produto cadastrado</h3>
                    <p>Clique em "Novo Produto" para adicionar seu primeiro produto</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Exit Button -->
    <a href="painel_admin.php" class="exit-btn" title="Voltar ao Painel">
        <i class="fas fa-sign-out-alt"></i>
    </a>

    <script>
        // Função para confirmar exclusão
        function confirmarExclusao(id, nome) {
            if (confirm(`Tem certeza que deseja excluir o produto "${nome}"?`)) {
                window.location.href = `listar_produtos_admin.php?excluir=${id}`;
            }
        }

        // Adicionar animação de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.product-card, .action-card, .stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>