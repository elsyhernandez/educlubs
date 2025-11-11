document.addEventListener('DOMContentLoaded', function() {
    // ==========================================================
    // --- Lógica del Carrusel 3D ---
    // ==========================================================
    const carousel = document.getElementById('carrusel');
    if (carousel) {
        const cards = Array.from(carousel.children);
        const numCards = cards.length;
        const angleIncrement = 360 / numCards;
        let currentCardIndex = 0;
        let currentRotation = 0;
        let autoRotateInterval;

        function updateCarousel() {
            currentRotation = -(currentCardIndex * angleIncrement);
            carousel.style.transform = `translateZ(calc(var(--translate-z) * -1)) rotateY(${currentRotation}deg)`;

            cards.forEach((card, index) => {
                card.classList.remove('active');
            });

            cards.forEach((card, index) => {
                const relativeIndex = (index - currentCardIndex + numCards) % numCards;
                if (relativeIndex === 0) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            });
        }

        function moverCarrusel(dir) {
            resetAutoRotate();
            currentCardIndex += dir;

            if (currentCardIndex < 0) {
                currentCardIndex = numCards - 1;
            } else if (currentCardIndex >= numCards) {
                currentCardIndex = 0;
            }

            updateCarousel();
        }

        cards.forEach((card, index) => {
            card.addEventListener('click', () => {
                resetAutoRotate();
                currentCardIndex = index;
                updateCarousel();
            });
        });

        function autoRotate() {
            moverCarrusel(1);
        }

        function resetAutoRotate() {
            clearInterval(autoRotateInterval);
            autoRotateInterval = setInterval(autoRotate, 5000);
        }

        // Event listeners for carousel buttons
        const prevButton = document.querySelector('.btn-prev');
        const nextButton = document.querySelector('.btn-next');

        if (prevButton) {
            prevButton.addEventListener('click', () => moverCarrusel(-1));
        }
        if (nextButton) {
            nextButton.addEventListener('click', () => moverCarrusel(1));
        }

        updateCarousel();
        resetAutoRotate();
    }

    // ==========================================================
    // --- Lógica del Carrusel de Features ---
    // ==========================================================
    const featuresWrapper = document.getElementById('featuresWrapper');
    if (featuresWrapper) {
        const prevBtnFeatures = document.getElementById('featuresPrevBtn');
        const nextBtnFeatures = document.getElementById('featuresNextBtn');
        const paginationDotsFeatures = document.getElementById('featuresPaginationDots');
        const featureCards = document.querySelectorAll('.feature-card');

        const cardsPerView = 2;
        let currentFeatureSlide = 0;
        const totalSlides = Math.ceil(featureCards.length / cardsPerView);

        function updateFeaturesCarousel() {
            const featuresContainer = document.querySelector('.features-container');
            const containerWidth = featuresContainer.offsetWidth;
            const gap = 20;
            const cardWidth = (containerWidth - gap) / cardsPerView;
            const slideDistance = cardWidth * cardsPerView + gap;
            let offset;

            if (window.innerWidth <= 768) {
                offset = -currentFeatureSlide * (containerWidth + gap);
            } else {
                offset = -currentFeatureSlide * slideDistance;
            }

            featuresWrapper.style.transform = `translateX(${offset}px)`;

            document.querySelectorAll('.feature-dot').forEach((dot, index) => {
                if (index === currentFeatureSlide) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            featureCards.forEach(card => card.classList.remove('animate-slide'));
            const startCardIndex = currentFeatureSlide * cardsPerView;
            const visibleCardsCount = window.innerWidth <= 768 ? 1 : cardsPerView;

            for (let i = 0; i < visibleCardsCount; i++) {
                const cardIndex = startCardIndex + i;
                if (featureCards[cardIndex]) {
                    featureCards[cardIndex].classList.add('animate-slide');
                }
            }
        }

        const moveFeaturesCarousel = (dir) => {
            if (dir === -1) {
                if (currentFeatureSlide === 0) {
                    currentFeatureSlide = totalSlides - 1;
                } else {
                    currentFeatureSlide--;
                }
            } else if (dir === 1) {
                if (currentFeatureSlide === totalSlides - 1) {
                    currentFeatureSlide = 0;
                } else {
                    currentFeatureSlide++;
                }
            }
            updateFeaturesCarousel();
        };

        if (prevBtnFeatures) {
            prevBtnFeatures.addEventListener('click', () => moveFeaturesCarousel(-1));
        }
        if (nextBtnFeatures) {
            nextBtnFeatures.addEventListener('click', () => moveFeaturesCarousel(1));
        }
        if (paginationDotsFeatures) {
            paginationDotsFeatures.addEventListener('click', (event) => {
                if (event.target.classList.contains('feature-dot')) {
                    currentFeatureSlide = parseInt(event.target.dataset.slide);
                    updateFeaturesCarousel();
                }
            });
        }

        window.addEventListener('load', updateFeaturesCarousel);
        window.addEventListener('resize', updateFeaturesCarousel);
    }

    // ==========================================================
    // --- Lógica de Intersección (Animación al Scroll) ---
    // ==========================================================
    const bottomSection = document.getElementById('bottomSection');
    if (bottomSection) {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                        if (featuresWrapper) {
                            updateFeaturesCarousel();
                        }
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '0px',
                threshold: 0.1
            });
            observer.observe(bottomSection);
        } else {
            bottomSection.classList.add('animate');
            if (featuresWrapper) {
                updateFeaturesCarousel();
            }
        }
    }

    // ==========================================================
    // --- Lógica del Modal de Registro ---
    // ==========================================================
    const registrationModal = document.getElementById('registrationModal');
    const btnRegistrar = document.getElementById('btn-registrar');
    const closeModal = document.querySelector('#registrationModal .close-button');

    if (btnRegistrar) {
        btnRegistrar.addEventListener('click', function() {
            if (registrationModal) {
                registrationModal.style.display = 'block';
            }
        });
    }

    if (closeModal) {
        closeModal.addEventListener('click', function() {
            if (registrationModal) {
                registrationModal.style.display = 'none';
            }
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target == registrationModal) {
            registrationModal.style.display = 'none';
        }
    });
});
