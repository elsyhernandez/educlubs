document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuNav = document.getElementById('menu-nav');
    const menuIcon = menuToggle.querySelector('i');

    menuToggle.addEventListener('click', function() {
        menuNav.classList.toggle('show');
        menuIcon.classList.toggle('fa-times');
        menuIcon.classList.toggle('fa-bars');
    });

    document.addEventListener('click', function(event) {
        if (!menuNav.contains(event.target) && !menuToggle.contains(event.target)) {
            menuNav.classList.remove('show');
            if (menuIcon.classList.contains('fa-times')) {
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        }
    });

    const currentPath = window.location.pathname.split('/').pop();
    const navLinks = menuNav.querySelectorAll('ul li a');

    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href').split('/').pop();
        if (linkPath === currentPath) {
            link.classList.add('active');
        }
    });
});
