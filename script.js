// Aguardar o DOM estar pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM carregado, inicializando funcionalidades...');
    
    // Inicializar todas as funcionalidades
    initMobileMenu();
    initTestimonialsCarousel();
    initSmoothScrolling();
    initAnimations();
    initKeyboardHandlers();
    initImageLoading();
    initSignupModal();
    
    console.log('✅ Todas as funcionalidades inicializadas!');
});

// ============= MENU MOBILE =============
function initMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const closeMobileMenu = document.getElementById('closeMobileMenu');
    const mobileNav = document.getElementById('mobileNav');
    const mobileOverlay = document.getElementById('mobileOverlay');

    // Verificar se os elementos existem
    if (!mobileMenuToggle || !closeMobileMenu || !mobileNav || !mobileOverlay) {
        console.warn('⚠️ Alguns elementos do menu mobile não foram encontrados');
        return;
    }

    function openMobileMenu() {
        console.log('📱 Abrindo menu mobile');
        mobileNav.classList.add('active');
        mobileOverlay.classList.add('active');
        document.body.classList.add('menu-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
    }

    function closeMobileMenuFunc() {
        console.log('📱 Fechando menu mobile');
        mobileNav.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.classList.remove('menu-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
    }

    // Event listeners
    mobileMenuToggle.addEventListener('click', openMobileMenu);
    closeMobileMenu.addEventListener('click', closeMobileMenuFunc);
    mobileOverlay.addEventListener('click', closeMobileMenuFunc);

    // Fechar menu ao clicar nos links
    document.querySelectorAll('.mobile-link').forEach(link => {
        link.addEventListener('click', closeMobileMenuFunc);
    });

    // Fechar menu no redimensionamento da tela
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && mobileNav.classList.contains('active')) {
            closeMobileMenuFunc();
        }
    });

    console.log('✅ Menu mobile inicializado');
}

// ============= CARROSSEL DE DEPOIMENTOS =============
function initTestimonialsCarousel() {
    const testimonialsContainer = document.getElementById('testimonialsContainer');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const dots = document.querySelectorAll('.dot');
    const testimonialCards = document.querySelectorAll('.testimonial-card');

    // Verificar se os elementos existem
    if (!testimonialsContainer || !prevBtn || !nextBtn) {
        console.warn('⚠️ Elementos do carrossel de depoimentos não encontrados');
        return;
    }

    if (testimonialCards.length === 0) {
        console.warn('⚠️ Nenhum card de depoimento encontrado');
        return;
    }

    let currentSlide = 0;
    const totalSlides = testimonialCards.length;
    let autoPlayInterval;

    console.log(`🎠 Carrossel encontrado com ${totalSlides} slides`);

    function showSlide(index) {
        console.log(`🎠 Mudando para slide ${index}`);
        
        // Validar índice
        if (index < 0 || index >= totalSlides) {
            console.error('❌ Índice de slide inválido:', index);
            return;
        }

        // Remove active de todos os slides e dots
        testimonialCards.forEach(card => {
            card.classList.remove('active');
        });
        
        if (dots.length > 0) {
            dots.forEach(dot => dot.classList.remove('active'));
        }
        
        // Adiciona active ao slide atual
        testimonialCards[index].classList.add('active');
        
        if (dots[index]) {
            dots[index].classList.add('active');
        }
        
        currentSlide = index;
        console.log(`✅ Slide ${index} ativado`);
    }

    function nextSlide() {
        const next = currentSlide === totalSlides - 1 ? 0 : currentSlide + 1;
        showSlide(next);
    }

    function prevSlide() {
        const prev = currentSlide === 0 ? totalSlides - 1 : currentSlide - 1;
        showSlide(prev);
    }

    // Event listeners dos botões
    nextBtn.addEventListener('click', function() {
        console.log('▶️ Botão próximo clicado');
        nextSlide();
    });

    prevBtn.addEventListener('click', function() {
        console.log('◀️ Botão anterior clicado');
        prevSlide();
    });

    // Event listeners dos dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function() {
            console.log(`🔘 Dot ${index} clicado`);
            showSlide(index);
        });
    });

    // Auto-play do carrossel
    autoPlayInterval = setInterval(function() {
        nextSlide();
    }, 5000);

    // Pausar auto-play quando hover nos controles
    const carouselControls = [prevBtn, nextBtn];
    carouselControls.forEach(control => {
        control.addEventListener('mouseenter', () => {
            clearInterval(autoPlayInterval);
            console.log('⏸️ Auto-play pausado');
        });
        
        control.addEventListener('mouseleave', () => {
            autoPlayInterval = setInterval(function() {
                nextSlide();
            }, 5000);
            console.log('▶️ Auto-play retomado');
        });
    });

    console.log('✅ Carrossel de depoimentos inicializado');
}

