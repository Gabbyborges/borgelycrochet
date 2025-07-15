<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Borgely Crochet - Artesanato com Amor</title>
  <meta name="description" content="Peças únicas de crochê feitas com amor e dedicação. Buquês, amigurumis, roupas e acessórios artesanais por Gabriely.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Quicksand:wght@300;400;500;600&family=La+Belle+Aurore&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css?v=1" />
</head>
<body>

<!-- Newsletter Card Modal -->
<div class="newsletter-modal" id="newsletterModal">
  <div class="newsletter-overlay" id="newsletterOverlay"></div>
  <div class="newsletter-card">
    <button class="newsletter-close" id="closeNewsletterBtn" aria-label="Fechar">×</button>
    
    <div class="newsletter-content">
      <div class="newsletter-header">
        <div class="newsletter-icon">💌</div>
        <h3 class="newsletter-title">Fique por dentro das novidades!</h3>
        <p class="newsletter-subtitle">Receba em primeira mão nossas promoções, lançamentos e conteúdos exclusivos</p>
      </div>
      
      <div class="newsletter-benefits">
        <div class="benefit-item">
          <span class="benefit-icon">🎁</span>
          <span>Promoções exclusivas</span>
        </div>
        <div class="benefit-item">
          <span class="benefit-icon">🆕</span>
          <span>Novidades em primeira mão</span>
        </div>
        <div class="benefit-item">
          <span class="benefit-icon">📚</span>
          <span>Dicas e tutoriais</span>
        </div>
      </div>
      
      <div class="newsletter-actions">
        <a href="cliente/login_cliente.php" class="newsletter-btn primary">
          <span>✨ Quero me cadastrar</span>
        </a>
      </div>
      
      <p class="newsletter-disclaimer">
        Ao se cadastrar, você concorda em receber e-mails promocionais. Você pode cancelar a qualquer momento.
      </p>
    </div>
  </div>
</div>

<!-- Mobile Overlay -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- Header -->
<header class="header">
  <div class="header-container">
    <!-- Logo -->
    <div class="logo">
      <img src="img/logo.png?v=<?php echo time(); ?>" alt="Borgely Crochet - Artesanato com Amor" />
    </div>

    <!-- Desktop Navigation -->
    <nav class="desktop-nav">
      <ul class="nav-menu">
        <li><a href="loja.php" class="nav-link">🛍️ Loja</a></li>
        <li><a href="#sobre" class="nav-link">💝 Sobre</a></li>
        <li><a href="#contato" class="nav-link">📞 Contato</a></li>
        <li><a href="blog.php" class="nav-link">📝 Blog</a></li>
        <li><a href="#depoimentos" class="nav-link">💬 Depoimentos</a></li>
        
        <?php if (isset($_SESSION['cliente_id'])): ?>
          <li><a href="cliente/painel_cliente.php" class="nav-link user-link">👤 Meu Painel</a></li>
          <li><a href="cliente/logout.php" class="nav-link logout-link">🚪 Sair</a></li>
        <?php else: ?>
          <li><a href="cliente/login_cliente.php" class="nav-link login-link">🔐 Entrar</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Abrir menu">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>
  </div>

  <!-- Mobile Navigation -->
  <nav class="mobile-nav" id="mobileNav">
    <div class="mobile-nav-header">
      <h3>🌸 Menu</h3>
      <button class="close-mobile-menu" id="closeMobileMenu" aria-label="Fechar menu">×</button>
    </div>
    <ul class="mobile-menu">
      <li><a href="loja.php" class="mobile-link">🛍️ Loja</a></li>
      <li><a href="#sobre" class="mobile-link">💝 Sobre Mim</a></li>
      <li><a href="#contato" class="mobile-link">📞 Contato</a></li>
      <li><a href="blog.php" class="mobile-link">📝 Blog</a></li>
      <li><a href="#depoimentos" class="mobile-link">💬 Depoimentos</a></li>
      
      <?php if (isset($_SESSION['cliente_id'])): ?>
        <li><a href="cliente/painel_cliente.php" class="mobile-link user-link">👤 Meu Painel</a></li>
        <li><a href="cliente/logout.php" class="mobile-link logout-link">🚪 Sair</a></li>
      <?php else: ?>
        <li><a href="cliente/login_cliente.php" class="mobile-link login-link">🔐 Entrar</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<section class="hero-banner">
  <div class="banner-container">
    <img src="img/banner.png" alt="Borgely Crochet - Peças Artesanais Únicas" class="banner-image">
    <div class="banner-content">
      <a href="loja.php" class="cta-button">
        <span>🛍️ Ver Coleção</span>
        <span class="button-arrow">→</span>
      </a>
    </div>
  </div>
</section>


    
</section>

