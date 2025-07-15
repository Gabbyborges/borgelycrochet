<?php
session_start();
if (!isset($_SESSION['cliente_id'])) {
    header("Location: login_cliente.php");
    exit;
}

include("../db.php");

$cliente_id = $_SESSION['cliente_id'];

// Buscar dados do cliente
$sql_cliente = $conn->prepare("SELECT nome, email FROM clientes WHERE id = ?");
$sql_cliente->bind_param("i", $cliente_id);
$sql_cliente->execute();
$result_cliente = $sql_cliente->get_result();
$cliente = $result_cliente->fetch_assoc();

// Atualizar perfil (email e senha)
$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_perfil'])) {
    $novo_email = trim($_POST['email']);
    $nova_senha = $_POST['senha'];
    $confirm_senha = $_POST['confirm_senha'];

    if ($nova_senha !== $confirm_senha) {
        $mensagem = "As senhas não conferem!";
    } elseif (!filter_var($novo_email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Email inválido!";
    } else {
        if (!empty($nova_senha)) {
            // Atualiza email e senha (senha com hash)
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE clientes SET email = ?, senha = ? WHERE id = ?");
            $stmt->bind_param("ssi", $novo_email, $senha_hash, $cliente_id);
        } else {
            // Atualiza só email
            $stmt = $conn->prepare("UPDATE clientes SET email = ? WHERE id = ?");
            $stmt->bind_param("si", $novo_email, $cliente_id);
        }
        if ($stmt->execute()) {
            $mensagem = "Perfil atualizado com sucesso!";
            // Atualiza dados em variável para refletir as mudanças no formulário
            $cliente['email'] = $novo_email;
        } else {
            $mensagem = "Erro ao atualizar perfil. Tente novamente.";
        }
    }
}

