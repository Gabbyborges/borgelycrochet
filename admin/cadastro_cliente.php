<?php
include("../db.php"); // Corrigido para subir 1 nível

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $email = $conn->real_escape_string($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // senha segura

    // Verifica se e-mail já existe
    $check = $conn->query("SELECT * FROM clientes WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $mensagem = "⚠️ Este e-mail já está cadastrado.";
    } else {
        $sql = "INSERT INTO clientes (nome, email, senha) VALUES ('$nome', '$email', '$senha')";
        if ($conn->query($sql)) {
            $mensagem = "✅ Cadastro realizado com sucesso! Faça login.";
        } else {
            $mensagem = "Erro ao cadastrar: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cadastro de Cliente</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 50%, #f3e8ff 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px;
      position: relative;
      overflow: hidden;
    }

    /* Elementos decorativos de fundo */
    body::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(236, 72, 153, 0.05) 0%, transparent 70%);
      animation: float 20s ease-in-out infinite;
      z-index: -1;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
    }

    /* Container principal */
    .register-container {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(236, 72, 153, 0.1);
      border-radius: 24px;
      padding: 48px 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 
        0 20px 40px rgba(236, 72, 153, 0.1),
        0 1px 3px rgba(0, 0, 0, 0.05);
      position: relative;
      transition: all 0.3s ease;
    }

    .register-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #ec4899, #f472b6, #f9a8d4);
      border-radius: 24px 24px 0 0;
    }

    .register-container:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 25px 50px rgba(236, 72, 153, 0.15),
        0 2px 6px rgba(0, 0, 0, 0.08);
    }

    /* Título */
    h2 {
      color: #1f2937;
      font-weight: 600;
      font-size: 28px;
      margin-bottom: 8px;
      text-align: center;
      line-height: 1.2;
    }

    .subtitle {
      color: #6b7280;
      font-size: 16px;
      font-weight: 400;
      text-align: center;
      margin-bottom: 32px;
      line-height: 1.4;
    }

    /* Formulário */
    form {
      width: 100%;
    }

    .form-group {
      margin-bottom: 24px;
      position: relative;
    }

    label {
      display: block;
      font-weight: 500;
      color: #374151;
      margin-bottom: 8px;
      font-size: 14px;
      letter-spacing: 0.025em;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 16px 20px;
      border: 2px solid #f3f4f6;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 400;
      color: #1f2937;
      background: #fefefe;
      transition: all 0.2s ease;
      outline: none;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus {
      border-color: #ec4899;
      box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
      background: #fff;
    }

    input[type="text"]::placeholder,
    input[type="email"]::placeholder,
    input[type="password"]::placeholder {
      color: #9ca3af;
      font-weight: 400;
    }

    /* Botão */
    button {
      width: 100%;
      background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%);
      color: white;
      padding: 16px;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.2s ease;
      letter-spacing: 0.025em;
      position: relative;
      overflow: hidden;
    }

    button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s ease;
    }

    button:hover::before {
      left: 100%;
    }

    button:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 25px rgba(236, 72, 153, 0.3);
    }

    button:active {
      transform: translateY(0);
    }

    /* Mensagem */
    .mensagem {
      margin-top: 16px;
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      text-align: center;
      animation: slideIn 0.3s ease;
    }

    .mensagem.sucesso {
      background: rgba(34, 197, 94, 0.1);
      border: 1px solid rgba(34, 197, 94, 0.2);
      color: #16a34a;
    }

    .mensagem.erro {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.2);
      color: #dc2626;
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

    /* Link de login */
    .login-link {
      margin-top: 32px;
      text-align: center;
      font-size: 14px;
      color: #6b7280;
      line-height: 1.5;
    }

    .login-link a {
      color: #ec4899;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s ease;
      position: relative;
    }

    .login-link a:hover {
      color: #be185d;
    }

    .login-link a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #ec4899, #f472b6);
      transition: width 0.3s ease;
    }

    .login-link a:hover::after {
      width: 100%;
    }

    /* Responsividade */
    @media (max-width: 480px) {
      .register-container {
        padding: 32px 24px;
        margin: 16px;
      }

      h2 {
        font-size: 24px;
      }

      .subtitle {
        font-size: 14px;
      }

      input[type="text"],
      input[type="email"],
      input[type="password"] {
        padding: 14px 16px;
        font-size: 16px;
      }

      button {
        padding: 14px;
        font-size: 15px;
      }
    }

    /* Animação de entrada */
    .register-container {
      animation: fadeInUp 0.6s ease forwards;
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
  <div class="register-container">
    <h2>Seja bem-vinda!</h2>
    <p class="subtitle">Crie sua conta para começar</p>

    <form method="POST" autocomplete="off">
      <div class="form-group">
        <label for="nome">Nome completo</label>
        <input type="text" id="nome" name="nome" placeholder="Seu nome completo" required />
      </div>

      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="seu@email.com" required />
      </div>

      <div class="form-group">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="••••••••" required />
      </div>

      <button type="submit">Cadastrar</button>

      <?php 
      if (!empty($mensagem)) {
        $classe = (strpos($mensagem, '✅') !== false) ? 'sucesso' : 'erro';
        echo "<div class='mensagem $classe'>$mensagem</div>";
      }
      ?>
    </form>

    <div class="login-link">
      Já tem conta? <a href="../cliente/login_cliente.php">Faça login</a>
    </div>
  </div>
</body>
</html>