<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_admin.php");
    exit;
}

include("../db.php");

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $preco = floatval(str_replace(',', '.', $_POST['preco']));
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $categoria = $conn->real_escape_string($_POST['categoria']);
    
    $caminhoRelativo = "";

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (in_array($extensao, $extensoesPermitidas)) {
            $nomeArquivo = uniqid() . '.' . $extensao;
            $caminhoFisico = '../uploads/' . $nomeArquivo;

            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0777, true);
            }

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoFisico)) {
                $caminhoRelativo = 'uploads/' . $nomeArquivo;
            } else {
                $mensagem = "Falha ao mover o arquivo de imagem.";
            }
        } else {
            $mensagem = "Extensão de imagem não permitida. Use jpg, jpeg, png ou gif.";
        }
    } else {
        $mensagem = "Erro no upload da imagem.";
    }

    if (!$mensagem) {
        $sql = "INSERT INTO produtos (nome, preco, descricao, imagem, categoria) VALUES ('$nome', $preco, '$descricao', '$caminhoRelativo', '$categoria')";
        if ($conn->query($sql)) {
            $mensagem = "Produto cadastrado com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar produto: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
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

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 24px;
            padding: 12px 20px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .back-btn:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateX(-4px);
            box-shadow: var(--shadow-md);
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: var(--gray-500);
            font-size: 1.1rem;
        }

        /* Form Container */
        .form-container {
            background: var(--white);
            border-radius: 24px;
            padding: 40px;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 28px;
        }

        .form-group label {
            display: block;
            color: var(--gray-700);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .form-group label i {
            color: var(--primary-color);
            margin-right: 8px;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Quicksand', sans-serif;
            transition: var(--transition);
            background: var(--gray-50);
        }

        .form-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
            outline: none;
            background: var(--white);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23b86f87" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 20px;
            cursor: pointer;
        }

        .form-select option {
            padding: 12px;
            font-family: 'Quicksand', sans-serif;
        }

        /* File Upload */
        .upload-container {
            position: relative;
        }

        .upload-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--white);
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Quicksand', sans-serif;
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .file-input {
            display: none;
        }

        .file-info {
            margin-top: 12px;
            padding: 12px 16px;
            background: var(--soft-pink);
            border-radius: 8px;
            color: var(--gray-600);
            font-size: 0.9rem;
            border-left: 4px solid var(--primary-color);
        }

        .file-selected {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--white);
            padding: 18px 24px;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Quicksand', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: var(--transition);
        }

        .submit-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: var(--white);
        }

        .alert-error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: var(--white);
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

        .form-container {
            animation: fadeInUp 0.6s ease-out;
        }

        .form-group {
            animation: fadeInUp 0.6s ease-out;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }

            .form-container {
                padding: 24px;
                margin: 0 10px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .form-input, .submit-btn {
                padding: 14px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="painel_admin.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Painel
        </a>

        <div class="header">
            <h1>Cadastrar Produto</h1>
            <p>Adicione um novo produto ao seu catálogo</p>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert <?= strpos($mensagem, 'sucesso') !== false ? 'alert-success' : 'alert-error' ?>">
                <i class="fas <?= strpos($mensagem, 'sucesso') !== false ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nome">
                        <i class="fas fa-tag"></i>
                        Nome do Produto
                    </label>
                    <input type="text" id="nome" name="nome" class="form-input" required placeholder="Digite o nome do produto">
                </div>

                <div class="form-group">
                    <label for="preco">
                        <i class="fas fa-dollar-sign"></i>
                        Preço
                    </label>
                    <input type="text" id="preco" name="preco" class="form-input" required placeholder="Ex: 250,00">
                </div>

                <div class="form-group">
                    <label for="descricao">
                        <i class="fas fa-align-left"></i>
                        Descrição
                    </label>
                    <textarea id="descricao" name="descricao" class="form-input form-textarea" required placeholder="Descreva o produto em detalhes"></textarea>
                </div>

                <div class="form-group">
                    <label for="categoria">
                        <i class="fas fa-folder"></i>
                        Categoria
                    </label>
                    <select id="categoria" name="categoria" class="form-input form-select" required>
                        <option value="">-- Selecione uma categoria --</option>
                        <option value="Roupas">Roupas</option>
                        <option value="Amigurumi">Amigurumi</option>
                        <option value="Infantil">Infantil</option>
                        <option value="Buquês">Buquês</option>
                        <option value="Bolsas">Bolsas</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-image"></i>
                        Imagem do Produto
                    </label>
                    <div class="upload-container">
                        <label for="imagem" class="upload-btn">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Selecionar Imagem
                        </label>
                        <input type="file" id="imagem" name="imagem" class="file-input" accept="image/*" required>
                        <div id="file-info" class="file-info">
                            <i class="fas fa-info-circle"></i>
                            Nenhum arquivo selecionado
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-plus-circle"></i>
                    Cadastrar Produto
                </button>
            </form>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('imagem');
        const fileInfo = document.getElementById('file-info');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
                
                fileInfo.innerHTML = `
                    <i class="fas fa-check-circle"></i>
                    <span class="file-selected">Arquivo selecionado: ${fileName}</span>
                    <br>
                    <small>Tamanho: ${fileSize} MB</small>
                `;
            } else {
                fileInfo.innerHTML = `
                    <i class="fas fa-info-circle"></i>
                    Nenhum arquivo selecionado
                `;
            }
        });

        // Animação no submit
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = document.querySelector('.submit-btn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cadastrando...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>