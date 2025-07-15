<?php
include 'db.php';  // Inclui a conexão com o banco de dados

// Pegar categoria selecionada
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : 'todos';

// Construir query baseada na categoria
if ($categoria_filtro === 'todos') {
    $sql = "SELECT * FROM blog ORDER BY data_postagem DESC";
    $stmt = $conn->prepare($sql);  // Usando $conn ao invés de $conexao
} else {
    $sql = "SELECT * FROM blog WHERE categoria = ? ORDER BY data_postagem DESC";  // Usando '?' para prepared statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $categoria_filtro);  // 's' significa que o parâmetro é uma string
}

$stmt->execute();
$resultado = $stmt->get_result();  // Obter o resultado da execução da consulta
$posts = [];
while ($row = $resultado->fetch_assoc()) {
    $posts[] = $row;  // Armazenar os posts
}

// Função para obter ícone da categoria
function getCategoryIcon($categoria) {
    $icons = [
        'dica' => '💡',
        'fe' => '🙏',
        'lista' => '🌸',
        'tutorial' => '📚',
        'projeto' => '🧶',
        'ensinamento' => '📖'
    ];
    return isset($icons[$categoria]) ? $icons[$categoria] : '📝';
}

// Função para obter nome amigável da categoria
function getCategoryName($categoria) {
    $names = [
        'dica' => '💡 Dicas Extras',
        'fe' => '📖 Fé & Ensinamentos',
        'lista' => '🌸 Listas Inspiradoras',
        'tutorial' => '📚 Tutoriais',
        'projeto' => '🧶 Projetos',
        'ensinamento' => '📖 Ensinamentos'
    ];
    return isset($names[$categoria]) ? $names[$categoria] : '📝 ' . ucfirst($categoria);
}

// Função para formatar data
function formatarData($data) {
    $agora = new DateTime();
    $post_data = new DateTime($data);
    $diff = $agora->diff($post_data);
    
    if ($diff->days == 0) {
        return "📅 Hoje";
    } elseif ($diff->days == 1) {
        return "📅 Ontem";
    } elseif ($diff->days <= 7) {
        return "📅 " . $diff->days . " dias atrás";
    } elseif ($diff->days <= 30) {
        $semanas = floor($diff->days / 7);
        return "📅 " . $semanas . " semana" . ($semanas > 1 ? "s" : "") . " atrás";
    } else {
        return "📅 " . $post_data->format('d/m/Y');
    }
}

