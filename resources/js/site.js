const navbar = document.getElementById('navbar');

window.addEventListener('scroll', () => {
    if (!navbar) {
        return;
    }

    const scrolled = window.scrollY > 50;
    navbar.classList.toggle('shadow-lg', scrolled);
    navbar.classList.toggle('backdrop-blur-md', scrolled);
    navbar.style.backgroundColor = scrolled ? 'rgba(8, 80, 65, 0.95)' : '#085041';
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', event => {
        const target = document.querySelector(anchor.getAttribute('href'));

        if (!target) {
            return;
        }

        event.preventDefault();
        target.scrollIntoView({ behavior: 'smooth' });
    });
});

const profileButton = document.getElementById('profileMenuButton');
const profileDropdown = document.getElementById('profileMenuDropdown');

if (profileButton && profileDropdown) {
    profileButton.addEventListener('click', event => {
        event.stopPropagation();
        profileDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', event => {
        if (!profileDropdown.contains(event.target) && !profileButton.contains(event.target)) {
            profileDropdown.classList.add('hidden');
        }
    });
}

const mobileMenuButton = document.getElementById('mobileMenuButton');
const mobileMenu = document.getElementById('mobileMenu');
const mobileMenuOpenIcon = document.getElementById('mobileMenuIconOpen');
const mobileMenuCloseIcon = document.getElementById('mobileMenuIconClose');

function closeMobileMenu() {
    if (!mobileMenu || !mobileMenuOpenIcon || !mobileMenuCloseIcon) {
        return;
    }

    mobileMenu.classList.add('hidden');
    mobileMenuOpenIcon.classList.remove('hidden');
    mobileMenuCloseIcon.classList.add('hidden');
}

if (mobileMenuButton && mobileMenu && mobileMenuOpenIcon && mobileMenuCloseIcon) {
    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        mobileMenuOpenIcon.classList.toggle('hidden');
        mobileMenuCloseIcon.classList.toggle('hidden');
    });

    document.querySelectorAll('.mobile-menu-link').forEach(link => {
        link.addEventListener('click', closeMobileMenu);
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            closeMobileMenu();
        }
    });
}

async function refreshCsrfToken(form) {
    const csrfUrl = document.body.dataset.csrfUrl;

    if (!csrfUrl) {
        throw new Error('Sesi tidak tersedia.');
    }

    const response = await fetch(csrfUrl, {
        credentials: 'same-origin',
        cache: 'no-store',
        headers: { Accept: 'application/json' },
    });

    if (!response.ok || response.redirected) {
        throw new Error('Sesi sudah berubah.');
    }

    const data = await response.json();
    const input = form.querySelector('input[name="_token"]');

    if (!data.token || !input) {
        throw new Error('Token sesi tidak valid.');
    }

    input.value = data.token;
}

document.querySelectorAll('.logout-form').forEach(form => {
    form.addEventListener('submit', async event => {
        event.preventDefault();

        if (form.dataset.submitting === 'true') {
            return;
        }

        form.dataset.submitting = 'true';

        try {
            await refreshCsrfToken(form);
            form.submit();
        } catch {
            window.location.assign(document.body.dataset.loginUrl || '/login');
        }
    });
});
