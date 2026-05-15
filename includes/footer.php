</main>
<footer class="footer">
    <div class="footer-inner">

        <div class="footer-kolona">
            <h3 class="footer-logo"><span class="logo-m">M</span>onetica</h3>
            <p>Umjetničko društvo posvećeno promicanju i razvoju likovnih umjetnosti u zajednici od 1987. godine.</p>
        </div>

        <div class="footer-kolona">
            <h4>Brze poveznice</h4>
            <ul class="footer-lista">
                <li><a href="<?= BASE_URL ?>/index.php">Početna</a></li>
                <li><a href="<?= BASE_URL ?>/o-nama.php">O nama</a></li>
                <li><a href="<?= BASE_URL ?>/galerija.php">Galerija</a></li>
                <li><a href="<?= BASE_URL ?>/aktualno.php">Aktualno</a></li>
                <li><a href="<?= BASE_URL ?>/kontakt.php">Kontakt</a></li>
            </ul>
        </div>

        <div class="footer-kolona">
            <h4>Povežite se s nama</h4>
            <p><i class="fa fa-map-marker-alt"></i> Ilica 45, Zagreb</p>
            <p><i class="fa fa-phone"></i> +385 1 234 5678</p>
            <p><i class="fa fa-envelope"></i> info@monetica.hr</p>

            <div class="social-ikone">
                <a href="#" aria-label="Facebook"  class="social-ikona"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Instagram" class="social-ikona"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="YouTube"   class="social-ikona"><i class="fab fa-youtube"></i></a>
                <a href="#" aria-label="Twitter"   class="social-ikona"><i class="fab fa-twitter"></i></a>
            </div>

            <p style="font-size:0.8rem; color:rgba(255,255,255,0.5); margin-bottom:8px; margin-top:20px;">
                <i class="fa fa-envelope" style="color:var(--boja-akcent);"></i> Pretplatite se na naš newsletter:
            </p>
            <form class="newsletter-mini" action="<?= BASE_URL ?>/newsletter.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="email" name="email" placeholder="Vaš e-mail..." required>
                <button type="submit"><i class="fa fa-paper-plane"></i></button>
            </form>
        </div>

    </div>

    <div class="footer-dno">
        <p>&copy; <?= date('Y') ?> Umjetničko društvo Monetica. Sva prava pridržana.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmFGOaqAb/EMGHNMODsJtHuJkTL/"
        crossorigin="anonymous"></script>

<script src="<?= BASE_URL ?>/js/main.js"></script>

</body>
</html>