// Função para criar excerpt (usar resumo se existir, senão conteúdo)
function criarExcerpt($resumo, $conteudo, $limite = 150) {
    if (!empty($resumo)) {
        return $resumo;
    }
    
    $texto = strip_tags($conteudo);
    if (strlen($texto) > $limite) {
        return substr($texto, 0, $limite) . '...';
    }
    return $texto;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - <?php echo getCategoryName($categoria_filtro); ?></title>
    <style>
        :root {
            --primary-color: #d4839a;
            --primary-dark: #b86f87;
            --secondary-color: #f7e7d3;
            --accent-color: #e8a4c7;
            --soft-pink: #f5e6ea;
            --warm-white: #fefcfa;
            --text-dark: #4a3c42;
            --text-light: #6b5d63;
            --border-light: #e8ddd0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: linear-gradient(135deg, var(--warm-white) 0%, var(--soft-pink) 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--warm-white);
            padding: 15px 0;
            box-shadow: 0 2px 20px rgba(212, 131, 154, 0.3);
            position: relative;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .back-btn {
            background: rgba(254, 252, 250, 0.15);
            border: 2px solid rgba(254, 252, 250, 0.25);
            color: var(--warm-white);
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .back-btn:hover {
            background: rgba(254, 252, 250, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .blog-title {
            font-size: 2.2rem;
            font-weight: 700;
            text-align: center;
            flex: 1;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .menu-toggle {
            display: none;
            background: rgba(254, 252, 250, 0.15);
            border: 2px solid rgba(254, 252, 250, 0.25);
            color: var(--warm-white);
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: rgba(254, 252, 250, 0.25);
        }

        /* Navigation Desktop */
        .nav-container {
            background: var(--warm-white);
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .category-nav {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .category-btn {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--soft-pink) 100%);
            border: 2px solid var(--border-light);
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }

        .category-btn:hover, .category-btn.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--warm-white);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(212, 131, 154, 0.4);
        }

        .category-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(254, 252, 250, 0.3), transparent);
            transition: left 0.5s;
        }

        .category-btn:hover::before {
            left: 100%;
        }

        /* Mobile Sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -300px;
            width: 300px;
            height: 100vh;
            background: var(--warm-white);
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: left 0.3s ease;
            overflow-y: auto;
        }

        .mobile-sidebar.open {
            left: 0;
        }

        .sidebar-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--warm-white);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-title {
            font-weight: 700;
            font-size: 1.2rem;
        }

        .close-sidebar {
            background: none;
            border: none;
            color: var(--warm-white);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .close-sidebar:hover {
            background: rgba(254, 252, 250, 0.2);
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .sidebar-category {
            display: block;
            padding: 15px 25px;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            border-bottom: 1px solid var(--border-light);
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar-category:hover, .sidebar-category.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--warm-white);
            padding-left: 35px;
        }

        .sidebar-category::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary-color);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-category.active::before {
            transform: scaleY(1);
        }

        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Posts Grid */
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .post-card {
            background: var(--warm-white);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(74, 60, 66, 0.1);
            overflow: hidden;
            transition: all 0.4s ease;
            position: relative;
            border: 1px solid var(--border-light);
        }

        .post-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(74, 60, 66, 0.15);
        }

        .post-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--soft-pink) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            position: relative;
            overflow: hidden;
        }

        .post-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(254, 252, 250, 0.3) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .post-card:hover .post-image::before {
            transform: translateX(100%);
        }

        .post-content {
            padding: 25px;
        }

        .post-category {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--primary-color) 100%);
            color: var(--warm-white);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .post-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .post-excerpt {
            color: var(--text-light);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .post-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-top: 15px;
            border-top: 1px solid var(--border-light);
        }

        .post-date {
            color: var(--text-light);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .read-more {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--warm-white);
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .read-more::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(254, 252, 250, 0.3), transparent);
            transition: left 0.5s;
        }

        .read-more:hover::before {
            left: 100%;
        }

        .read-more:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 131, 154, 0.4);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-light);
            grid-column: 1 / -1;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--text-dark);
        }

        /* Current category indicator */
        .current-category {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--soft-pink) 100%);
            border: 2px solid var(--accent-color);
            padding: 15px 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            color: var(--text-dark);
            font-weight: 600;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .posts-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 25px;
            }

            .category-nav {
                gap: 10px;
            }

            .category-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .header {
                padding: 12px 0;
            }

            .header-content {
                position: relative;
            }

            .back-btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }

            .blog-title {
                font-size: 1.6rem;
            }

            .menu-toggle {
                display: block;
            }

            /* Hide desktop navigation on mobile */
            .nav-container {
                display: none;
            }

            .posts-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                margin-top: 20px;
            }

            .post-content {
                padding: 20px;
            }

            .post-title {
                font-size: 1.2rem;
            }

            .post-image {
                height: 150px;
                font-size: 2.5rem;
            }

            .current-category {
                padding: 12px 16px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 10px;
            }

            .header-content {
                gap: 10px;
            }

            .back-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }

            .blog-title {
                font-size: 1.4rem;
            }

            .mobile-sidebar {
                width: 280px;
                left: -280px;
            }

            .post-card {
                border-radius: 15px;
            }

            .post-content {
                padding: 15px;
            }

            .post-category {
                font-size: 0.8rem;
                padding: 4px 12px;
            }

            .read-more {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 320px) {
            .mobile-sidebar {
                width: 100%;
                left: -100%;
            }

            .posts-grid {
                gap: 15px;
            }
        }

        /* Smooth animations */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">📂 Categorias</span>
            <button class="close-sidebar" id="closeSidebar">×</button>
        </div>
        <nav class="sidebar-nav">
            <a href="?categoria=todos" class="sidebar-category <?php echo $categoria_filtro === 'todos' ? 'active' : ''; ?>">
                📋 Todos os Posts
            </a>
            <a href="?categoria=dica" class="sidebar-category <?php echo $categoria_filtro === 'dica' ? 'active' : ''; ?>">
                💡 Dicas Extras
            </a>
            <a href="?categoria=fe" class="sidebar-category <?php echo $categoria_filtro === 'fe' ? 'active' : ''; ?>">
                📖 Fé & Ensinamentos
            </a>
            <a href="?categoria=lista" class="sidebar-category <?php echo $categoria_filtro === 'lista' ? 'active' : ''; ?>">
                🌸 Listas Inspiradoras
            </a>
            <a href="?categoria=tutorial" class="sidebar-category <?php echo $categoria_filtro === 'tutorial' ? 'active' : ''; ?>">
                📚 Tutoriais
            </a>
            <a href="?categoria=projeto" class="sidebar-category <?php echo $categoria_filtro === 'projeto' ? 'active' : ''; ?>">
                🧶 Projetos
            </a>
            <a href="?categoria=ensinamento" class="sidebar-category <?php echo $categoria_filtro === 'ensinamento' ? 'active' : ''; ?>">
                📖 Ensinamentos
            </a>
        </nav>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="back-btn">
                    <span>←</span> <span class="back-text">Início</span>
                </a>
                <h1 class="blog-title">✨ Blog </h1>
                <button class="menu-toggle" id="menuToggle">
                    <span>☰</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Desktop Navigation -->
    <nav class="nav-container">
        <div class="container">
            <div class="category-nav">
                <a href="?categoria=todos" class="category-btn <?php echo $categoria_filtro === 'todos' ? 'active' : ''; ?>">
                    📋 Todos
                </a>
                <a href="?categoria=dica" class="category-btn <?php echo $categoria_filtro === 'dica' ? 'active' : ''; ?>">
                    💡 Dicas Extras
                </a>
                <a href="?categoria=fe" class="category-btn <?php echo $categoria_filtro === 'fe' ? 'active' : ''; ?>">
                    📖 Fé & Ensinamentos
                </a>
                <a href="?categoria=lista" class="category-btn <?php echo $categoria_filtro === 'lista' ? 'active' : ''; ?>">
                    🌸 Listas Inspiradoras
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container">
        <!-- Current Category Indicator (Mobile) -->
        <div class="current-category">
            📂 Visualizando: <?php echo getCategoryName($categoria_filtro); ?>
        </div>

        <div class="posts-grid" id="postsGrid">
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <h3>🌸 Nenhum post encontrado</h3>
                    <p>Não há posts publicados nesta categoria ainda. Que tal explorar outras categorias?</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <div class="post-image">
                            <?php echo getCategoryIcon($post['categoria']); ?>
                        </div>
                        <div class="post-content">
                            <span class="post-category"><?php echo getCategoryName($post['categoria']); ?></span>
                            <h2 class="post-title"><?php echo htmlspecialchars($post['titulo']); ?></h2>
                            <p class="post-excerpt"><?php echo criarExcerpt($post['resumo'], $post['conteudo']); ?></p>
                            <div class="post-meta">
                                <span class="post-date"><?php echo formatarData($post['data_postagem']); ?></span>
                            </div>
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">
                                Ler mais <span>→</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Mobile Menu Functionality
        const menuToggle = document.getElementById('menuToggle');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const closeSidebar = document.getElementById('closeSidebar');
        const overlay = document.getElementById('overlay');

        function openSidebar() {
            mobileSidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebarFunc() {
            mobileSidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        menuToggle.addEventListener('click', openSidebar);
        closeSidebar.addEventListener('click', closeSidebarFunc);
        overlay.addEventListener('click', closeSidebarFunc);

        // Close sidebar when clicking on a category link
        document.querySelectorAll('.sidebar-category').forEach(link => {
            link.addEventListener('click', closeSidebarFunc);
        });

        // Smooth scroll para links
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

        // Intersection Observer for post animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Apply initial styles and observe post cards
        document.querySelectorAll('.post-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });

        // Handle keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileSidebar.classList.contains('open')) {
                closeSidebarFunc();
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileSidebar.classList.contains('open')) {
                closeSidebarFunc();
            }
        });

        // Prevent scroll when sidebar is open on mobile
        let startY = 0;
        mobileSidebar.addEventListener('touchstart', function(e) {
            startY = e.touches[0].clientY;
        });

        mobileSidebar.addEventListener('touchmove', function(e) {
            const currentY = e.touches[0].clientY;
            const scrollTop = this.scrollTop;
            const scrollHeight = this.scrollHeight;
            const clientHeight = this.clientHeight;

            if ((scrollTop <= 0 && currentY > startY) || 
                (scrollTop >= scrollHeight - clientHeight && currentY < startY)) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>