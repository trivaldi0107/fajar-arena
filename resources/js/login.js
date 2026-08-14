document.addEventListener("DOMContentLoaded", () => {

    const characters = document.querySelectorAll('.character');
    const eyes = document.querySelectorAll('.eye');
    const email = document.getElementById('email');
    const password = document.getElementById('password');

    // FOLLOW CURSOR
    document.addEventListener('mousemove', (e) => {
        eyes.forEach(eye => {
            const rect = eye.getBoundingClientRect();

            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;

            const angle = Math.atan2(e.clientY - y, e.clientX - x);

            const moveX = Math.cos(angle) * 3;
            const moveY = Math.sin(angle) * 3;

            eye.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });
    });

    // EMAIL → 😮
    email?.addEventListener('focus', () => {
        characters.forEach(c => {
            c.classList.remove('error', 'password');
            c.classList.add('o');
        });
    });

    // PASSWORD → 😑
    password?.addEventListener('focus', () => {
        characters.forEach(c => {
            c.classList.remove('o', 'error');
            c.classList.add('password');
        });
    });

    // RESET → 🙂
    document.addEventListener('click', (e) => {
        if (!email?.contains(e.target) && !password?.contains(e.target)) {
            characters.forEach(c => {
                c.classList.remove('password', 'o', 'error');
                c.classList.add('smile');
            });
        }
    });

    // ERROR SIMULATION (nanti bisa dari Laravel validation)
    const hasError =
        document.querySelector('[data-error="email"]') ||
        document.querySelector('[data-error="password"]');

    if (hasError) {
        characters.forEach(c => {
            c.classList.remove('smile');
            c.classList.add('error');
        });
    }

});