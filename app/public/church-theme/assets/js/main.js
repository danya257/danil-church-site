/**
 * Church Theme Main JavaScript
 * Современный JS для православного храма
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Church Theme Loaded');
    
    // ===== АНИМАЦИЯ ПРИ СКРОЛЛЕ =====
    const scrollRevealElements = document.querySelectorAll('.scroll-reveal');
    
    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;
        const elementVisible = 150;
        
        scrollRevealElements.forEach((element) => {
            const elementTop = element.getBoundingClientRect().top;
            
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('active');
            }
        });
    };
    
    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll(); // Проверяем при загрузке
    
    // ===== ПЛАВНЫЙ СКРОЛЛ К ЯКОРЯМ =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // ===== МОБИЛЬНОЕ МЕНЮ =====
    const navToggle = document.querySelector('.nav-toggle');
    const mainNav = document.querySelector('.main-navigation');
    
    if (navToggle && mainNav) {
        navToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    
    // ===== ЗАКРЫТИЕ МЕНЮ ПРИ КЛИКЕ ВНЕ =====
    document.addEventListener('click', function(event) {
        if (mainNav && !mainNav.contains(event.target) && !navToggle.contains(event.target)) {
            mainNav.classList.remove('active');
            if (navToggle) navToggle.classList.remove('active');
        }
    });
    
    // ===== ПАРАЛЛАКС ЭФФЕКТ ДЛЯ HERO =====
    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const heroContent = heroSection.querySelector('.hero-content');
            if (heroContent) {
                heroContent.style.transform = `translateY(${scrolled * 0.3}px)`;
                heroContent.style.opacity = 1 - (scrolled / 600);
            }
        });
    }
    
    // ===== ФОРМА ОБРАТНОЙ СВЯЗИ =====
    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Здесь будет AJAX отправка
            alert('Спасибо! Ваше сообщение отправлено.');
            contactForm.reset();
        });
    }
    
    // ===== СЧЕТЧИК ДНЕЙ ДО ПРАЗДНИКА =====
    const countdownElement = document.querySelector('.countdown');
    if (countdownElement) {
        const targetDate = new Date(countdownElement.dataset.date);
        
        const updateCountdown = () => {
            const now = new Date();
            const diff = targetDate - now;
            
            if (diff > 0) {
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                
                countdownElement.innerHTML = `До праздника: ${days} дн. ${hours} ч.`;
            }
        };
        
        setInterval(updateCountdown, 1000);
        updateCountdown();
    }
});

// ===== VIDEO INTRO (если есть) =====
window.addEventListener('load', function() {
    const videoIntro = document.querySelector('.video-intro');
    const videoIntroOverlay = document.querySelector('.video-intro-overlay');
    
    if (videoIntro && videoIntroOverlay) {
        setTimeout(() => {
            videoIntro.classList.add('loaded');
            setTimeout(() => {
                videoIntroOverlay.classList.add('hidden');
                document.body.classList.remove('video-intro-active');
            }, 2000);
        }, 500);
    }
});