// ============= SCROLL SUAVE =============
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (target) {
                console.log(`🔗 Navegando para: ${targetId}`);
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                console.warn(`⚠️ Elemento não encontrado: ${targetId}`);
            }
        });
    });

    console.log('✅ Scroll suave inicializado');
}

// ============= ANIMAÇÕES =============
function initAnimations() {
    // Intersection Observer para animações
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                console.log('🎭 Elemento animado:', entry.target.className);
            }
        });
    }, observerOptions);

    // Observar elementos para animação
    const elementsToAnimate = document.querySelectorAll('.product-card, .about-content, .testimonial-card');
    elementsToAnimate.forEach(el => {
        observer.observe(el);
    });

    console.log(`✅ Observer de animações inicializado para ${elementsToAnimate.length} elementos`);
}

// ============= TECLADO =============
function initKeyboardHandlers() {
    document.addEventListener('keydown', function(e) {
        const mobileNav = document.getElementById('mobileNav');
        const newsletterModal = document.getElementById('newsletterModal');
        
        if (e.key === 'Escape') {
            // Fechar menu mobile
            if (mobileNav && mobileNav.classList.contains('active')) {
                console.log('⌨️ ESC pressionado - fechando menu mobile');
                const closeMobileMenu = document.getElementById('closeMobileMenu');
                if (closeMobileMenu) {
                    closeMobileMenu.click();
                }
            }
            
            // Fechar modal de newsletter
            if (newsletterModal && newsletterModal.classList.contains('show')) {
                console.log('⌨️ ESC pressionado - fechando modal de newsletter');
                hideNewsletterModal();
            }
        }
    });

    console.log('✅ Handlers de teclado inicializados');
}

// ============= CARREGAMENTO DE IMAGENS =============
function initImageLoading() {
    document.querySelectorAll('img').forEach(img => {
        // Se a imagem já carregou
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            // Aguardar o carregamento
            img.addEventListener('load', function() {
                this.classList.add('loaded');
                console.log('🖼️ Imagem carregada:', this.src);
            });
            
            img.addEventListener('error', function() {
                console.error('❌ Erro ao carregar imagem:', this.src);
            });
        }
    });

    console.log('✅ Sistema de carregamento de imagens inicializado');
}

