<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a EduClubs</title>
    <link rel="stylesheet" href="../css/landing.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../admin/assets/css/swiper-bundle.min.css">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258">
            <span>EduClubs</span>
        </div>
        <nav>
            <a href="auth.php" class="btn btn-header">Empezar</a>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <div class="content-text">
                    <h1>Bienvenido a EduClubs</h1>
                    <p>La plataforma definitiva para la gesti&oacute;n de clubes escolares en el CBTis 258. Inscr&iacute;bete, participa y organiza actividades de forma sencilla y centralizada. Descubre un universo de oportunidades para desarrollar tus pasiones y talentos.</p>
                    <a href="auth.php" class="btn2 btn-main">Explorar Clubes</a>
                </div>
                <div class="content-illustration">
                    <img src="https://imgs.search.brave.com/_VpDeB8mr801_g7J-cSTop6sCuiVrSc7GULCr-sqIBA/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9lc3Rp/bG9zZGVhcHJlbmRp/emFqZS5vcmcvd3At/Y29udGVudC91cGxv/YWRzLzIwMjQvMDQv/QUYxUWlwUFJPenVP/S0VVVEJjWWp5QlNp/c29xRHp4ZGNHTU9r/TC1wR3N6QS13NDA4/LWgzMDYtay1uby5q/cGVn" alt="Estudiantes colaborando">
                </div>
            </div>
        </section>

        <section class="features">
            <h2>¿Por qu&eacute; EduClubs?</h2>
            <div class="feature-cards">
                <div class="card">
                    <i class="fas fa-users"></i>
                    <h3>Centralizaci&oacute;n</h3>
                    <p>Todos los clubes en un solo lugar. Inscríbete y gestiona tus membresías fácilmente.</p>
                </div>
                <div class="card">
                    <i class="fas fa-tasks"></i>
                    <h3>Gesti&oacute;n para Maestros</h3>
                    <p>Crea y administra tus clubes, gestiona listas de miembros y organiza actividades sin complicaciones.</p>
                </div>
                <div class="card">
                    <i class="fas fa-search-plus"></i>
                    <h3>Explora y Participa</h3>
                    <p>Descubre la gran variedad de clubes disponibles, desde deportes y artes hasta ciencias y tecnolog&iacute;a. &Uacute;nete a los que m&aacute;s te apasionen y conoce a otros estudiantes con tus mismos intereses.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="contact-info">
                <h3>Contacto</h3>
                <p><i class="fas fa-map-marker-alt"></i> CBTis 258, Cd. Ju&aacute;rez, Chihuahua</p>
                <p><i class="fas fa-phone"></i> (656) 123-4567</p>
                <p><i class="fas fa-envelope"></i> contacto@cbtis258.edu.mx</p>
            </div>
            <div class="social-media">
                <h3>S&iacute;guenos</h3>
                <a href="https://www.facebook.com/CBTIS258NL/" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 EduClubs CBTis 258. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="../admin/assets/js/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".club-swiper", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: "auto",
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true,
            },
            pagination: {
                el: ".swiper-pagination",
            },
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
        });
    </script>
</body>
</html>
