<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

include("../db.php"); // Caminho ajustado

// Buscar pedidos com nome do cliente
$sql = "SELECT p.id, p.data_pedido, p.total, p.status, c.nome AS cliente_nome, c.email AS cliente_email
        FROM pedidos p
        JOIN clientes c ON p.usuario_id = c.id
        ORDER BY p.data_pedido DESC";
$result = $conn->query($sql);

// Contar totais para estatísticas
$stats_sql = "SELECT 
    COUNT(*) as total_pedidos,
    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pedidos_pendentes,
    SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as pedidos_concluidos,
    SUM(total) as faturamento_total
    FROM pedidos";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - Painel Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--soft-pink) 0%, var(--gray-50) 100%);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        .header {
            background: var(--white);
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header h1 i {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.25rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--white);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
        }

        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }

        .btn-secondary:hover {
            background: var(--gray-200);
            transform: translateY(-1px);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: 1px solid var(--gray-100);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-card-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white);
        }

        .stat-card-icon.orders {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        }

        .stat-card-icon.pending {
            background: linear-gradient(135deg, #f59e0b, #f97316);
        }

        .stat-card-icon.completed {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .stat-card-icon.revenue {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .stat-card-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .stat-card-label {
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        /* Table Container */
        .table-container {
            background: var(--white);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-100);
        }

        .table-header {
            padding: 2rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-box {
            position: relative;
            max-width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            font-size: 0.875rem;
            transition: var(--transition);
            background: var(--gray-50);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
            background: var(--white);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-100);
        }

        .table th {
            background: var(--gray-50);
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: var(--soft-pink);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Badge */
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-badge.pendente {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.processando {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-badge.concluido {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.cancelado {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 0.5rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }

        .btn-edit {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .btn-edit:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: scale(1.1);
        }

        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: var(--white);
            transform: scale(1.1);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--gray-300);
        }

        .empty-state h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header {
                padding: 1.5rem;
                text-align: center;
            }

            .header h1 {
                font-size: 2rem;
            }

            .header-actions {
                width: 100%;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                max-width: none;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 600px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-shopping-bag"></i>
                Gerenciar Pedidos
            </h1>
            <div class="header-actions">
                <a href="novo_pedido.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Novo Pedido
                </a>
                <a href="../cliente/logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Total de Pedidos</div>
                    <div class="stat-card-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['total_pedidos'] ?? 0 ?></div>
                <div class="stat-card-label">Pedidos realizados</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Pedidos Pendentes</div>
                    <div class="stat-card-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['pedidos_pendentes'] ?? 0 ?></div>
                <div class="stat-card-label">Aguardando processamento</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Pedidos Concluídos</div>
                    <div class="stat-card-icon completed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['pedidos_concluidos'] ?? 0 ?></div>
                <div class="stat-card-label">Finalizados com sucesso</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Faturamento Total</div>
                    <div class="stat-card-icon revenue">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="stat-card-value">R$ <?= number_format($stats['faturamento_total'] ?? 0, 2, ',', '.') ?></div>
                <div class="stat-card-label">Receita acumulada</div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">
                    <i class="fas fa-list"></i>
                    Lista de Pedidos
                </h2>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Pesquisar pedidos..." id="searchInput">
                </div>
            </div>

            <?php if ($result->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Data do Pedido</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span style="font-weight: 600; color: var(--primary-color);">
                                #<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--gray-900);">
                                <?= htmlspecialchars($row['cliente_nome']) ?>
                            </div>
                        </td>
                        <td>
                            <span style="color: var(--gray-600);">
                                <?= htmlspecialchars($row['cliente_email']) ?>
                            </span>
                        </td>
                        <td>
                            <span style="color: var(--gray-700);">
                                <?= date('d/m/Y', strtotime($row['data_pedido'])) ?>
                            </span>
                            <div style="font-size: 0.75rem; color: var(--gray-500);">
                                <?= date('H:i', strtotime($row['data_pedido'])) ?>
                            </div>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: var(--gray-900);">
                                R$ <?= number_format($row['total'], 2, ',', '.') ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?= $row['status'] ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="editar_pedido.php?id=<?= $row['id'] ?>" class="btn-action btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="visualizar_pedido.php?id=<?= $row['id'] ?>" class="btn-action btn-edit" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h3>Nenhum pedido encontrado</h3>
                <p>Quando houver pedidos, eles aparecerão aqui.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Funcionalidade de pesquisa
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>