// Buscar pedidos do cliente
$sql_pedidos = $conn->prepare("SELECT id, data_pedido, total, status FROM pedidos WHERE usuario_id = ? ORDER BY data_pedido DESC");
$sql_pedidos->bind_param("i", $cliente_id);
$sql_pedidos->execute();
$result_pedidos = $sql_pedidos->get_result();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Painel do Cliente - Borgely Crochet</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
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
      min-height: 100vh;
      color: var(--gray-800);
    }

    /* Header */
    .header {
      background: var(--white);
      box-shadow: var(--shadow-sm);
      border-bottom: 1px solid var(--gray-100);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .header-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 16px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 20px;
      font-weight: 700;
      color: var(--primary-color);
      text-decoration: none;
    }

    .user-welcome {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 14px;
    }

    .user-info h2 {
      font-size: 16px;
      font-weight: 600;
      color: var(--gray-800);
      margin: 0;
    }

    .user-info p {
      font-size: 12px;
      color: var(--gray-500);
      margin: 0;
    }

    /* Main Container */
    .main-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 32px 24px;
    }

    /* Welcome Section */
    .welcome-section {
      background: var(--white);
      border-radius: 16px;
      padding: 32px;
      margin-bottom: 32px;
      box-shadow: var(--shadow-sm);
      text-align: center;
    }

    .welcome-title {
      font-size: 28px;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 8px;
    }

    .welcome-subtitle {
      font-size: 16px;
      color: var(--gray-500);
      margin-bottom: 24px;
    }

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 24px;
      margin-bottom: 32px;
    }

    .stat-card {
      background: var(--white);
      border-radius: 16px;
      padding: 24px;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
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
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .stat-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
    }

    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 20px;
    }

    .stat-title {
      font-size: 14px;
      font-weight: 500;
      color: var(--gray-600);
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }

    .stat-value {
      font-size: 24px;
      font-weight: 700;
      color: var(--gray-800);
    }

    /* Action Grid */
    .action-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 32px;
    }

    .action-card {
      background: var(--white);
      border-radius: 12px;
      padding: 20px;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      cursor: pointer;
      border: 1px solid var(--gray-100);
      text-decoration: none;
      color: inherit;
    }

    .action-card:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
      border-color: var(--primary-color);
    }

    .action-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .action-icon {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 18px;
    }

    .action-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--gray-800);
    }

    .action-description {
      font-size: 14px;
      color: var(--gray-600);
      line-height: 1.5;
    }

    /* Orders Table */
    .orders-section {
      background: var(--white);
      border-radius: 16px;
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      margin-bottom: 32px;
    }

    .orders-header {
      padding: 24px;
      border-bottom: 1px solid var(--gray-100);
    }

    .orders-title {
      font-size: 20px;
      font-weight: 600;
      color: var(--gray-800);
      margin-bottom: 4px;
    }

    .orders-subtitle {
      font-size: 14px;
      color: var(--gray-500);
    }

    .orders-table {
      width: 100%;
      border-collapse: collapse;
    }

    .orders-table th,
    .orders-table td {
      padding: 16px 24px;
      text-align: left;
      border-bottom: 1px solid var(--gray-100);
    }

    .orders-table th {
      background: var(--gray-50);
      font-weight: 600;
      color: var(--gray-700);
      font-size: 14px;
    }

    .orders-table tbody tr:hover {
      background: var(--gray-50);
    }

    .status-badge {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }

    .status-pending {
      background: rgba(251, 191, 36, 0.1);
      color: #d97706;
    }

    .status-completed {
      background: rgba(34, 197, 94, 0.1);
      color: #059669;
    }

    .status-cancelled {
      background: rgba(239, 68, 68, 0.1);
      color: #dc2626;
    }

    .empty-state {
      text-align: center;
      padding: 48px 24px;
      color: var(--gray-500);
    }

    .empty-state-icon {
      font-size: 48px;
      margin-bottom: 16px;
      opacity: 0.5;
    }

    /* Profile Form */
    .profile-section {
      background: var(--white);
      border-radius: 16px;
      padding: 32px;
      box-shadow: var(--shadow-sm);
      max-width: 600px;
      margin: 0 auto;
    }

    .profile-header {
      text-align: center;
      margin-bottom: 32px;
    }

    .profile-title {
      font-size: 20px;
      font-weight: 600;
      color: var(--gray-800);
      margin-bottom: 8px;
    }

    .profile-subtitle {
      font-size: 14px;
      color: var(--gray-500);
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-weight: 500;
      color: var(--gray-700);
      margin-bottom: 8px;
      font-size: 14px;
    }

    .form-input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid var(--gray-200);
      border-radius: 8px;
      font-size: 16px;
      color: var(--gray-800);
      background: var(--white);
      transition: var(--transition);
      outline: none;
    }

    .form-input:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
    }

    .form-button {
      width: 100%;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 14px 24px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      transition: var(--transition);
      margin-top: 8px;
    }

    .form-button:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    /* Messages */
    .message {
      padding: 16px 20px;
      border-radius: 8px;
      margin-bottom: 24px;
      font-weight: 500;
      text-align: center;
    }

    .message.success {
      background: rgba(34, 197, 94, 0.1);
      color: #059669;
      border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .message.error {
      background: rgba(239, 68, 68, 0.1);
      color: #dc2626;
      border: 1px solid rgba(239, 68, 68, 0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .header-content {
        padding: 16px;
      }

      .main-container {
        padding: 24px 16px;
      }

      .welcome-section {
        padding: 24px 20px;
      }

      .welcome-title {
        font-size: 24px;
      }

      .stats-grid {
        grid-template-columns: 1fr;
      }

      .action-grid {
        grid-template-columns: 1fr;
      }

      .profile-section {
        padding: 24px 20px;
      }

      .orders-table th,
      .orders-table td {
        padding: 12px 16px;
        font-size: 14px;
      }

      .user-welcome {
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
      }

      .user-info h2 {
        font-size: 14px;
      }
    }

    /* Animations */
    .stat-card,
    .action-card,
    .orders-section,
    .profile-section {
      animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="header-content">
      <a href="#" class="logo">Borgely Crochet</a>
      <div class="user-welcome">
        <div class="user-avatar">
          <?= strtoupper(substr($cliente['nome'], 0, 1)) ?>
        </div>
        <div class="user-info">
          <h2>Olá, <?= htmlspecialchars($cliente['nome']) ?></h2>
          <p>Bem-vinda ao seu painel</p>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="main-container">
    <!-- Welcome Section -->
    <section class="welcome-section">
      <h1 class="welcome-title">Bem-vinda, <?= htmlspecialchars($cliente['nome']) ?>!</h1>
      <p class="welcome-subtitle">Painel do Cliente</p>
    </section>

    <!-- Stats Grid -->
    <section class="stats-grid">
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon">👤</div>
          <div class="stat-title">Perfil</div>
        </div>
        <div class="stat-value"><?= htmlspecialchars($cliente['nome']) ?></div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon">📧</div>
          <div class="stat-title">Email</div>
        </div>
        <div class="stat-value" style="font-size: 16px;"><?= htmlspecialchars($cliente['email']) ?></div>
      </div>
      
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon">🛍️</div>
          <div class="stat-title">Total de Pedidos</div>
        </div>
        <div class="stat-value"><?= $result_pedidos->num_rows ?></div>
      </div>
    </section>

    <!-- Action Grid -->
    <section class="action-grid">
      <div class="action-card">
        <div class="action-header">
          <div class="action-icon">📋</div>
          <div class="action-title">Meus Pedidos</div>
        </div>
        <p class="action-description">Visualize o histórico completo dos seus pedidos</p>
      </div>
    </section>

    <!-- Orders Section -->
    <section class="orders-section">
      <div class="orders-header">
        <h3 class="orders-title">Histórico de Pedidos</h3>
        <p class="orders-subtitle">Acompanhe todos os seus pedidos realizados</p>
      </div>
      
      <?php if ($result_pedidos->num_rows === 0): ?>
        <div class="empty-state">
          <div class="empty-state-icon">🛍️</div>
          <p>Nenhum pedido encontrado</p>
        </div>
      <?php else: ?>
        <table class="orders-table">
          <thead>
            <tr>
              <th>ID do Pedido</th>
              <th>Data</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($pedido = $result_pedidos->fetch_assoc()): ?>
              <tr>
                <td>#<?= str_pad($pedido['id'], 4, '0', STR_PAD_LEFT) ?></td>
                <td><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></td>
                <td>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></td>
                <td>
                  <?php
                  $status = strtolower($pedido['status']);
                  $statusClass = 'status-pending';
                  if ($status === 'concluído' || $status === 'entregue') {
                    $statusClass = 'status-completed';
                  } elseif ($status === 'cancelado') {
                    $statusClass = 'status-cancelled';
                  }
                  ?>
                  <span class="status-badge <?= $statusClass ?>">
                    <?= htmlspecialchars($pedido['status']) ?>
                  </span>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>

    <!-- Profile Section -->
    <section class="profile-section">
      <div class="profile-header">
        <h3 class="profile-title">Editar Perfil</h3>
        <p class="profile-subtitle">Atualize suas informações pessoais</p>
      </div>

      <?php if ($mensagem): ?>
        <div class="message <?= strpos($mensagem, 'sucesso') !== false ? 'success' : 'error' ?>">
          <?= htmlspecialchars($mensagem) ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="editar_perfil" value="1" />
        
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <input type="email" id="email" name="email" class="form-input" value="<?= htmlspecialchars($cliente['email']) ?>" required />
        </div>

        <div class="form-group">
          <label for="senha" class="form-label">Nova senha</label>
          <input type="password" id="senha" name="senha" class="form-input" placeholder="Deixe vazio para manter a atual" />
        </div>

        <div class="form-group">
          <label for="confirm_senha" class="form-label">Confirmar nova senha</label>
          <input type="password" id="confirm_senha" name="confirm_senha" class="form-input" placeholder="Confirme a nova senha" />
        </div>

        <button type="submit" class="form-button">Salvar Alterações</button>
      </form>
    </section>
  </main>
</body>
</html>