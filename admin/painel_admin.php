<?php 
include('../db.php');  session_start(); 


if (!isset($_SESSION['admin_id'])) {   
    header("Location: login_admin.php");   
    exit; 
}  

// Consultas 
$qtdProdutos = $conn->query("SELECT COUNT(*) as total FROM produtos")->fetch_assoc()['total']; 
$qtdPedidos = $conn->query("SELECT COUNT(*) as total FROM pedidos")->fetch_assoc()['total']; 
$qtdClientes = $conn->query("SELECT COUNT(*) as total FROM clientes")->fetch_assoc()['total']; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--soft-pink) 100%);
            min-height: 100vh;
            padding: 20px;
            color: var(--gray-700);
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: var(--white);
            border-radius: 24px;
            padding: 32px 40px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-lg);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .header h1 {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header .subtitle {
            color: var(--gray-500);
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Navigation */
        .navigation {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .nav-item {
            text-decoration: none;
            background: var(--white);
            color: var(--gray-700);
            padding: 20px 24px;
            border-radius: 16px;
            transition: var(--transition);
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-100);
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            opacity: 0;
            transition: var(--transition);
        }

        .nav-item:hover::before {
            opacity: 1;
        }

        .nav-item:hover {
            color: var(--white);
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .nav-item i {
            font-size: 1.2rem;
            position: relative;
            z-index: 1;
        }

        .nav-item span {
            position: relative;
            z-index: 1;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 20px;
            padding: 32px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid var(--gray-100);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .stat-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.8rem;
        }

        .stat-card h3 {
            color: var(--gray-700);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--gray-500);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Logout Button */
        .logout-btn {
            background: linear-gradient(135deg, #dc2626, #ef4444) !important;
            color: var(--white) !important;
        }

        .logout-btn::before {
            background: linear-gradient(135deg, #b91c1c, #dc2626) !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 0 10px;
            }

            .header {
                padding: 24px 20px;
                margin-bottom: 24px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .navigation {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .nav-item {
                padding: 16px 20px;
            }

            .stats-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .stat-card {
                padding: 24px;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header, .nav-item, .stat-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .nav-item:nth-child(2) { animation-delay: 0.1s; }
        .nav-item:nth-child(3) { animation-delay: 0.2s; }
        .nav-item:nth-child(4) { animation-delay: 0.3s; }
        .nav-item:nth-child(5) { animation-delay: 0.4s; }
        .nav-item:nth-child(6) { animation-delay: 0.5s; }
        .nav-item:nth-child(7) { animation-delay: 0.6s; }

        .stat-card:nth-child(1) { animation-delay: 0.2s; }
        .stat-card:nth-child(2) { animation-delay: 0.3s; }
        .stat-card:nth-child(3) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <h1>Bem-vinda, <?= htmlspecialchars($_SESSION['admin_usuario']) ?>!</h1>
            <p class="subtitle">Painel de Administração</p>
        </div>

        <!-- Navigation -->
        <nav class="navigation">
            <a href="cadastro_produto.php" class="nav-item">
                <i class="fas fa-plus-circle"></i>
                <span>Cadastrar Produto</span>
            </a>
            <a href="listar_produtos_admin.php" class="nav-item">
                <i class="fas fa-boxes"></i>
                <span>Gerenciar Produtos</span>
            </a>
            <a href="listar_pedidos.php" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Gerenciar Pedidos</span>
            </a>
            <a href="listar_clientes.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Clientes Cadastrados</span>
            </a>
            <a href="novo_post.php" class="nav-item">
                <i class="fas fa-pen"></i>
                <span>Novo Post no Blog</span>
            </a>
            <a href="gerenciar_posts.php" class="nav-item">
                <i class="fas fa-blog"></i>
                <span>Gerenciar Posts do Blog</span>
            </a>
            <a href="materiais.php" class="nav-item">
                <i class="fas fa-boxes-stacked"></i>
                <span>Gerenciar Estoque</span>
            </a>
             <a href="financeiro.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Gerenciar Financeiro</span>
            </a>
            <a href="../cliente/logout.php" class="nav-item logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </nav>

        <!-- Stats Cards -->
        <section class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h3>Total de Produtos</h3>
                <div class="stat-number"><?= $qtdProdutos ?></div>
                <div class="stat-label">Produtos Cadastrados</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h3>Total de Pedidos</h3>
                <div class="stat-number"><?= $qtdPedidos ?></div>
                <div class="stat-label">Pedidos Realizados</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <h3>Total de Clientes</h3>
                <div class="stat-number"><?= $qtdClientes ?></div>
                <div class="stat-label">Clientes Cadastrados</div>
            </div>
        </section>
    </div>
</body>
</html>