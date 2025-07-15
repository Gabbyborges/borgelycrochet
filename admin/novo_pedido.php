<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

include("../db.php");
$mensagem = "";
$tipo_mensagem = "";

// Buscar clientes existentes
$clientes = $conn->query("SELECT id, nome FROM clientes ORDER BY nome");

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dados do cliente
    $cliente_existente = isset($_POST['cliente_existente']) ? intval($_POST['cliente_existente']) : 0;
    $novo_cliente_nome = trim($_POST['novo_cliente_nome'] ?? '');
    $novo_cliente_email = trim($_POST['novo_cliente_email'] ?? '');

    // Se cliente existente selecionado
    if ($cliente_existente) {
        $usuario_id = $cliente_existente;
    } else {
        // Criar novo cliente se nome e email preenchidos
        if ($novo_cliente_nome !== '' && $novo_cliente_email !== '') {
            $novo_cliente_nome_esc = $conn->real_escape_string($novo_cliente_nome);
            $novo_cliente_email_esc = $conn->real_escape_string($novo_cliente_email);

            // Verificar se email já existe
            $res = $conn->query("SELECT id FROM clientes WHERE email='$novo_cliente_email_esc' LIMIT 1");
            if ($res->num_rows > 0) {
                $mensagem = "Email já cadastrado, escolha um cliente existente.";
                $tipo_mensagem = "error";
            } else {
                $conn->query("INSERT INTO clientes (nome, email, senha) VALUES ('$novo_cliente_nome_esc', '$novo_cliente_email_esc', '')");
                $usuario_id = $conn->insert_id;
                $mensagem = "Cliente criado com sucesso!";
                $tipo_mensagem = "success";
            }
        } else {
            $mensagem = "Preencha nome e email para novo cliente ou selecione um existente.";
            $tipo_mensagem = "warning";
        }
    }

    // Se usuário definido, registrar pedido
    if (!empty($usuario_id) && empty($mensagem)) {
        $descricao = $conn->real_escape_string($_POST['descricao'] ?? '');
        $total = floatval(str_replace(',', '.', $_POST['total'] ?? '0'));
        $status = $conn->real_escape_string($_POST['status'] ?? 'pendente');

        $sql_pedido = "INSERT INTO pedidos (usuario_id, data_pedido, total, status) VALUES ($usuario_id, NOW(), $total, '$status')";

        if ($conn->query($sql_pedido)) {
            $mensagem = "Pedido registrado com sucesso!";
            $tipo_mensagem = "success";
        } else {
            $mensagem = "Erro ao registrar pedido: " . $conn->error;
            $tipo_mensagem = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Pedido - Painel Admin</title>
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
            padding: 2rem 1rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: var(--white);
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            text-align: center;
            position: relative;
        }

        .back-button {
            position: absolute;
            left: 2rem;
            top: 50%;
            transform: translateY(-50%);
            background: var(--gray-100);
            color: var(--gray-700);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-button:hover {
            background: var(--gray-200);
            transform: translateY(-50%) translateX(-2px);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .header h1 i {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.25rem;
        }

        .header p {
            color: var(--gray-600);
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease-out;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fed7aa;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form Container */
        .form-container {
            background: var(--white);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-100);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--white);
            padding: 2rem;
            text-align: center;
        }

        .form-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            opacity: 0.9;
        }

        .form-content {
            padding: 2rem;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 2rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        /* Fieldset */
        .fieldset {
            border: 2px solid var(--gray-200);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background: var(--gray-50);
            transition: var(--transition);
        }

        .fieldset:hover {
            border-color: var(--primary-color);
            background: var(--white);
        }

        .fieldset legend {
            font-weight: 600;
            color: var(--primary-color);
            padding: 0 1rem;
            background: var(--white);
            border-radius: 8px;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .fieldset .form-group {
            margin-bottom: 1.5rem;
        }

        .fieldset .form-group:last-child {
            margin-bottom: 0;
        }

        /* Input Icons */
        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1rem;
        }

        .input-group input,
        .input-group select {
            padding-left: 3rem;
        }

        /* Submit Button */
        .submit-button {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--white);
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 16px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: var(--shadow-md);
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        /* Status Badge Preview */
        .status-preview {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
        }

        .status-preview.pendente {
            background: #fef3c7;
            color: #92400e;
        }

        .status-preview.pago {
            background: #d1fae5;
            color: #065f46;
        }

        .status-preview.enviado {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-preview.entregue {
            background: #dcfce7;
            color: #166534;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header {
                padding: 1.5rem;
            }

            .back-button {
                position: static;
                transform: none;
                margin-bottom: 1rem;
                width: fit-content;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-content {
                padding: 1.5rem;
            }

            .fieldset {
                padding: 1rem;
            }
        }

        /* Form Animation */
        .form-container {
            animation: fadeInUp 0.6s ease-out;
        }

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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="listar_pedidos.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <h1>
                <i class="fas fa-plus-circle"></i>
                Novo Pedido
            </h1>
            <p>Adicione um novo pedido ao sistema</p>
        </div>

        <!-- Alert Messages -->
        <?php if ($mensagem): ?>
            <div class="alert alert-<?= $tipo_mensagem ?>">
                <?php if ($tipo_mensagem === 'success'): ?>
                    <i class="fas fa-check-circle"></i>
                <?php elseif ($tipo_mensagem === 'error'): ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php elseif ($tipo_mensagem === 'warning'): ?>
                    <i class="fas fa-exclamation-triangle"></i>
                <?php endif; ?>
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <!-- Form Container -->
        <div class="form-container">
            <div class="form-header">
                <h2>Informações do Pedido</h2>
                <p>Preencha os dados abaixo para criar um novo pedido</p>
            </div>

            <div class="form-content">
                <form method="POST" id="orderForm">
                    <!-- Cliente Selection -->
                    <div class="form-group">
                        <label for="cliente_existente">
                            <i class="fas fa-user"></i>
                            Selecionar Cliente
                        </label>
                        <div class="input-group">
                            <i class="fas fa-users"></i>
                            <select name="cliente_existente" id="cliente_existente">
                                <option value="0">-- Criar Novo Cliente --</option>
                                <?php 
                                $clientes->data_seek(0); // Reset pointer
                                while ($c = $clientes->fetch_assoc()): 
                                ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <!-- New Client Fieldset -->
                    <fieldset class="fieldset" id="newClientFieldset">
                        <legend>Novo Cliente</legend>
                        <div class="form-group">
                            <label for="novo_cliente_nome">Nome do Cliente</label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" name="novo_cliente_nome" id="novo_cliente_nome" placeholder="Digite o nome completo">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="novo_cliente_email">Email do Cliente</label>
                            <div class="input-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="novo_cliente_email" id="novo_cliente_email" placeholder="Digite o email">
                            </div>
                        </div>
                    </fieldset>

                    <!-- Order Details -->
                    <div class="form-group">
                        <label for="descricao">Descrição do Pedido</label>
                        <textarea name="descricao" id="descricao" required placeholder="Descreva os detalhes do pedido..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="total">Valor Total</label>
                        <div class="input-group">
                            <i class="fas fa-dollar-sign"></i>
                            <input type="text" name="total" id="total" required placeholder="0,00" pattern="[0-9]+([,][0-9]{2})?">
                        </div>
                        <small style="color: var(--gray-500); font-size: 0.875rem;">
                            Formato: 150,00 (use vírgula para separar centavos)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="status">Status do Pedido</label>
                        <div class="input-group">
                            <i class="fas fa-flag"></i>
                            <select name="status" id="status">
                                <option value="pendente">Pendente</option>
                                <option value="pago">Pago</option>
                                <option value="enviado">Enviado</option>
                                <option value="entregue">Entregue</option>
                            </select>
                        </div>
                        <div class="status-preview pendente" id="statusPreview">
                            <i class="fas fa-clock"></i>
                            Pendente
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="submit-button">
                        <i class="fas fa-save"></i>
                        Registrar Pedido
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle new client fieldset
        document.getElementById('cliente_existente').addEventListener('change', function() {
            const fieldset = document.getElementById('newClientFieldset');
            const newClientInputs = fieldset.querySelectorAll('input');
            
            if (this.value === '0') {
                fieldset.style.display = 'block';
                newClientInputs.forEach(input => input.required = true);
            } else {
                fieldset.style.display = 'none';
                newClientInputs.forEach(input => {
                    input.required = false;
                    input.value = '';
                });
            }
        });

        // Status preview
        document.getElementById('status').addEventListener('change', function() {
            const preview = document.getElementById('statusPreview');
            const value = this.value;
            
            preview.className = `status-preview ${value}`;
            
            const statusIcons = {
                'pendente': 'fas fa-clock',
                'pago': 'fas fa-check-circle',
                'enviado': 'fas fa-truck',
                'entregue': 'fas fa-check-double'
            };
            
            const statusLabels = {
                'pendente': 'Pendente',
                'pago': 'Pago',
                'enviado': 'Enviado',
                'entregue': 'Entregue'
            };
            
            preview.innerHTML = `<i class="${statusIcons[value]}"></i> ${statusLabels[value]}`;
        });

        // Format currency input
        document.getElementById('total').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = value.replace(/(\d+)(\d{2})$/, '$1,$2');
            }
            e.target.value = value;
        });

        // Form validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const clienteExistente = document.getElementById('cliente_existente').value;
            const novoClienteNome = document.getElementById('novo_cliente_nome').value.trim();
            const novoClienteEmail = document.getElementById('novo_cliente_email').value.trim();
            
            if (clienteExistente === '0' && (!novoClienteNome || !novoClienteEmail)) {
                e.preventDefault();
                alert('Por favor, selecione um cliente existente ou preencha os dados do novo cliente.');
                return false;
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Hide new client fieldset initially if existing client is selected
            const clienteSelect = document.getElementById('cliente_existente');
            if (clienteSelect.value !== '0') {
                document.getElementById('newClientFieldset').style.display = 'none';
            }
        });
    </script>
</body>
</html>