// ============= MODAL DE NEWSLETTER =============
function initSignupModal() {
    const newsletterModal = document.getElementById('newsletterModal');
    const closeNewsletterBtn = document.getElementById('closeNewsletterBtn');
    const closeNewsletterSecondary = document.getElementById('closeNewsletterSecondary');
    const newsletterOverlay = document.getElementById('newsletterOverlay');
    
    // Verificar se os elementos existem
    if (!newsletterModal || !closeNewsletterBtn || !closeNewsletterSecondary || !newsletterOverlay) {
        console.warn('⚠️ Alguns elementos do modal de newsletter não foram encontrados');
        return;
    }
    
    // Verificar se o usuário já se cadastrou (existe uma flag no sessionStorage)
    const isUserRegistered = sessionStorage.getItem('userRegistered');
    
    // Se o usuário não tiver se cadastrado, exibe o modal
    if (!isUserRegistered) {
        // Exibe o modal após 3 segundos
        setTimeout(() => {
            showNewsletterModal();
        }, 3000);  // 3 segundos após o carregamento da página
    }
    
    // Função para mostrar o modal
    function showNewsletterModal() {
        newsletterModal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Marca como exibido para evitar mostrar novamente apenas no caso do cadastro
        sessionStorage.removeItem('userRegistered');
        
        // Adiciona animação de entrada
        setTimeout(() => {
            const modalCard = newsletterModal.querySelector('.newsletter-card');
            if (modalCard) {
                modalCard.style.transform = 'translate(-50%, -50%) scale(1)';
            }
        }, 100);
        
        console.log('📩 Modal de newsletter exibido');
    }
    
    // Função para esconder o modal
    function hideNewsletterModal() {
        newsletterModal.classList.remove('show');
        document.body.style.overflow = '';
        
        // Reseta a animação
        setTimeout(() => {
            const modalCard = newsletterModal.querySelector('.newsletter-card');
            if (modalCard) {
                modalCard.style.transform = 'translate(-50%, -50%) scale(0.9)';
            }
        }, 300);
        
        console.log('📩 Modal de newsletter fechado');
    }
    
    // Funções de fechamento do modal
    closeNewsletterBtn.addEventListener('click', hideNewsletterModal);
    closeNewsletterSecondary.addEventListener('click', hideNewsletterModal);
    
    // Fechar o modal ao clicar na área de sobreposição
    newsletterOverlay.addEventListener('click', hideNewsletterModal);
    
    // Impedir o fechamento do modal ao clicar dentro do card
    const modalCard = newsletterModal.querySelector('.newsletter-card');
    if (modalCard) {
        modalCard.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    console.log('✅ Modal de newsletter inicializado');
}

// Iniciar o modal de newsletter
initSignupModal();


// ============= FUNÇÕES DE DEBUG =============
window.debugBorgely = {
    testCarousel: function() {
        console.log('🧪 Testando carrossel...');
        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) {
            nextBtn.click();
            console.log('✅ Teste do carrossel executado');
        } else {
            console.error('❌ Botão do carrossel não encontrado');
        }
    },
    
    showElements: function() {
        const elements = [
            'mobileMenuToggle',
            'mobileNav', 
            'testimonialsContainer',
            'prevBtn',
            'nextBtn',
            'newsletterModal',
            'closeNewsletterBtn'
        ];
        
        console.log('🔍 Verificando elementos:');
        elements.forEach(id => {
            const el = document.getElementById(id);
            console.log(`${id}:`, el ? '✅ Encontrado' : '❌ Não encontrado');
        });
    },
    
    openModal: function() {
        const modal = document.getElementById('newsletterModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            console.log('✅ Modal aberto via debug');
        } else {
            console.error('❌ Modal não encontrado');
        }
    },
    
    closeModal: function() {
        if (window.hideNewsletterModal) {
            window.hideNewsletterModal();
            console.log('✅ Modal fechado via debug');
        } else {
            console.error('❌ Função de fechar modal não encontrada');
        }
    },
    
    info: function() {
        console.log('ℹ️ Borgely Crochet - Sistema JavaScript');
        console.log('📱 Menu mobile:', document.getElementById('mobileNav') ? 'OK' : 'ERRO');
        console.log('🎠 Carrossel:', document.getElementById('testimonialsContainer') ? 'OK' : 'ERRO');
        console.log('🔘 Dots:', document.querySelectorAll('.dot').length, 'encontrados');
        console.log('💬 Cards:', document.querySelectorAll('.testimonial-card').length, 'encontrados');
        console.log('📩 Modal:', document.getElementById('newsletterModal') ? 'OK' : 'ERRO');
    }
};

// Disponibilizar funções de debug globalmente
console.log('🛠️ Use debugBorgely.info() para informações do sistema');
console.log('🛠️ Use debugBorgely.testCarousel() para testar o carrossel');
console.log('🛠️ Use debugBorgely.showElements() para verificar elementos');
console.log('🛠️ Use debugBorgely.openModal() para testar o modal');
console.log('🛠️ Use debugBorgely.closeModal() para fechar o modal');