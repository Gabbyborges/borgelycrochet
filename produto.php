<?php
include("db.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
  echo "Produto não encontrado.";
  exit;
}

$sql = "SELECT * FROM produtos WHERE id = $id LIMIT 1";
$res = $conn->query($sql);

if ($res->num_rows === 0) {
  echo "Produto não encontrado.";
  exit;
}

$produto = $res->fetch_assoc();

// Buscar imagens adicionais do produto com tratamento de erro
$imagens_adicionais = [];

try {
    $imagens_sql = "SELECT imagem FROM produtos_imagem WHERE produto_id = ? ORDER BY id";
    $stmt = $conn->prepare($imagens_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $imagens_result = $stmt->get_result();
    
    if ($imagens_result && $imagens_result->num_rows > 0) {
        while ($img = $imagens_result->fetch_assoc()) {
            $imagens_adicionais[] = $img['imagem'];
        }
    }
    $stmt->close();
} catch (Exception $e) {
    // Se der erro, continue normalmente
}

// Se não houver imagens adicionais, use apenas a imagem principal
if (empty($imagens_adicionais)) {
    $imagens_adicionais = [$produto['imagem']];
} else {
    if (!in_array($produto['imagem'], $imagens_adicionais)) {
        array_unshift($imagens_adicionais, $produto['imagem']);
    }
}

$mensagem = urlencode("Olá! Tenho interesse no produto: " . $produto['nome'] . " (ID " . $produto['id'] . ")");
$link_whatsapp = "https://wa.me/SEU_NUMERO_AQUI?text=$mensagem";

$categoria = $produto['categoria'];
$relacionados = $conn->query("SELECT * FROM produtos WHERE categoria = '$categoria' AND id != $id ORDER BY RAND() LIMIT 3");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($produto['nome']) ?> | Boutique</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #b86f87;
            --primary-light: #d4a5b8;
            --secondary: #f8bbd9;
            --accent: #ff6b9d;
            --soft-pink: #fce4ec;
            --white: #ffffff;
            --black: #1a1a1a;
            --gray: #666666;
            --light-gray: #f5f5f5;
            --border: #e0e0e0;
            --shadow: 0 8px 32px rgba(0,0,0,0.1);
            --shadow-light: 0 4px 16px rgba(0,0,0,0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--black);
            background: var(--white);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: var(--white);
            box-shadow: var(--shadow-light);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        /* Breadcrumb */
        .breadcrumb {
            padding: 2rem 0 1rem;
            font-size: 0.9rem;
            color: var(--gray);
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }

        /* Product Section */
        .product {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-bottom: 4rem;
        }

        /* Gallery */
        .gallery {
            position: relative;
        }

        .main-image {
            width: 100%;
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }

        .main-image:hover img {
            transform: scale(1.05);
        }

        .thumbnails {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
            flex-shrink: 0;
        }

        .thumbnail.active {
            border-color: var(--primary);
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Product Info */
        .product-info {
            padding: 2rem 0;
        }

        .category-tag {
            display: inline-block;
            background: var(--soft-pink);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .product-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        /* Description Section */
        .description-section {
            margin-bottom: 2rem;
        }

        .description {
            color: var(--gray);
            font-size: 1rem;
            line-height: 1.7;
            text-align: justify;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .description.collapsed {
            max-height: 80px;
            position: relative;
        }

        .description.collapsed::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 30px;
            background: linear-gradient(transparent, var(--white));
        }

        .description-toggle {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s ease;
        }

        .description-toggle:hover {
            color: var(--primary-light);
        }

        .description-toggle i {
            transition: transform 0.3s ease;
        }

        .description-toggle.expanded i {
            transform: rotate(180deg);
        }

        .price-section {
            background: linear-gradient(135deg, var(--soft-pink), var(--secondary));
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
        }

        .price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .installments {
            color: var(--gray);
            font-size: 0.95rem;
        }

        .whatsapp-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            width: 100%;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .whatsapp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }

        .product-features {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--gray);
        }

        .feature i {
            color: var(--primary);
            width: 20px;
        }

        .guarantee {
            background: var(--light-gray);
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid var(--primary);
        }

        .guarantee h4 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .guarantee p {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Related Products */
        .related-section {
            margin-top: 4rem;
            padding: 3rem 0;
            background: var(--light-gray);
        }

        .related-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: var(--black);
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-content {
            padding: 1.5rem;
        }

        .card-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--black);
        }

        .card-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .card-btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
            text-align: center;
            width: 100%;
        }

        .card-btn:hover {
            background: var(--primary-light);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            position: relative;
            margin: 5% auto;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow: auto;
        }

        .modal img {
            width: 100%;
            height: auto;
            border-radius: 12px;
        }

        .close {
            position: absolute;
            top: 1rem;
            right: 2rem;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            z-index: 1001;
            background: rgba(0,0,0,0.5);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close:hover {
            background: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .product {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .product-title {
                font-size: 2rem;
            }

            .price {
                font-size: 1.6rem;
            }

            .main-image {
                height: 400px;
            }

            .related-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 15px;
            }

            .product-title {
                font-size: 1.5rem;
            }

            .price {
                font-size: 1.4rem;
            }

            .related-grid {
                grid-template-columns: 1fr;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="" class="logo"></a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="breadcrumb">
            <a href="loja.php">Início</a> / <a href="loja.php">Produtos</a> / <?= htmlspecialchars($produto['nome']) ?>
        </div>

        <div class="product">
            <div class="gallery">
                <div class="main-image">
                    <img id="mainImage" src="<?= htmlspecialchars($imagens_adicionais[0]) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" onclick="openModal(this.src)">
                </div>
                
                <?php if (count($imagens_adicionais) > 1): ?>
                <div class="thumbnails">
                    <?php foreach ($imagens_adicionais as $index => $imagem): ?>
                        <div class="thumbnail <?= $index === 0 ? 'active' : '' ?>" onclick="changeImage('<?= htmlspecialchars($imagem) ?>', this)">
                            <img src="<?= htmlspecialchars($imagem) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <div class="category-tag">
                    <?= htmlspecialchars($produto['categoria'] ?? 'Produto') ?>
                </div>

                <h1 class="product-title"><?= htmlspecialchars($produto['nome']) ?></h1>

                <div class="description-section">
                    <div class="description collapsed" id="productDescription">
                        <?= nl2br(htmlspecialchars($produto['descricao'])) ?>
                    </div>
                    <button class="description-toggle" id="toggleDescription" onclick="toggleDescription()">
                        <span>Ver mais</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>

                <div class="price-section">
                    <div class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                    <div class="installments">Parcelamento disponível via WhatsApp</div>
                </div>

                <a href="<?= $link_whatsapp ?>" target="_blank" class="whatsapp-btn">
                    <i class="fab fa-whatsapp"></i>
                    Quero Encomendar
                </a>

                <div class="product-features">
                    <div class="feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Sob encomenda</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Qualidade garantida</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-headset"></i>
                        <span>Atendimento personalizado</span>
                    </div>
                </div>

                <div class="guarantee">
                    <h4>Garantia de Qualidade</h4>
                    <p>Todos os nossos produtos são cuidadosamente selecionados e passam por rigoroso controle de qualidade.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="related-section">
        <div class="container">
            <h2 class="related-title">Você também pode gostar</h2>
            <div class="related-grid">
                <?php if ($relacionados && $relacionados->num_rows > 0): ?>
                    <?php while ($rel = $relacionados->fetch_assoc()): ?>
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($rel['imagem']) ?>" alt="<?= htmlspecialchars($rel['nome']) ?>">
                            <div class="card-content">
                                <h3 class="card-title"><?= htmlspecialchars($rel['nome']) ?></h3>
                                <div class="card-price">R$ <?= number_format($rel['preco'], 2, ',', '.') ?></div>
                                <a href="produto.php?id=<?= $rel['id'] ?>" class="card-btn">Ver Produto</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="imageModal" class="modal" onclick="closeModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="modalImage" src="" alt="">
        </div>
    </div>

    <script>
        function changeImage(newImage, element) {
            document.getElementById('mainImage').src = newImage;
            
            // Remove active class from all thumbnails
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            
            // Add active class to clicked thumbnail
            element.classList.add('active');
        }

        function openModal(imageSrc) {
            document.getElementById('imageModal').style.display = 'block';
            document.getElementById('modalImage').src = imageSrc;
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function toggleDescription() {
            const description = document.getElementById('productDescription');
            const toggle = document.getElementById('toggleDescription');
            const span = toggle.querySelector('span');
            const icon = toggle.querySelector('i');
            
            if (description.classList.contains('collapsed')) {
                description.classList.remove('collapsed');
                span.textContent = 'Ver menos';
                toggle.classList.add('expanded');
            } else {
                description.classList.add('collapsed');
                span.textContent = 'Ver mais';
                toggle.classList.remove('expanded');
            }
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>