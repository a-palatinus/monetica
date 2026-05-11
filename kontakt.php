<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$naslovStranice = 'Kontakt';
$poruka = '';
$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime    = trim($_POST['ime']    ?? '');
    $email  = trim($_POST['email']  ?? '');
    $tekst  = trim($_POST['poruka'] ?? '');

    if (empty($ime) || empty($email) || empty($tekst)) {
        $greska = 'Molimo ispunite sva polja.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greska = 'Unesite valjanu e-mail adresu.';
    } elseif (mb_strlen($tekst) < 20) {
        $greska = 'Poruka mora sadržavati najmanje 20 znakova.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO kontakt_poruke (ime, email, poruka) VALUES (?, ?, ?)");
        $stmt->execute([$ime, $email, $tekst]);
        $poruka = 'Hvala! Vaša poruka je uspješno poslana. Javit ćemo vam se uskoro.';
    }
}

include 'includes/header.php';
?>

<div class="o-nama-hero">
    <div class="kontejner">
        <h1>Kontakt</h1>
        <p>Javite nam se — rado ćemo odgovoriti na sva pitanja</p>
    </div>
</div>

<section class="sekcija">
    <div class="kontejner">
        <div class="kontakt-mreza">

            <div>
                <h2 class="naslov-sekcija lijevo">Pošaljite nam poruku</h2>

                <?php if ($poruka): ?>
                    <div class="poruka-uspjeh"><i class="fa fa-check-circle"></i> <?= ocisti($poruka) ?></div>
                <?php endif; ?>
                <?php if ($greska): ?>
                    <div class="poruka-greska"><i class="fa fa-exclamation-circle"></i> <?= ocisti($greska) ?></div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/kontakt.php" method="POST">
                    <div class="forma-2kol">
                        <div class="forma-grupa">
                            <label for="ime">Ime i prezime *</label>
                            <input type="text"
                                   id="ime"
                                   name="ime"
                                   value="<?= ocisti($_POST['ime'] ?? (jePrijavljen() ? punoIme() : '')) ?>"
                                   placeholder="Vaše ime..."
                                   required>
                        </div>
                        <div class="forma-grupa">
                            <label for="email">E-mail *</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="<?= ocisti($_POST['email'] ?? (jePrijavljen() ? $_SESSION['email'] : '')) ?>"
                                   placeholder="vasa@email.hr"
                                   required>
                        </div>
                    </div>

                    <div class="forma-grupa">
                        <label for="poruka">Vaša poruka *</label>
                        <textarea id="poruka"
                                  name="poruka"
                                  placeholder="Napišite vašu poruku ovdje..."
                                  required><?= ocisti($_POST['poruka'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primarni" style="width: 100%;">
                        <i class="fa fa-paper-plane"></i> Pošalji poruku
                    </button>
                </form>
            </div>

            <div>
                <h2 class="naslov-sekcija lijevo">Pronađite nas</h2>

                <div style="margin-bottom: 30px;">
                    <div class="kontakt-info-stavka">
                        <div class="kontakt-ikona"><i class="fa fa-map-marker-alt"></i></div>
                        <div>
                            <strong>Adresa</strong><br>
                            <span style="color: var(--boja-siva);">Ilica 45, 10000 Zagreb</span>
                        </div>
                    </div>

                    <div class="kontakt-info-stavka">
                        <div class="kontakt-ikona"><i class="fa fa-phone"></i></div>
                        <div>
                            <strong>Telefon</strong><br>
                            <a href="tel:+38512345678" style="color: var(--boja-siva);">+385 1 234 5678</a>
                        </div>
                    </div>

                    <div class="kontakt-info-stavka">
                        <div class="kontakt-ikona"><i class="fa fa-envelope"></i></div>
                        <div>
                            <strong>E-mail</strong><br>
                            <a href="mailto:info@monetica.hr" style="color: var(--boja-siva);">info@monetica.hr</a>
                        </div>
                    </div>

                    <div class="kontakt-info-stavka">
                        <div class="kontakt-ikona"><i class="fa fa-clock"></i></div>
                        <div>
                            <strong>Radno vrijeme</strong><br>
                            <span style="color: var(--boja-siva);">Pon–Pet: 9:00–17:00<br>Sub: 10:00–14:00</span>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <strong>Pratite nas:</strong>
                    <div class="social-ikone" style="margin-top: 12px;">
                        <a href="#" class="social-ikona" style="background: #3b5998;" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-ikona" style="background: #e1306c;" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-ikona" style="background: #ff0000;" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-ikona" style="background: #1da1f2;" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>

                <div class="mapa-omotac">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2781.4!2d15.9669!3d45.8131!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4765d6f7a!2sIlica%2045%2C%20Zagreb!5e0!3m2!1shr!2shr!4v1"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokacija Društva Monetica">
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