<!-- About Section -->
<section class="about-section" id="sobre">
  <div class="container">
    <div class="about-content">
      <div class="about-image">
        <img src="img/sobreeuu.webp" alt="Gabriely - Artesã da Borgely Crochet" loading="lazy">
        <div class="image-decoration"></div>
      </div>
      
      <div class="about-text">
        <div class="about-header">
          <h2 class="about-title">Criação Borgely 🌸</h2>
          <div class="title-decoration"></div>
        </div>
        
        <div class="about-story">
          <p class="intro-text">Oi! Eu sou a <strong>Gabriely</strong>, tenho 20 anos… e o crochê mora no meu coração desde muito novinha. ✨</p>
          
          <p>Aprendi a crochetar aos 10 anos, fazendo tapetinhos com curiosidade e carinho — e mal sabia eu, naquela época, o quanto isso se tornaria parte de mim.</p>
          
          <p>Depois de uma pausa, voltei aos poucos… e quando criei meus primeiros souplats, algo despertou de novo dentro de mim. Comecei a enxergar o crochê com outros olhos. Vi que ele podia ir muito além dos tapetes simples — e foi aí que essa paixão floresceu de verdade.</p>
          
          <p>Vieram as bolsas, os amigurumis, os buquês, as roupinhas… um universo que me encantou completamente. Mas sabe de uma coisa? Acima de tudo, eu sei que esse dom não veio de mim. Ele veio de <strong>Deus</strong>. Foi Ele quem me presenteou com esse talento, e é Ele quem me guia em cada pontinho que faço.</p>
          
          <p class="final-text">Hoje, através da <strong>Borgely Crochet</strong>, quero compartilhar um pedacinho desse dom com você. Mais do que peças artesanais, quero entregar carinho, cuidado e aquele aconchego que só algo feito à mão pode ter — exclusivo para você. 💖✨</p>
          
          <blockquote class="bible-verse">
            <p>"Consagre ao Senhor tudo o que você faz, e os seus planos serão bem-sucedidos."</p>
            <cite>Provérbios 16:3</cite>
          </blockquote>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section" id="depoimentos">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">O que estão dizendo 💬</h2>
      <p class="section-subtitle">Depoimentos reais de clientes apaixonadas</p>
    </div>
    
    <div class="testimonials-carousel">
      <button class="carousel-btn prev-btn" id="prevBtn" aria-label="Depoimento anterior">
        <span>‹</span>
      </button>
      
      <div class="testimonials-container" id="testimonialsContainer">
        <div class="testimonial-card active">
          <div class="testimonial-content">
            <div class="stars">⭐⭐⭐⭐⭐</div>
            <p class="testimonial-text">"As peças são lindas e dá pra sentir o carinho em cada detalhe! Comprei um amigurumi e minha filha não larga mais."</p>
            <div class="testimonial-author">
              <strong>— Ana Luiza</strong>
              <span class="author-location">São Paulo, SP</span>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-content">
            <div class="stars">⭐⭐⭐⭐⭐</div>
            <p class="testimonial-text">"Recebi meu buquê e fiquei emocionada. Trabalho impecável, superou todas as minhas expectativas!"</p>
            <div class="testimonial-author">
              <strong>— Júlia M.</strong>
              <span class="author-location">Rio de Janeiro, RJ</span>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-content">
            <div class="stars">⭐⭐⭐⭐⭐</div>
            <p class="testimonial-text">"Comprei uma bolsinha e foi amor à primeira vista. Que capricho! Já virei cliente fiel."</p>
            <div class="testimonial-author">
              <strong>— Camila Rocha</strong>
              <span class="author-location">Belo Horizonte, MG</span>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-content">
            <div class="stars">⭐⭐⭐⭐⭐</div>
            <p class="testimonial-text">"É tudo tão bem feito que dá vontade de comprar a loja inteira! Recomendo de olhos fechados."</p>
            <div class="testimonial-author">
              <strong>— Bruna T.</strong>
              <span class="author-location">Curitiba, PR</span>
            </div>
          </div>
        </div>
      </div>
      
      <button class="carousel-btn next-btn" id="nextBtn" aria-label="Próximo depoimento">
        <span>›</span>
      </button>
    </div>
    
    <div class="carousel-dots" id="carouselDots">
      <button class="dot active" data-slide="0"></button>
      <button class="dot" data-slide="1"></button>
      <button class="dot" data-slide="2"></button>
      <button class="dot" data-slide="3"></button>
    </div>
  </div>
</section>

<!-- Contact Section -->
<section class="contact-section" id="contato">
  <div class="container">
    <div class="contact-content">
      <div class="contact-info">
        <h2 class="contact-title">Vamos Conversar? 💕</h2>
        <p class="contact-subtitle">Entre em contato e vamos criar algo especial juntas!</p>
        
        <div class="contact-methods">
          <a href="https://wa.me/5511999999999" class="contact-method whatsapp" target="_blank">
            <span class="contact-icon">📱</span>
            <div class="contact-details">
              <strong>WhatsApp</strong>
              <span>Resposta rápida</span>
            </div>
          </a>
          
          <a href="https://instagram.com/borgelycrochet" class="contact-method instagram" target="_blank">
            <span class="contact-icon">📸</span>
            <div class="contact-details">
              <strong>Instagram</strong>
              <span>@borgelycrochet</span>
            </div>
          </a>
        </div>
      </div>
      
      <div class="contact-image">

      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-brand">
        <img src="img/logo.png" alt="Borgely Crochet" class="footer-logo">
        <p>Feito com amor e dedicação ✨</p>
      </div>
      
      <div class="footer-links">
        <div class="footer-column">
          <h4>Navegação</h4>
          <ul>
            <li><a href="loja.php">Loja</a></li>
            <li><a href="#sobre">Sobre</a></li>
            <li><a href="blog.php">Blog</a></li>
            <li><a href="#contato">Contato</a></li>
          </ul>
        </div>
        
        <div class="footer-column">
          <h4>Redes Sociais</h4>
          <ul>
            <li><a href="https://www.instagram.com/borgely.crochet/" target="_blank">Instagram</a></li>
            <li><a href="https://wa.me/5516991678560" target="_blank">WhatsApp</a></li>
          </ul>
        </div>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; 2025 Borgely Crochet | Todos os direitos reservados</p>
      <p class="dev-credit">Desenvolvido por Gabriely Borges</p>
    </div>
  </div>
</footer>

<!-- Admin Access (Hidden) -->
<a href="admin/login_admin.php" 
   class="admin-link" 
   title="Área Admin"
   aria-label="Acesso administrativo">
  🔒
</a>
<!-- Substitua a linha do script por esta: -->
<script src="script.js?v=<?php echo time(); ?>" defer></script>
</body>
</html>