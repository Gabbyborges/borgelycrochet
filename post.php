<?php
require_once 'db.php';

// Função para sanitizar dados
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

// Função para formatar data
function formatarData($data) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    
    $timestamp = strtotime($data);
    $dia = date('j', $timestamp);
    $mes = $meses[date('n', $timestamp)];
    $ano = date('Y', $timestamp);
    
    return "$dia de $mes, $ano";
}

// Inicializar variáveis
$post = null;
$erro = null;


$post_id = null;
$slug = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $post_id = (int)$_GET['id']; // Converte para inteiro para segurança
} elseif (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = sanitize($_GET['slug']);
} else {
    $erro = "Post não encontrado.";
}

if (!$erro) {
    try {
        // Buscar por ID ou slug
        if ($post_id) {
            $query = "SELECT * FROM blog WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $post_id);
        } else {
            $query = "SELECT * FROM blog WHERE slug = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $slug);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $post = mysqli_fetch_assoc($result);
            
            // Simular dados que não existem na tabela
            $post['visualizacoes'] = rand(50, 500);
            $post['categoria_nome'] = $post['categoria'];
            $post['categoria_icone'] = '📝';
            $post['categoria_cor'] = '#e91e63';
            $post['created_at'] = $post['data_postagem'];
            $post['tempo_leitura'] = ceil(str_word_count(strip_tags($post['conteudo'])) / 200);
            $post['meta_title'] = $post['titulo'];
            $post['meta_description'] = !empty($post['resumo']) ? $post['resumo'] : substr(strip_tags($post['conteudo']), 0, 150);
            
        } else {
            $erro = "Post não encontrado.";
        }
        
        mysqli_stmt_close($stmt);
        
    } catch(Exception $e) {
        $erro = "Erro ao carregar o post: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post ? htmlspecialchars($post['meta_title']) : 'Post não encontrado'; ?> </title>
    
    <?php if ($post): ?>
    <meta name="description" content="<?php echo htmlspecialchars($post['meta_description']); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($post['titulo']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($post['meta_description']); ?>">
    <?php if (!empty($post['imagem'])): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($post['imagem']); ?>">
    <?php endif; ?>
    <meta property="og:url" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <?php endif; ?>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.8;
            color: #333;
            background: linear-gradient(135deg, #fff5f5 0%, #ffeef8 100%);
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #e91e63 0%, #d81b60 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 20px rgba(233, 30, 99, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .blog-title {
            font-size: 1.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Main Content */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .post-container {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .post-header {
            padding: 40px 40px 0;
        }

        .categoria-label {
            display: inline-block;
            background: linear-gradient(135deg, <?php echo $post['categoria_cor'] ?? '#e91e63'; ?> 0%, <?php echo $post['categoria_cor'] ?? '#d81b60'; ?> 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
        }

        .post-titulo {
            color: #2c3e50;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 25px;
            line-height: 1.3;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .post-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .post-imagem {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 0;
            margin-bottom: 0;
            position: relative;
        }

        .image-container {
            position: relative;
            overflow: hidden;
            margin: 0 -40px 40px -40px;
        }

        .image-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(transparent, rgba(255, 255, 255, 0.8));
        }

        .post-content {
            padding: 0 40px 40px;
        }

        .post-conteudo {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
            text-align: justify;
        }

        .post-conteudo p {
            margin-bottom: 20px;
        }

        .post-conteudo h2 {
            color: #e91e63;
            font-size: 1.8rem;
            margin: 35px 0 20px;
            font-weight: 700;
        }

        .post-conteudo h3 {
            color: #495057;
            font-size: 1.4rem;
            margin: 30px 0 15px;
            font-weight: 600;
        }

        blockquote {
            background: linear-gradient(135deg, #fff5f8 0%, #ffeef8 100%);
            border-left: 5px solid #e91e63;
            margin: 30px 0;
            padding: 25px 30px;
            font-style: italic;
            color: #555;
            border-radius: 15px;
            position: relative;
            box-shadow: 0 5px 20px rgba(233, 30, 99, 0.1);
        }

        blockquote::before {
            content: '"';
            font-size: 4rem;
            color: #e91e63;
            position: absolute;
            top: -10px;
            left: 20px;
            opacity: 0.3;
        }

        /* Error Message */
        .error-container {
            background: white;
            border-radius: 25px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .error-message {
            color: #e91e63;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        /* Action Buttons */
        .action-buttons {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
            font-size: 1rem;
        }

        .btn-blog {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }

        .btn-blog:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(108, 117, 125, 0.4);
        }

        .btn-whatsapp {
            background: linear-gradient(135deg, #25d366 0%, #1ebe5d 100%);
            color: white;
        }

        .btn-whatsapp:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        /* Reading Progress Bar */
        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(90deg, #e91e63, #d81b60);
            z-index: 1000;
            transition: width 0.1s ease;
        }

        /* Share Floating Button */
        .floating-share {
            position: fixed;
            right: 30px;
            bottom: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #25d366 0%, #1ebe5d 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4);
            transition: all 0.3s ease;
            z-index: 100;
        }

        .floating-share:hover {
            transform: scale(1.1);
            box-shadow: 0 15px 40px rgba(37, 211, 102, 0.6);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px 15px;
            }

            .post-header,
            .post-content {
                padding-left: 25px;
                padding-right: 25px;
            }

            .image-container {
                margin-left: -25px;
                margin-right: -25px;
            }

            .post-titulo {
                font-size: 2rem;
            }

            .post-imagem {
                height: 250px;
            }

            .post-conteudo {
                font-size: 1rem;
                text-align: left;
            }

            .action-buttons {
                padding: 20px;
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .floating-share {
                right: 20px;
                bottom: 20px;
                width: 55px;
                height: 55px;
            }

            .blog-title {
                display: none;
            }

            .header-content {
                justify-content: flex-start;
            }
        }

        @media (max-width: 480px) {
            .post-header,
            .post-content {
                padding-left: 20px;
                padding-right: 20px;
            }

            .image-container {
                margin-left: -20px;
                margin-right: -20px;
            }

            .post-titulo {
                font-size: 1.7rem;
                line-height: 1.2;
            }

            .post-imagem {
                height: 200px;
            }

            .action-buttons {
                padding: 15px;
            }

            blockquote {
                padding: 20px;
                margin: 20px 0;
            }
        }

        /* Animation for content loading */
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

        .post-container {
            animation: fadeInUp 0.8s ease;
        }

        .action-buttons {
            animation: fadeInUp 0.8s ease 0.2s both;
        }
    </style>
</head>
<body>
    <!-- Progress Bar -->
    <div class="progress-bar" id="progressBar"></div>

    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="blog.php" class="back-btn">
                <span>←</span> Voltar para o Blog
            </a>
            <h1 class="blog-title">🧶 Borgely Crochet</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <?php if ($erro): ?>
            <div class="error-container">
                <div class="error-message"><?php echo htmlspecialchars($erro); ?></div>
                <a href="blog.php" class="btn btn-blog">Voltar para o Blog</a>
            </div>
        <?php else: ?>
            <article class="post-container">
                <div class="post-header">
                    <span class="categoria-label">
                        <?php echo $post['categoria_icone']; ?> <?php echo htmlspecialchars($post['categoria_nome'] ?? 'Sem Categoria'); ?>
                    </span>
                    
                    <h1 class="post-titulo"><?php echo htmlspecialchars($post['titulo']); ?></h1>
                    
                    <div class="post-meta">
                        <div class="meta-item">
                            <span>📅</span>
                            <span><?php echo formatarData($post['created_at']); ?></span>
                        </div>
                        <div class="meta-item">
                            <span>⏱️</span>
                            <span><?php echo $post['tempo_leitura']; ?> min de leitura</span>
                        </div>
                        <div class="meta-item">
                            <span>👁️</span>
                            <span><?php echo number_format($post['visualizacoes'], 0, ',', '.'); ?> visualizações</span>
                        </div>
                    </div>
                </div>

                <?php if (!empty($post['imagem'])): ?>
                <div class="image-container">
                    <img src="<?php echo htmlspecialchars($post['imagem']); ?>" 
                         class="post-imagem" 
                         alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                </div>
                <?php endif; ?>

                <div class="post-content">
                    <div class="post-conteudo">
                        <?php 
                        // O conteúdo pode estar com tags HTML que precisam ser processadas
                        // Vamos exibir o conteúdo de forma segura
                        if (!empty($post['conteudo'])) {
                            // Se o conteúdo tem HTML, exibe diretamente
                            // Se não, converte quebras de linha em parágrafos
                            if (strip_tags($post['conteudo']) != $post['conteudo']) {
                                echo $post['conteudo'];
                            } else {
                                echo nl2br(htmlspecialchars($post['conteudo']));
                            }
                        } else {
                            echo "<p>Conteúdo não disponível.</p>";
                        }
                        ?>
                    </div>
                </div>
            </article>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="blog.php" class="btn btn-blog">
                    <span>←</span> Voltar para o Blog
                </a>
                <a href="#" class="btn btn-whatsapp" id="whatsappShare" target="_blank">
                    <span>💬</span> Compartilhar no WhatsApp
                </a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Floating Share Button -->
    <?php if (!$erro): ?>
    <a href="#" class="floating-share" id="floatingWhatsapp" target="_blank" title="Compartilhar no WhatsApp">
        💬
    </a>
    <?php endif; ?>

    <script>
        // Reading Progress Bar
        window.addEventListener('scroll', function() {
            const progressBar = document.getElementById('progressBar');
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;
            progressBar.style.width = Math.min(scrollPercent, 100) + '%';
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // WhatsApp share functionality
        <?php if ($post): ?>
        function generateWhatsAppLink() {
            const postTitle = "<?php echo addslashes($post['titulo']); ?>";
            const currentUrl = window.location.href;
            const message = `Olha esse post lindo da Borgely Crochet 💖: ${postTitle} 👉 ${currentUrl}`;
            return `https://wa.me/?text=${encodeURIComponent(message)}`;
        }

        // Update WhatsApp links
        document.addEventListener('DOMContentLoaded', function() {
            const whatsappUrl = generateWhatsAppLink();
            
            const whatsappShare = document.getElementById('whatsappShare');
            const floatingWhatsapp = document.getElementById('floatingWhatsapp');
            
            if (whatsappShare) whatsappShare.href = whatsappUrl;
            if (floatingWhatsapp) floatingWhatsapp.href = whatsappUrl;
        });
        <?php endif; ?>
    </script>
</body>
</html>