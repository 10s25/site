

// --- SLIDER VISUELS ---
if (!window.__sliderVisuelsInit) {
    window.__sliderVisuelsInit = true;
    console.log('🧩 Initialisation du slider visuels (avec préchargement)');

    document.addEventListener('DOMContentLoaded', () => {
        const container = document.querySelector('.slider-visuels-container');
        const slider = document.querySelector('.slider-visuels');
        const slides = document.querySelectorAll('.slide-visuel');
        const next = document.querySelector('.slider-btn-visuels.next');
        const prev = document.querySelector('.slider-btn-visuels.prev');

        if (!slider || slides.length === 0) {
            console.warn('⚠️ Aucun slider visuel trouvé.');
            return;
        }

        let index = 0;
        const extraSpace = 20;
        const slideDuration = 6000;
        const transitionDuration = 500;
        let interval;

        slider.style.transition = `transform ${transitionDuration}ms ease`;

        function adjustHeight() {
            const img = slides[index]?.querySelector('img');
            if (!img) return;
            const update = () => {
                container.style.height = (img.offsetHeight + extraSpace) + 'px';
            };
            img.complete ? update() : (img.onload = update);
        }

        async function preloadImage(i) {
            return new Promise((resolve) => {
                const img = slides[i]?.querySelector('img');
                if (!img) return resolve();
                if (img.complete) return resolve();
                const temp = new Image();
                temp.src = img.src;
                temp.onload = () => resolve();
                temp.onerror = () => resolve();
            });
        }

        async function showSlide(i, withTransition = true) {
            container.classList.add('loading');
            await preloadImage(i);

            slider.style.transition = withTransition
                ? `transform ${transitionDuration}ms ease`
                : 'none';

            index = (i + slides.length) % slides.length;
            slider.style.transform = `translateX(-${index * 100}%)`;
            adjustHeight();

            container.classList.remove('loading');
            console.log(`🎞️ Affiche slide ${index + 1}/${slides.length}`);
        }

        async function nextSlide() {
            const nextIndex = (index + 1) % slides.length;
            await showSlide(nextIndex);
        }

        async function prevSlide() {
            const prevIndex = (index - 1 + slides.length) % slides.length;
            await showSlide(prevIndex);
        }

        next?.addEventListener('click', () => {
            nextSlide();
            resetAuto();
        });

        prev?.addEventListener('click', () => {
            prevSlide();
            resetAuto();
        });

        function startAuto() {
            interval = setInterval(nextSlide, slideDuration);
        }

        function resetAuto() {
            clearInterval(interval);
            startAuto();
        }

        showSlide(0, false);
        startAuto();
    });
}
