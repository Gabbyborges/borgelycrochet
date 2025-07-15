<?php
include("../db.php");

$result = $conn->query("SELECT * FROM blog ORDER BY data_postagem DESC");
$totalPosts = $result->num_rows;
$publishedPosts = 0;
$draftPosts = 0;

// Contar posts publicados e rascunhos (se houver campo status)
$posts = [];
while($post = $result->fetch_assoc()) {
    $posts[] = $post;
    // Assumindo que posts são publicados por padrão
    $publishedPosts++;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Posts do Blog</title>
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

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--white);
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-md);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-secondary:hover {
            background: var(--primary-color);
            color: var(--white);
        }

        .btn-danger {
            background: #dc3545;
            color: var(--white);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .search-bar {
            background: var(--white);
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 1rem;
            transition: var(--transition);
            min-width: 250px;
        }

        .search-bar:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
        }

        .table-container {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--gray-200);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: var(--white);
        }

        .table th {
            padding: 16px;
            font-weight: 600;
            text-align: left;
            font-size: 1rem;
        }

        .table td {
            padding: 16px;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: var(--soft-pink);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .post-id {
            font-weight: 600;
            color: var(--primary-color);
            background: var(--soft-pink);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .post-title {
            font-weight: 600;
            color: var(--gray-800);
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .post-date {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-500);
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-200);
            text-align: center;
        }

        .stat-card h3 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-card p {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        .icon {
            width: 20px;
            height: 20px;
        }

        .icon-sm {
            width: 16px;
            height: 16px;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-bar {
                min-width: 100%;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .post-title {
                max-width: 150px;
            }
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .category-dica {
            background: #e3f2fd;
            color: #1565c0;
        }

        .category-fe {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .category-lista {
            background: #e8f5e8;
            color: #2e7d32;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                🧶 Gerenciar Posts do Blog
            </h1>
            <p>Painel de Administração - Gestão de Conteúdo</p>
            
            <div class="header-actions">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <input type="text" class="search-bar" placeholder="Pesquisar posts..." id="searchInput">
                </div>
                <a href="novo_post.php" class="btn-primary">
                    <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Novo Post
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <strong>Sucesso!</strong> Operação realizada com sucesso.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <strong>Erro!</strong> Houve um problema ao realizar a operação.
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $totalPosts ?></h3>
                <p>Total de Posts</p>
            </div>
            <div class="stat-card">
                <h3><?= $publishedPosts ?></h3>
                <p>Posts Publicados</p>
            </div>
            <div class="stat-card">
                <h3><?= $draftPosts ?></h3>
                <p>Rascunhos</p>
            </div>
        </div>

        <?php if ($totalPosts > 0): ?>
            <!-- Posts Table -->
            <div class="table-container">
                <table class="table" id="postsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Categoria</th>
                            <th>Data de Publicação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($posts as $post): ?>
                            <tr>
                                <td><span class="post-id">#<?= str_pad($post['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
                                <td class="post-title" title="<?= htmlspecialchars($post['titulo']) ?>">
                                    <?= htmlspecialchars($post['titulo']) ?>
                                </td>
                                <td>
                                    <?php
                                    $categoria = $post['categoria'] ?? 'dica';
                                    $categoriaLabels = [
                                        'dica' => '💡 Dicas',
                                        'fe' => '📖 Fé',
                                        'lista' => '🌸 Lista'
                                    ];
                                    $categoriaClass = 'category-' . $categoria;
                                    ?>
                                    <span class="status-badge <?= $categoriaClass ?>">
                                        <?= $categoriaLabels[$categoria] ?? '💡 Dicas' ?>
                                    </span>
                                </td>
                                <td class="post-date">
                                    <?= date('d/m/Y H:i', strtotime($post['data_postagem'])) ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="editar_post.php?id=<?= $post['id'] ?>" class="btn-secondary">
                                            <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                            </svg>
                                            Editar
                                        </a>
                                        <button class="btn-danger" onclick="confirmDelete(<?= $post['id'] ?>)">
                                            <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                            </svg>
                                            Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
                <h3>Nenhum post encontrado</h3>
                <p>Comece criando seu primeiro post no blog.</p>
                <a href="novo_post.php" class="btn-primary" style="margin-top: 20px;">
                    <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Criar Primeiro Post
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('#postsTable tbody tr');
            
            tableRows.forEach(row => {
                const title = row.querySelector('.post-title').textContent.toLowerCase();
                const id = row.querySelector('.post-id').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || id.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Confirm delete function
        function confirmDelete(postId) {
            if (confirm('Tem certeza que deseja excluir este post? Esta ação não pode ser desfeita.')) {
                window.location.href = `excluir_post.php?id=${postId}`;
            }
        }

        // Add hover effects
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>