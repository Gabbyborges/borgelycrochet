<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

// include do banco - ajustado
include("../db.php");

// consulta com verificação
$result = $conn->query("SELECT * FROM clientes ORDER BY data_cadastro DESC");
if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes Cadastrados</title>
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
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--soft-pink) 100%);
            min-height: 100vh;
            color: var(--gray-800);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            background: var(--white);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: left;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            outline: none;
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
            background: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background: var(--primary-color);
            color: var(--white);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray-500);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .table-container {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .table-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--white);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.5rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .search-box input {
            background: none;
            border: none;
            color: var(--white);
            font-size: 0.875rem;
            width: 200px;
            outline: none;
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--gray-50);
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-200);
        }

        td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-800);
            font-size: 0.875rem;
        }

        tr:hover {
            background: var(--soft-pink);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-details h4 {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
        }

        .user-details p {
            color: var(--gray-500);
            font-size: 0.75rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .date-info {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .action-btn.view {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .action-btn.edit {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .action-btn.delete {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            margin-top: 2rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--accent-color);
            transform: translateX(-4px);
        }

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

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .title {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                gap: 1rem;
            }

            .search-box input {
                width: 100%;
            }

            .table-wrapper {
                font-size: 0.75rem;
            }

            th, td {
                padding: 0.75rem;
            }

            .user-info {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
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
            <h1 class="title">
                <i class="fas fa-users"></i>
                Clientes Cadastrados
            </h1>
            <div style="display: flex; gap: 1rem;">
                <a href="cadastro_cliente.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Novo Cliente
                </a>
                <a href="logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?= $result->num_rows ?></div>
                <div class="stat-label">Total de Clientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-number">
                    <?php
                    $today = date('Y-m-d');
                    $today_result = $conn->query("SELECT COUNT(*) as count FROM clientes WHERE DATE(data_cadastro) = '$today'");
                    $today_count = $today_result->fetch_assoc()['count'];
                    echo $today_count;
                    ?>
                </div>
                <div class="stat-label">Novos Hoje</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-number">
                    <?php
                    $week_ago = date('Y-m-d', strtotime('-7 days'));
                    $week_result = $conn->query("SELECT COUNT(*) as count FROM clientes WHERE data_cadastro >= '$week_ago'");
                    $week_count = $week_result->fetch_assoc()['count'];
                    echo $week_count;
                    ?>
                </div>
                <div class="stat-label">Últimos 7 dias</div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-table"></i>
                    Lista de Clientes
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Buscar cliente...">
                </div>
            </div>

            <div class="table-wrapper">
                <?php if ($result->num_rows > 0): ?>
                    <table id="clientsTable">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Email</th>
                                <th>Data de Cadastro</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Reset result pointer
                            $result->data_seek(0);
                            while ($row = $result->fetch_assoc()): 
                                $initials = strtoupper(substr($row['nome'], 0, 1));
                                $second_name = explode(' ', $row['nome']);
                                if (count($second_name) > 1) {
                                    $initials .= strtoupper(substr($second_name[1], 0, 1));
                                }
                            ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar"><?= $initials ?></div>
                                            <div class="user-details">
                                                <h4><?= htmlspecialchars($row['nome']) ?></h4>
                                                <p>ID: <?= $row['id'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td>
                                        <div class="date-info">
                                            <?= date('d/m/Y', strtotime($row['data_cadastro'])) ?><br>
                                            <small><?= date('H:i', strtotime($row['data_cadastro'])) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i>
                                            Ativo
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="ver_cliente.php?id=<?= $row['id'] ?>" class="action-btn view" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar_cliente.php?id=<?= $row['id'] ?>" class="action-btn edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="excluir_cliente.php?id=<?= $row['id'] ?>" class="action-btn delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>Nenhum cliente cadastrado</h3>
                        <p>Comece adicionando seu primeiro cliente ao sistema.</p>
                        <a href="cadastro_cliente.php" class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-plus"></i>
                            Cadastrar Cliente
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <a href="painel_admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Painel
        </a>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#clientsTable tbody tr');
            
            tableRows.forEach(row => {
                const name = row.querySelector('.user-details h4').textContent.toLowerCase();
                const email = row.cells[1].textContent.toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Add loading animation
        window.addEventListener('load', function() {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html>