<footer class="bg-dark text-white py-5">
        <div class="container-lg">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h5 class="fw-bold mb-3">
                        <i data-lucide="target" class="me-2"></i>NextTrak
                    </h5>
                    <p class="text-muted">
                        The smart way to track job applications and accelerate your career growth.
                    </p>
                </div>

                <div class="col-lg-6 text-lg-end">
                    <div class="mb-3">
                        <a href="#" class="text-white text-decoration-none me-4">Privacy Policy</a>
                        <a href="#" class="text-white text-decoration-none me-4">Terms of Service</a>
                        <a href="#contact" class="text-white text-decoration-none">Contact</a>
                    </div>
                    <p class="text-muted mb-0">© 2024 NextTrak. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
<!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();

        //Scroll animation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    </script>
</body>

</html>