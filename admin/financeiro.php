<?php 
// Conexão com banco
include("../db.php");

// Buscar registros financeiros
$sql = "SELECT * FROM financeiro ORDER BY data DESC";
$result = $conn->query($sql);

// Somar total
$sql_total = "SELECT 
    SUM(CASE WHEN tipo='entrada' THEN valor ELSE 0 END) AS total_entradas,
    SUM(CASE WHEN tipo='saida' THEN valor ELSE 0 END) AS total_saidas
FROM financeiro";
$totais = $conn->query($sql_total)->fetch_assoc();

$saldo = $totais['total_entradas'] - $totais['total_saidas'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro</title>
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--soft-pink) 0%, var(--gray-50) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: var(--white);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 2rem;
            text-align: center;
            border-top: 4px solid var(--primary-color);
        }

        .header h1 {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--gray-600);
            font-size: 1.1rem;
        }

        .actions {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .btn {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow-md);
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            text-align: center;
            transition: var(--transition);
            border-top: 4px solid var(--primary-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.entrada {
            background: #dcfce7;
            color: #16a34a;
        }

        .stat-icon.saida {
            background: #fef2f2;
            color: #dc2626;
        }

        .stat-icon.saldo {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .stat-title {
            color: var(--gray-600);
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .table-container {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .table-header {
            background: var(--primary-color);
            color: var(--white);
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--gray-200);
        }

        .table th {
            background: var(--gray-50);
            font-weight: 600;
            color: var(--gray-700);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table tr:hover {
            background: var(--gray-50);
        }

        .tipo-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .tipo-entrada {
            background: #dcfce7;
            color: #16a34a;
        }

        .tipo-saida {
            background: #fef2f2;
            color: #dc2626;
        }

        .valor {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            text-decoration: none;
        }

        .edit-btn {
            background: #e0f2fe;
            color: #0277bd;
        }

        .edit-btn:hover {
            background: #b3e5fc;
        }

        .delete-btn {
            background: #fef2f2;
            color: #dc2626;
        }

        .delete-btn:hover {
            background: #fecaca;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray-500);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
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
            <h1>💰 Controle Financeiro</h1>
            <p>Painel de Administração</p>
        </div>

        <!-- Actions -->
        <div class="actions">
            <a href="adicionar_financa.php" class="btn">
                <span>➕</span> Adicionar Registro
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon entrada">📈</div>
                <div class="stat-title">Total de Entradas</div>
                <div class="stat-value">R$ <?= number_format($totais['total_entradas'] ?? 0, 2, ',', '.') ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-icon saida">📉</div>
                <div class="stat-title">Total de Saídas</div>
                <div class="stat-value">R$ <?= number_format($totais['total_saidas'] ?? 0, 2, ',', '.') ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-icon saldo">💰</div>
                <div class="stat-title">Saldo Atual</div>
                <div class="stat-value">R$ <?= number_format($saldo, 2, ',', '.') ?></div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                📊 Histórico de Transações
            </div>
            
            <?php if ($result && $result->num_rows > 0) { ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td>
                                <span class="tipo-badge <?= $row['tipo'] == 'entrada' ? 'tipo-entrada' : 'tipo-saida' ?>">
                                    <?= $row['tipo'] == 'entrada' ? '🟢 Entrada' : '🔴 Saída' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['descricao']) ?></td>
                            <td class="valor">R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
                            <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                            <td>
                                <div class="actions-cell">
                                    <a href="editar_financa.php?id=<?= $row['id'] ?>" class="action-btn edit-btn" title="Editar">
                                        ✏️
                                    </a>
                                    <a href="excluir_financa.php?id=<?= $row['id'] ?>" 
                                       class="action-btn delete-btn" 
                                       onclick="return confirm('Deseja realmente excluir este registro?')" 
                                       title="Excluir">
                                        🗑️
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="empty-state">
                    <div class="empty-state-icon">💸</div>
                    <h3>Nenhum registro encontrado</h3>
                    <p>Comece adicionando seu primeiro registro financeiro.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>