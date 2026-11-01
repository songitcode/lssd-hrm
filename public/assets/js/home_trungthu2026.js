/* ==========================================================
   HOME_TRUNGTHU2026.JS
   Interactive festival layer for LLCS LSSD
   ========================================================== */

(() => {
    'use strict';

    const root = document.querySelector('.tt26-page');
    if (!root) return;

    /* ---------------------------------------------------------
       Modal helper kept globally available for inline Blade
       onclick handlers.
       --------------------------------------------------------- */
    window.openImageModal = function (src) {
        const img = document.getElementById('modalImg');
        if (img) img.src = src;
    };

    /* ---------------------------------------------------------
       Countdown
       Festival reference date: 25/09/2026 (Rằm tháng Tám).
       Countdown is intentionally a client-side visual only.
       --------------------------------------------------------- */
    const countdown = {
        days: document.getElementById('tt26Days'),
        hours: document.getElementById('tt26Hours'),
        minutes: document.getElementById('tt26Minutes'),
        seconds: document.getElementById('tt26Seconds')
    };

    const festivalAt = new Date('2026-09-25T19:00:00+07:00').getTime();

    function renderCountdown() {
        const now = Date.now();
        let distance = Math.max(0, festivalAt - now);

        const days = Math.floor(distance / 86400000);
        distance %= 86400000;

        const hours = Math.floor(distance / 3600000);
        distance %= 3600000;

        const minutes = Math.floor(distance / 60000);
        const seconds = Math.floor((distance % 60000) / 1000);

        if (countdown.days) countdown.days.textContent = String(days).padStart(2, '0');
        if (countdown.hours) countdown.hours.textContent = String(hours).padStart(2, '0');
        if (countdown.minutes) countdown.minutes.textContent = String(minutes).padStart(2, '0');
        if (countdown.seconds) countdown.seconds.textContent = String(seconds).padStart(2, '0');

        const box = document.getElementById('tt26Countdown');
        if (box && festivalAt <= now) {
            box.classList.add('is-festival');
            const label = box.querySelector('.tt26-countdown-label span');
            const note = box.querySelector('.tt26-countdown-label small');

            if (label) label.textContent = 'ĐÊM TRĂNG ĐÃ VỀ';
            if (note) note.textContent = 'Chúc mừng Tết Trung Thu 2026';
        }
    }

    renderCountdown();
    window.setInterval(renderCountdown, 1000);

    /* ---------------------------------------------------------
       Scroll reveal
       --------------------------------------------------------- */
    const revealItems = root.querySelectorAll('[data-reveal]');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            });
        }, { threshold: 0.12 });

        revealItems.forEach((item, index) => {
            item.style.transitionDelay = `${Math.min(index * 0.045, 0.22)}s`;
            observer.observe(item);
        });
    } else {
        revealItems.forEach(item => item.classList.add('is-visible'));
    }

    /* ---------------------------------------------------------
       Ambient fireflies
       --------------------------------------------------------- */
    const prefersReducedMotion = window.matchMedia
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function createFirefly() {
        if (prefersReducedMotion) return;

        const firefly = document.createElement('span');
        firefly.className = 'tt26-firefly';

        const left = 5 + Math.random() * 90;
        const top = 18 + Math.random() * 70;
        const fx = (Math.random() - 0.5) * 90;
        const fy = (Math.random() - 0.5) * 120;

        firefly.style.left = `${left}vw`;
        firefly.style.top = `${top}vh`;
        firefly.style.setProperty('--fx', `${fx}px`);
        firefly.style.setProperty('--fy', `${fy}px`);
        firefly.style.animationDelay = `${Math.random() * -5}s`;
        firefly.style.animationDuration = `${4.2 + Math.random() * 3.6}s`;

        document.body.appendChild(firefly);

        window.setTimeout(() => firefly.remove(), 10000);
    }

    if (!prefersReducedMotion) {
        for (let i = 0; i < 22; i++) createFirefly();
        window.setInterval(createFirefly, 1050);
    }

    /* ---------------------------------------------------------
       Lantern click interaction
       --------------------------------------------------------- */
    const atmosphereLanterns = root.querySelectorAll('[data-lantern]');
    const stageLanterns = root.querySelectorAll('[data-stage-lantern]');
    const lightAllBtn = document.getElementById('tt26LightAll');

    function burstAtElement(element, amount = 8) {
        if (prefersReducedMotion || !element) return;

        const rect = element.getBoundingClientRect();
        const x = rect.left + rect.width / 2;
        const y = rect.top + rect.height / 2;

        for (let i = 0; i < amount; i++) {
            const particle = document.createElement('span');
            particle.className = 'tt26-particle';

            const angle = (Math.PI * 2 * i) / amount + Math.random() * 0.35;
            const distance = 28 + Math.random() * 56;

            particle.style.left = `${x}px`;
            particle.style.top = `${y}px`;
            particle.style.setProperty('--px', `${Math.cos(angle) * distance}px`);
            particle.style.setProperty('--py', `${Math.sin(angle) * distance}px`);
            particle.style.animationDelay = `${Math.random() * 0.08}s`;

            document.body.appendChild(particle);
            window.setTimeout(() => particle.remove(), 1000);
        }
    }

    stageLanterns.forEach(lantern => {
        lantern.addEventListener('click', () => {
            const active = lantern.classList.toggle('is-lit');
            if (active) burstAtElement(lantern, 11);
        });
    });

    atmosphereLanterns.forEach(lantern => {
        lantern.addEventListener('click', () => {
            burstAtElement(lantern, 7);
        });
    });

    if (lightAllBtn) {
        lightAllBtn.addEventListener('click', () => {
            stageLanterns.forEach(lantern => lantern.classList.add('is-lit'));
            burstAtElement(lightAllBtn, 20);
        });
    }

    /* ---------------------------------------------------------
       Soft click spark on festival buttons / CTA
       --------------------------------------------------------- */
    root.querySelectorAll('.tt26-btn, .tt26-feature-card, .tt26-quick-card').forEach(item => {
        item.addEventListener('click', event => {
            if (event.defaultPrevented) return;
            burstAtPoint(event.clientX, event.clientY, 5);
        });
    });

    function burstAtPoint(x, y, amount = 6) {
        if (prefersReducedMotion || !Number.isFinite(x) || !Number.isFinite(y)) return;

        for (let i = 0; i < amount; i++) {
            const particle = document.createElement('span');
            particle.className = 'tt26-particle';

            const angle = (Math.PI * 2 * i) / amount + Math.random() * 0.2;
            const distance = 18 + Math.random() * 38;

            particle.style.left = `${x}px`;
            particle.style.top = `${y}px`;
            particle.style.setProperty('--px', `${Math.cos(angle) * distance}px`);
            particle.style.setProperty('--py', `${Math.sin(angle) * distance}px`);

            document.body.appendChild(particle);
            window.setTimeout(() => particle.remove(), 1000);
        }
    }

    /* ---------------------------------------------------------
       Back to top
       --------------------------------------------------------- */
    const topButton = document.getElementById('tt26Top');

    function updateTopButton() {
        if (!topButton) return;
        topButton.classList.toggle('is-visible', window.scrollY > 620);
    }

    window.addEventListener('scroll', updateTopButton, { passive: true });
    updateTopButton();

    if (topButton) {
        topButton.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ---------------------------------------------------------
       Subtle cursor glow on desktop
       --------------------------------------------------------- */
    if (!prefersReducedMotion && window.matchMedia('(pointer:fine)').matches) {
        let glow = null;
        let ticking = false;

        const createCursorGlow = () => {
            if (glow) return;

            glow = document.createElement('span');
            glow.style.position = 'fixed';
            glow.style.width = '15px';
            glow.style.height = '15px';
            glow.style.borderRadius = '50%';
            glow.style.pointerEvents = 'none';
            glow.style.zIndex = '9991';
            glow.style.background = 'rgba(255,224,145,.85)';
            glow.style.boxShadow = '0 0 22px 6px rgba(245,200,91,.18)';
            glow.style.transform = 'translate(-50%, -50%)';
            glow.style.opacity = '0';
            glow.style.transition = 'opacity .18s ease';
            document.body.appendChild(glow);
        };

        createCursorGlow();

        window.addEventListener('mousemove', event => {
            if (!glow) return;

            const x = event.clientX;
            const y = event.clientY;

            if (!ticking) {
                window.requestAnimationFrame(() => {
                    glow.style.left = `${x}px`;
                    glow.style.top = `${y}px`;
                    glow.style.opacity = '1';
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        window.addEventListener('mouseleave', () => {
            if (glow) glow.style.opacity = '0';
        });
    }
})();
