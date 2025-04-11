// main.js - Funcionalidades principales del sitio

document.addEventListener('DOMContentLoaded', function() {
    // 1. Menú móvil (responsive)
    setupMobileMenu();

    // 2. Carrusel de productos destacados
    initFeaturedCarousel();

    // 3. Funcionalidad del carrito
    setupCart();

    // 4. Galería de imágenes en página de producto
    initProductGallery();

    // 5. Validación de formularios
    setupFormValidation();
});

// ==================== FUNCIONES ====================

// 1. Menú móvil
function setupMobileMenu() {
    const menuToggle = document.createElement('div');
    menuToggle.className = 'mobile-menu-toggle';
    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';

    const header = document.querySelector('.main-header .container');
    if (header) {
        header.prepend(menuToggle);

        menuToggle.addEventListener('click', function() {
            const nav = document.querySelector('.main-nav');
            nav.classList.toggle('active');
            this.classList.toggle('open');
        });
    }
}

// 2. Carrusel de productos
function initFeaturedCarousel() {
    const carousel = document.querySelector('.products-grid');
    if (carousel && window.innerWidth < 768) {
        let isDown = false;
        let startX;
        let scrollLeft;

        carousel.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
            carousel.style.cursor = 'grabbing';
        });

        carousel.addEventListener('mouseleave', () => {
            isDown = false;
            carousel.style.cursor = 'grab';
        });

        carousel.addEventListener('mouseup', () => {
            isDown = false;
            carousel.style.cursor = 'grab';
        });

        carousel.addEventListener('mousemove', (e) => {
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - carousel.offsetLeft;
            const walk = (x - startX) * 2;
            carousel.scrollLeft = scrollLeft - walk;
        });
    }
}

// 3. Funcionalidades del carrito
function setupCart() {
    // Actualizar cantidad en el carrito
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value < 1) this.value = 1;
        });
    });

    // Animación al añadir al carrito
    const addToCartButtons = document.querySelectorAll('.add-to-cart button');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Solo animar si el formulario es válido
            if (this.closest('form').checkValidity()) {
                e.preventDefault();
                this.innerHTML = '<i class="fas fa-check"></i> Añadido';
                setTimeout(() => {
                    this.closest('form').submit();
                }, 1000);
            }
        });
    });
}

// 4. Galería de productos
function initProductGallery() {
    const mainImage = document.querySelector('.product-images .main-image img');
    const thumbnails = document.querySelectorAll('.product-thumbnails img');

    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                mainImage.src = this.src;
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
}

// 5. Validación de formularios
function setupFormValidation() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = 'red';
                    isValid = false;
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Por favor complete todos los campos requeridos');
            }
        });
    });
}

// 6. Efecto scroll suave
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Manejar cierre de pestaña/navegador
window.addEventListener('beforeunload', function(e) {
    // Solo si el usuario está logueado
    if (document.cookie.indexOf('PHPSESSID') !== -1) {
        // Opcional: Hacer una llamada AJAX para cerrar sesión
        // fetch('<?php echo SITE_URL; ?>/user/logout-silent.php');
    }
});

// Verificar sesión al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si hay mensaje de sesión expirada
    if (window.location.search.includes('session_expired=1')) {
        alert('Tu sesión ha expirado. Por favor inicia sesión nuevamente.');
    }
});