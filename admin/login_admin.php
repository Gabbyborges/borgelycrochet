<?php
// Inclua o arquivo de conexão do diretório pai
include_once '../db.php';

// Inicializar variáveis para evitar erros
$erro = '';
$sucesso = '';
$debug = '';

// Processa o login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    $debug .= "Tentativa de login - Usuário: '$usuario'<br>";

    if (empty($usuario) || empty($senha)) {
        $erro = 'Todos os campos são obrigatórios.';
    } else {
        try {
            $debug .= "Preparando consulta SQL...<br>";

            // Query corrigida usando a tabela 'admin' com colunas 'usuario' e 'senha'
            $stmt = $conn->prepare("SELECT id, usuario, senha FROM admin WHERE usuario = ?");

            if (!$stmt) {
                throw new Exception("Erro ao preparar statement: " . $conn->error);
            }

            $debug .= "Statement preparado com sucesso<br>";
            $stmt->bind_param("s", $usuario);
            $debug .= "Parâmetros vinculados<br>";
            $stmt->execute();
            $debug .= "Query executada<br>";

            $result = $stmt->get_result();
            $debug .= "Resultado obtido<br>";
            $user = $result->fetch_assoc();

            $debug .= "Usuário encontrado: " . ($user ? 'SIM' : 'NÃO') . "<br>";

            if ($user) {
                $debug .= "Verificando senha...<br>";
                
                // Verificar se a senha está hasheada ou em texto simples
                $senha_banco = $user['senha'];
                $debug .= "Senha no banco (primeiros 10 chars): " . substr($senha_banco, 0, 10) . "...<br>";
                
                $senha_valida = false;
                
                // Tentar verificar com password_verify primeiro (para senhas hasheadas)
                if (password_verify($senha, $senha_banco)) {
                    $senha_valida = true;
                    $debug .= "Senha verificada com password_verify<br>";
                } 
                // Se não funcionar, comparar diretamente (para senhas em texto simples)
                elseif ($senha === $senha_banco) {
                    $senha_valida = true;
                    $debug .= "Senha verificada com comparação direta<br>";
                }
                
                if ($senha_valida) {
                    $debug .= "✅ Login bem-sucedido!<br>";
                    
                    // Iniciar sessão
                    session_start();
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_usuario'] = $user['usuario'];
                    $_SESSION['admin_logado'] = true;
                    
                    $debug .= "Sessão iniciada. Redirecionando...<br>";
                    
                    // Redirecionar para o painel
                    header('Location: painel_admin.php');
                    exit;
                } else {
                    $erro = 'Credenciais inválidas.';
                    $debug .= "❌ Senha incorreta<br>";
                }
            } else {
                $erro = 'Credenciais inválidas.';
                $debug .= "❌ Usuário não encontrado<br>";
            }

            $stmt->close();
        } catch (Exception $e) {
            $erro = 'Erro ao verificar credenciais.';
            $debug .= "❌ ERRO DETALHADO: " . $e->getMessage() . "<br>";
            $debug .= "Arquivo: " . __FILE__ . "<br>";
            $debug .= "Linha: " . __LINE__ . "<br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Borgely Crochet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--soft-pink) 0%, var(--secondary-color) 50%, var(--primary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="60" cy="40" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: 100px 100px;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(180deg); }
            100% { transform: translateY(0px) rotate(360deg); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
            transform: translateY(0);
            transition: var(--transition);
        }

        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .admin-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
            color: white;
            box-shadow: var(--shadow-lg);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .login-title {
            font-family: 'Quicksand', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 16px;
            color: var(--gray-500);
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 16px;
            font-weight: 500;
            color: var(--gray-800);
            background: var(--white);
            transition: var(--transition);
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(175, 32, 63, 0.1);
            transform: translateY(-1px);
        }

        .form-input:valid {
            border-color: #10b981;
        }

        .login-button {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--primary-light), var(--accent-color));
        }

        .login-button:active {
            transform: translateY(0);
        }

        .message {
            margin-top: 20px;
            padding: 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            animation: slideIn 0.3s ease;
        }

        .message.error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .message.success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .message.debug {
            background: #f6f6f6;
            color: #333;
            border: 1px dashed #aaa;
            font-size: 12px;
            text-align: left;
            margin-top: 30px;
        }

        .message.debug h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #666;
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

        .loading {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .login-button.loading {
            pointer-events: none;
        }

        .login-button.loading .loading {
            display: inline-block;
        }

        .login-button.loading .button-text {
            opacity: 0.7;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
                margin: 20px;
            }
            
            .login-title {
                font-size: 24px;
            }
            
            .admin-icon {
                width: 60px;
                height: 60px;
                font-size: 28px;
            }
        }

        /* Floating Elements */
        .floating-element {
            position: absolute;
            opacity: 0.1;
            animation: floatAround 15s infinite linear;
        }

        .floating-element:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            top: 20%;
            right: 15%;
            animation-delay: -5s;
        }

        .floating-element:nth-child(3) {
            bottom: 20%;
            left: 15%;
            animation-delay: -10s;
        }

        @keyframes floatAround {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(90deg); }
            50% { transform: translateY(-10px) rotate(180deg); }
            75% { transform: translateY(-30px) rotate(270deg); }
        }
    </style>
</head>
<body>
    <!-- Floating Elements -->
    <div class="floating-element">🌸</div>
    <div class="floating-element">✨</div>
    <div class="floating-element">🧶</div>

    <div class="login-container">
        <div class="login-header">
            <div class="admin-icon">🔒</div>
            <h1 class="login-title">Admin Login</h1>
            <p class="login-subtitle">Acesse o painel administrativo</p>
        </div>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label class="form-label" for="usuario">Usuário</label>
                <input 
                    type="text" 
                    id="usuario" 
                    name="usuario" 
                    class="form-input" 
                    required 
                    autofocus
                    autocomplete="username"
                    placeholder="Digite seu usuário"
                    value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="senha">Senha</label>
                <input 
                    type="password" 
                    id="senha" 
                    name="senha" 
                    class="form-input" 
                    required
                    autocomplete="current-password"
                    placeholder="Digite sua senha"
                >
            </div>

            <button type="submit" class="login-button" id="loginButton">
                <span class="loading"></span>
                <span class="button-text">Entrar no Painel</span>
            </button>
        </form>

        <?php if (!empty($erro)): ?>
            <div class="message error">
                ⚠️ <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="message success">
                ✅ <?php echo htmlspecialchars($sucesso); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($debug)): ?>
            <div class="message debug">
                <h3>DEBUG:</h3>
                <?php echo $debug; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('loginButton');
            button.classList.add('loading');
        });

        // Efeito de foco nos inputs
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>