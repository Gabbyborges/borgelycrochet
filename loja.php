<?php
include("db.php");

// Filtros
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

// Montar a consulta
$sql = "SELECT * FROM produtos WHERE 1";
if ($busca !== '') {
  $buscaEsc = $conn->real_escape_string($busca);
  $sql .= " AND (nome LIKE '%$buscaEsc%' OR descricao LIKE '%$buscaEsc%')";
}
if ($categoria !== '') {
  $categoriaEsc = $conn->real_escape_string($categoria);
  $sql .= " AND categoria = '$categoriaEsc'";
}
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);

// Categorias fixas conforme solicitado
$categorias_fixas = ['Roupas', 'Amigurumi', 'Infantil', 'Buquês', 'Bolsa', 'Outros'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <title>Loja Moderna</title>
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
      background: linear-gradient(135deg, var(--soft-pink) 0%, var(--white) 100%);
      color: var(--gray-900);
      line-height: 1.6;
      min-height: 100vh;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1rem;
    }

    /* Header */
    .header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      border-bottom: 1px solid var(--gray-200);
      transition: var(--transition);
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 0;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-color);
      text-decoration: none;
      transition: var(--transition);
    }

    .logo:hover {
      transform: scale(1.05);
    }

    .nav-btn {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: var(--white);
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: var(--transition);
      box-shadow: var(--shadow-md);
    }

    .nav-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
      color: var(--white);
    }

    /* Main Content */
    .main {
      margin-top: 100px;
      padding: 2rem 0;
    }

    .hero {
      text-align: center;
      padding: 3rem 0;
      background: linear-gradient(135deg, var(--white), var(--soft-pink));
      border-radius: 20px;
      margin-bottom: 3rem;
      box-shadow: var(--shadow-lg);
    }

    .hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.5rem, 5vw, 4rem);
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 1rem;
    }

    .hero p {
      font-size: 1.25rem;
      color: var(--gray-600);
      max-width: 600px;
      margin: 0 auto;
    }

    /* Search Section */
    .search-section {
      background: var(--white);
      border-radius: 20px;
      padding: 2rem;
      margin-bottom: 3rem;
      box-shadow: var(--shadow-xl);
      border: 1px solid var(--gray-200);
    }

    .search-form {
      display: grid;
      grid-template-columns: 1fr 250px auto;
      gap: 1rem;
      align-items: end;
    }

    .input-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--gray-700);
    }

    .input-group input,
    .input-group select {
      width: 100%;
      padding: 1rem;
      border: 2px solid var(--gray-200);
      border-radius: 12px;
      font-size: 1rem;
      transition: var(--transition);
      background: var(--white);
    }

    .input-group input:focus,
    .input-group select:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(184, 111, 135, 0.1);
    }

    .search-btn {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: var(--white);
      border: none;
      padding: 1rem 2rem;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 0.5rem;
      box-shadow: var(--shadow-md);
    }

    .search-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
      color: var(--white);
    }

    /* Products Grid */
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .product-card {
      background: var(--white);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: var(--shadow-md);
      transition: var(--transition);
      border: 1px solid var(--gray-200);
    }

    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-xl);
    }

    .product-image {
      position: relative;
      height: 200px;
      overflow: hidden;
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: var(--transition);
    }

    .product-card:hover .product-image img {
      transform: scale(1.05);
    }

    .product-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      background: var(--primary-color);
      color: var(--white);
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .product-info {
      padding: 1.5rem;
    }

    .product-name {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--gray-900);
      margin-bottom: 0.5rem;
      line-height: 1.3;
    }

    .product-price {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--primary-color);
      margin-bottom: 1rem;
    }

    .product-btn {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: var(--white);
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 10px;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: var(--transition);
      box-shadow: var(--shadow-sm);
      width: 100%;
      justify-content: center;
    }

    .product-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
      color: var(--white);
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      background: var(--white);
      border-radius: 20px;
      box-shadow: var(--shadow-lg);
    }

    .empty-state i {
      font-size: 4rem;
      color: var(--secondary-color);
      margin-bottom: 1rem;
    }

    .empty-state h3 {
      font-size: 1.5rem;
      color: var(--gray-800);
      margin-bottom: 1rem;
    }

    .empty-state p {
      color: var(--gray-600);
      margin-bottom: 1.5rem;
    }

    .empty-state a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 600;
    }

    .empty-state a:hover {
      color: var(--accent-color);
    }

    /* Loading */
    .loading {
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: var(--white);
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .header-content {
        flex-direction: column;
        gap: 1rem;
      }

      .main {
        margin-top: 140px;
      }

      .search-form {
        grid-template-columns: 1fr;
      }

      .hero {
        padding: 2rem 1rem;
      }

      .search-section {
        padding: 1.5rem;
      }

      .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      }
    }

    @media (max-width: 480px) {
      .products-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="container">
      <div class="header-content">
        <a href="index.php" class="logo">Loja</a>
        <a href="index.php" class="nav-btn">
          <i class="fas fa-home"></i> Início
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="main">
    <div class="container">
      <!-- Hero Section -->
      <section class="hero">
        <h1>Nossa Coleção</h1>
        <p>Descubra peças únicas feitas com carinho e dedicação especialmente para você</p>
      </section>

      <!-- Search Section -->
      <section class="search-section">
        <form class="search-form" method="GET">
          <div class="input-group">
            <label for="busca">Buscar produtos</label>
            <input 
              type="text" 
              id="busca"
              name="busca" 
              placeholder="Digite o nome do produto..." 
              value="<?php echo htmlspecialchars($busca); ?>"
            />
          </div>
          
          <div class="input-group">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria">
              <option value="">Todas as categorias</option>
              <?php foreach ($categorias_fixas as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $cat === $categoria ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class="search-btn">
            <i class="fas fa-search"></i>
            Filtrar
          </button>
        </form>
      </section>

      <!-- Products Section -->
      <section class="products-section">
        <?php if ($result->num_rows == 0): ?>
          <div class="empty-state">
            <i class="fas fa-search"></i>
            <h3>Nenhum produto encontrado</h3>
            <p>Tente ajustar os filtros ou explore nossa coleção completa</p>
            <a href="?">Ver todos os produtos</a>
          </div>
        <?php else: ?>
          <div class="products-grid">
            <?php while($produto = $result->fetch_assoc()): ?>
              <div class="product-card">
                <div class="product-image">
                  <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" loading="lazy" />
                  <div class="product-badge">
                    <?= htmlspecialchars($produto['categoria'] ?? 'Produto') ?>
                  </div>
                </div>
                <div class="product-info">
                  <h3 class="product-name"><?= htmlspecialchars($produto['nome']) ?></h3>
                  <div class="product-price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                  <a href="produto.php?id=<?= $produto['id'] ?>" class="product-btn">
                    <i class="fas fa-eye"></i>
                    Ver detalhes
                  </a>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php endif; ?>
      </section>
    </div>
  </main>

  <script>
    // Header scroll effect
    const header = document.querySelector('.header');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        header.style.background = 'rgba(255, 255, 255, 0.98)';
        header.style.boxShadow = 'var(--shadow-md)';
      } else {
        header.style.background = 'rgba(255, 255, 255, 0.95)';
        header.style.boxShadow = 'none';
      }
    });

    // Form loading state
    document.querySelector('.search-form').addEventListener('submit', (e) => {
      const btn = document.querySelector('.search-btn');
      const original = btn.innerHTML;
      btn.innerHTML = '<div class="loading"></div> Buscando...';
      btn.disabled = true;
      
      setTimeout(() => {
        btn.innerHTML = original;
        btn.disabled = false;
      }, 2000);
    });

    // Smooth animations
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    });

    document.querySelectorAll('.product-card').forEach(card => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      observer.observe(card);
    });
  </script>
</body>
</html>