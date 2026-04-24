/**
 * Church Theme - Main JavaScript
 * Исправленная версия - без дубликатов и конфликтов
 */

document.addEventListener('DOMContentLoaded', function() {
    
    'use strict';
    
    // ===== ПЕРЕМЕННЫЕ =====
    const body = document.body;
    const header = document.querySelector('.site-header');
    const mobileToggle = document.querySelector('.mobile-toggle');
    const mobileOverlay = document.querySelector('.mobile-menu-overlay');
    const mobileClose = document.querySelector('.mobile-menu-close');
    const menuItemsWithChildren = document.querySelectorAll('.menu-item-has-children');
    const scrollRevealElements = document.querySelectorAll('.scroll-reveal');
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    // ===== VIDEO INTRO (ТОЛЬКО ГЛАВНАЯ СТРАНИЦА) =====
    const videoIntro = document.getElementById('videoIntro');
    const introVideo = document.getElementById('introVideo');
    const videoLogo = document.querySelector('.video-intro-logo');
    
    function initVideoIntro() {
        if (!videoIntro || !introVideo) {
            body.classList.add('loaded');
            return;
        }
        
        // Блокируем скролл во время видео
        body.style.overflow = 'hidden';
        body.classList.add('video-intro-active');
        
        const finishIntro = () => {
            if (!videoIntro.classList.contains('hidden')) {
                // Скрываем логотип
                if (videoLogo) {
                    videoLogo.classList.remove('visible');
                }
                // Скрываем видео
                if (introVideo) {
                    introVideo.style.opacity = '0';
                }
                // Скрываем оверлей
                videoIntro.classList.add('hidden');
                
                // Возвращаем скролл
                setTimeout(() => {
                    body.style.overflow = '';
                    body.classList.remove('video-intro-active');
                    body.classList.add('loaded');
                    
                    // Удаляем видео из DOM
                    setTimeout(() => {
                        if (videoIntro && videoIntro.parentNode) {
                            videoIntro.remove();
                        }
                    }, 2000);
                }, 2000);
            }
        };
        
        // Когда видео загрузилось
        introVideo.addEventListener('loadeddata', () => {
            videoIntro.classList.add('visible');
            setTimeout(() => {
                introVideo.classList.add('loaded');
                if (videoLogo) {
                    setTimeout(() => {
                        videoLogo.classList.add('visible');
                    }, 300);
                }
            }, 300);
            
            introVideo.play().catch(error => {
                console.log('Autoplay prevented:', error);
                finishIntro();
            });
        });
        
        // Когда видео заканчивается
        introVideo.addEventListener('ended', finishIntro);
        
        // Таймаут безопасности (8 секунд)
        setTimeout(() => {
            finishIntro();
        }, 8000);
        
        // Обработка ошибок
        introVideo.addEventListener('error', () => {
            console.log('Video failed to load');
            body.style.overflow = '';
            finishIntro();
        });
        
        // Если видео уже в кэше
        if (introVideo.readyState >= 3) {
            videoIntro.classList.add('visible');
            setTimeout(() => {
                introVideo.classList.add('loaded');
                if (videoLogo) {
                    setTimeout(() => {
                        videoLogo.classList.add('visible');
                    }, 300);
                }
            }, 300);
            introVideo.play().catch(() => {});
        }
    }
    
    // ===== МОБИЛЬНОЕ МЕНЮ =====
    function initMobileMenu() {
        if (!mobileToggle || !mobileOverlay) return;
        
        function toggleMobileMenu() {
            const isActive = mobileOverlay.classList.toggle('active');
            if (mobileToggle) {
                mobileToggle.classList.toggle('active', isActive);
                mobileToggle.setAttribute('aria-expanded', isActive);
            }
            body.style.overflow = isActive ? 'hidden' : '';
        }
        
        function closeMobileMenu() {
            mobileOverlay.classList.remove('active');
            if (mobileToggle) {
                mobileToggle.classList.remove('active');
                mobileToggle.setAttribute('aria-expanded', 'false');
            }
            body.style.overflow = '';
        }
        
        // Открытие меню
        mobileToggle.addEventListener('click', (e) => {
            e.preventDefault();
            toggleMobileMenu();
        });
        
        // Закрытие по крестику
        if (mobileClose) {
            mobileClose.addEventListener('click', closeMobileMenu);
        }
        
        // Закрытие по клику на оверлей
        mobileOverlay.addEventListener('click', (e) => {
            if (e.target === mobileOverlay) {
                closeMobileMenu();
            }
        });
        
        // Аккордеон для подменю на мобильных
        menuItemsWithChildren.forEach((item) => {
            const link = item.querySelector('a');
            const subMenu = item.querySelector('.sub-menu');
            const toggle = item.querySelector('.dropdown-toggle');
            
            if (link && subMenu) {
                // Обработка клика на стрелочку
                if (toggle) {
                    toggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        item.classList.toggle('active');
                        
                        // Закрываем другие открытые подменю
                        menuItemsWithChildren.forEach((otherItem) => {
                            if (otherItem !== item && otherItem.classList.contains('active')) {
                                otherItem.classList.remove('active');
                            }
                        });
                    });
                }
                
                // Обработка клика на ссылку (только на мобильных)
                link.addEventListener('click', (e) => {
                    if (window.innerWidth <= 992) {
                        // Если есть подменю - не переходим по ссылке
                        if (subMenu.children.length > 0) {
                            e.preventDefault();
                            item.classList.toggle('active');
                        }
                    }
                });
            }
        });
        
        // Закрытие меню при изменении размера окна
        window.addEventListener('resize', () => {
            if (window.innerWidth > 992) {
                closeMobileMenu();
            }
        });
    }
    
    // ===== SCROLL REveal АНИМАЦИЯ =====
    function initScrollReveal() {
        if (scrollRevealElements.length === 0) return;
        
        // Используем IntersectionObserver для производительности
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    // Перестаем наблюдать после появления
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        scrollRevealElements.forEach((el) => {
            revealObserver.observe(el);
        });
    }
    
    // ===== ПЛАВНАЯ ПРОКРУТКА ДЛЯ ЯКОРЕЙ =====
    function initSmoothScroll() {
        anchorLinks.forEach((anchor) => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Игнорируем пустые ссылки и внешние
                if (href === '#' || href.length <= 1) return;
                if (href.startsWith('#') === false) return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    
                    // Учитываем высоту шапки
                    const headerOffset = 80;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Закрываем мобильное меню если открыто
                    if (mobileOverlay && mobileOverlay.classList.contains('active')) {
                        mobileOverlay.classList.remove('active');
                        body.style.overflow = '';
                    }
                }
            });
        });
    }
    
    // ===== ШАПКА ПРИ СКРОЛЛЕ =====
    function initHeaderScroll() {
        if (!header) return;
        
        let lastScroll = 0;
        
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            lastScroll = currentScroll;
        }, { passive: true });
    }
    
    // ===== PARALLAX ЭФФЕКТ ДЛЯ HERO =====
    function initHeroParallax() {
        const heroSection = document.querySelector('.hero');
        if (!heroSection || window.innerWidth <= 768) return;
        
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            if (scrolled < window.innerHeight) {
                heroSection.style.setProperty('--scroll-y', scrolled);
                heroSection.classList.add('parallax-active');
            }
        }, { passive: true });
    }
    
    // ===== СЧЕТЧИКИ С АНИМАЦИЕЙ =====
    function initCounters() {
        const counters = document.querySelectorAll('.counter[data-target]');
        if (counters.length === 0) return;
        
        const animateCounter = (counter) => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const update = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.ceil(current);
                    requestAnimationFrame(update);
                } else {
                    counter.textContent = target;
                }
            };
            update();
        };
        
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    animateCounter(entry.target);
                    entry.target.classList.add('counted');
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.forEach((counter) => counterObserver.observe(counter));
    }
    
    // ===== 3D TILT ЭФФЕКТ ДЛЯ КАРТОЧЕК =====
    function initTiltEffect() {
        const tiltCards = document.querySelectorAll('.tilt-card');
        if (tiltCards.length === 0 || window.innerWidth <= 768) return;
        
        tiltCards.forEach((card) => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
            });
        });
    }
    
    // ===== ИНИЦИАЛИЗАЦИЯ ВСЕХ ФУНКЦИЙ =====
    function init() {
        initVideoIntro();
        initMobileMenu();
        initScrollReveal();
        initSmoothScroll();
        initHeaderScroll();
        initHeroParallax();
        initCounters();
        initTiltEffect();
        
        // Добавляем класс loaded после полной загрузки
        window.addEventListener('load', () => {
            body.classList.add('loaded');
        });
        
        console.log('Church Theme initialized successfully');
    }
    
    // Запуск
    init();
});