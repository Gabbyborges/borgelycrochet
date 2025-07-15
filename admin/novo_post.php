<?php
include("../db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $titulo = $_POST['titulo'];
  $slug = $_POST['slug'];
  $resumo = $_POST['resumo'];
  $conteudo = $_POST['conteudo'];

  // Verifica se a pasta 'imagens' existe, se não existir cria
  $pasta = "../imagens";
  if (!is_dir($pasta)) {
    mkdir($pasta, 0777, true);
  }

  // Upload da imagem com nome seguro
  $nomeOriginal = $_FILES['imagem']['name'];
  $nomeSeguro = preg_replace('/[^\w.-]/', '_', $nomeOriginal); // remove acentos, espaços e caracteres especiais
  $caminho = $pasta . "/" . $nomeSeguro;

  if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
    $categoria = $_POST['categoria'];
$stmt = $conn->prepare("INSERT INTO blog (titulo, slug, resumo, conteudo, imagem, categoria) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $titulo, $slug, $resumo, $conteudo, $nomeSeguro, $categoria);

    echo "<p style='color:green;'>Post adicionado com sucesso!</p>";
  } else {
    echo "<p style='color:red;'>Erro ao enviar imagem. Verifique a pasta /imagens/.</p>";
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Novo Post</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: var(--white);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-lg);
            border-left: 6px solid var(--primary-color);
        }

        .header h1 {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header p {
            color: var(--gray-600);
            font-size: 1.1rem;
        }

        .form-container {
            background: var(--white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--gray-200);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
        }

        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
            transition: var(--transition);
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            background: var(--white);
            cursor: pointer;
            transition: var(--transition);
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
        }

        .file-input-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: block;
            padding: 12px 16px;
            border: 2px dashed var(--gray-300);
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: var(--gray-50);
        }

        .file-input-label:hover {
            border-color: var(--primary-color);
            background: var(--soft-pink);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--white);
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-md);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .form-container {
                padding: 20px;
            }
        }

        .icon {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
                Adicionar Novo Post ao Blog
            </h1>
            <p>Painel de Administração - Criação de Conteúdo</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <strong>Sucesso!</strong> Post adicionado com sucesso ao blog.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <strong>Erro!</strong> Houve um problema ao adicionar o post. Tente novamente.
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="titulo">
                            <svg class="icon" style="display: inline; width: 16px; height: 16px; margin-right: 5px;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            Título do Post
                        </label>
                        <input type="text" id="titulo" name="titulo" class="form-input" required placeholder="Digite o título do post">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="slug">
                            <svg class="icon" style="display: inline; width: 16px; height: 16px; margin-right: 5px;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                            Slug (URL amigável)
                        </label>
                        <input type="text" id="slug" name="slug" class="form-input" required placeholder="ex: flor-de-croche">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="categoria">
                        <svg class="icon" style="display: inline; width: 16px; height: 16px; margin-right: 5px;" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                        </svg>
                        Categoria do Post
                    </label>
                    <select id="categoria" name="categoria" class="form-select" required>
                        <option value="">Selecione uma categoria</option>
                        <option value="dica">💡 Dicas Extras</option>
                        <option value="fe">📖 Ensinamentos & Fé</option>
                        <option value="lista">🌸 Lista Inspiradora</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="resumo">
                        <svg class="icon" style="display: inline; width: 16px; height: 16px; margin-right: 5px;" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM16 18H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                        Resumo do Post
                    </label>
                    <textarea id="resumo" name="resumo" class="form-textarea" rows="3" required placeholder="Escreva um resumo atrativo do seu post..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="conteudo">
                        <svg class="icon" style="display: inline; width: 16px; height: 16px; margin-right: 5px;" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                        Conteúdo Completo
                    </label>
                    <textarea id="conteudo" name="conteudo" class="form-textarea" rows="10" required placeholder="Escreva o conteúdo completo do seu post..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <svg class="icon" style="display: inline; width: 16px; height: 16px; margin-right: 5px;" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                        </svg>
                        Imagem de Capa
                    </label>
                    <div class="file-input-container">
                        <input type="file" id="imagem" name="imagem" class="file-input" required accept="image/*">
                        <label for="imagem" class="file-input-label">
                            <svg class="icon" style="display: inline; width: 20px; height: 20px; margin-right: 8px;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM16 18H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                            </svg>
                            Clique para selecionar uma imagem
                        </label>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn-primary">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        Publicar Post
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Preview do arquivo selecionado
        document.getElementById('imagem').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = document.querySelector('.file-input-label');
            
            if (file) {
                label.innerHTML = `
                    <svg class="icon" style="display: inline; width: 20px; height: 20px; margin-right: 8px;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    Arquivo selecionado: ${file.name}
                `;
                label.style.borderColor = 'var(--primary-color)';
                label.style.background = 'var(--soft-pink)';
            }
        });

        // Auto-geração do slug baseado no título
        document.getElementById('titulo').addEventListener('input', function(e) {
            const titulo = e.target.value;
            const slug = titulo
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();
            
            document.getElementById('slug').value = slug;
        });
    </script>
</body>
</html>