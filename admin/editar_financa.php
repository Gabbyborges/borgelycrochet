<?php
include('../db.php');  

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    echo "ID não informado.";
    exit;
}

$id = $_GET['id'];

// Se o formulário foi enviado (edição)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST['tipo'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $data = $_POST['data'];

    $sql = "UPDATE financeiro SET tipo='$tipo', descricao='$descricao', valor='$valor', data='$data' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $success = true;
    } else {
        $error = "Erro: " . $conn->error;
    }
}

// Carrega os dados do registro para exibir no formulário
$sql = "SELECT * FROM financeiro WHERE id=$id";
$result = $conn->query($sql);
$financa = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro Financeiro</title>
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
            padding: 2rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
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
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--gray-600);
            font-size: 1rem;
        }

        .form-container {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
            color: var(--gray-800);
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
        }

        .form-select {
            cursor: pointer;
        }

        .form-select option {
            padding: 0.5rem;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 2rem;
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
            min-width: 140px;
            justify-content: center;
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-300);
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .form-hint {
            font-size: 0.8rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        .input-icon {
            position: relative;
        }

        .input-icon::before {
            content: attr(data-icon);
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.1rem;
            z-index: 1;
        }

        .input-icon .form-input {
            padding-left: 3rem;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .form-container {
                padding: 1.5rem;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>✏️ Editar Registro Financeiro</h1>
            <p>Painel de Administração</p>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <?php if (isset($success) && $success) { ?>
                <div class="alert alert-success">
                    <span>✅</span>
                    <span>Registro atualizado com sucesso!</span>
                </div>
                <div class="btn-group">
                    <a href="financeiro.php" class="btn btn-primary">
                        <span>📊</span> Voltar ao Painel
                    </a>
                </div>
            <?php } elseif (isset($error)) { ?>
                <div class="alert alert-error">
                    <span>❌</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php } ?>

            <?php if (!isset($success) || !$success) { ?>
                <form method="post" action="">
                    <div class="form-group">
                        <label class="form-label">Tipo de Transação</label>
                        <select name="tipo" class="form-select" required>
                            <option value="">Selecione o tipo</option>
                            <option value="entrada" <?= $financa['tipo'] == 'entrada' ? 'selected' : '' ?>>
                                🟢 Entrada
                            </option>
                            <option value="saida" <?= $financa['tipo'] == 'saida' ? 'selected' : '' ?>>
                                🔴 Saída
                            </option>
                        </select>
                        <div class="form-hint">Escolha se é uma entrada ou saída de dinheiro</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descrição</label>
                        <div class="input-icon" data-icon="📝">
                            <input type="text" 
                                   name="descricao" 
                                   class="form-input" 
                                   value="<?= htmlspecialchars($financa['descricao']) ?>" 
                                   required
                                   placeholder="Ex: Venda de produto, Pagamento de fornecedor...">
                        </div>
                        <div class="form-hint">Descreva brevemente esta transação</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Valor (R$)</label>
                        <div class="input-icon" data-icon="💰">
                            <input type="number" 
                                   step="0.01" 
                                   name="valor" 
                                   class="form-input" 
                                   value="<?= $financa['valor'] ?>" 
                                   required
                                   placeholder="0,00">
                        </div>
                        <div class="form-hint">Informe o valor em reais (use ponto para decimais)</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Data</label>
                        <div class="input-icon" data-icon="📅">
                            <input type="date" 
                                   name="data" 
                                   class="form-input" 
                                   value="<?= $financa['data'] ?>" 
                                   required>
                        </div>
                        <div class="form-hint">Data em que a transação foi realizada</div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <span>💾</span> Salvar Alterações
                        </button>
                        <a href="financeiro.php" class="btn btn-secondary">
                            <span>↩️</span> Cancelar
                        </a>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>