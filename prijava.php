<?php
// učitavanje potrebnih datoteka
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// ako je korisnik već prijavljen, preusmjeri ga na početnu
if (jePrijavljen()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// inicijalizacija varijabli stranice
$naslovStranice = 'Prijava';
$greska = '';
$porukaNeprijavljen = '';

// provjeri je li korisnik preusmjeren jer pokušao pristupiti zaštićenoj stranici
if (isset($_GET['poruka']) && $_GET['poruka'] === 'morate_se_prijaviti') {
    $porukaNeprijavljen = 'Morate se prijaviti za pristup toj stranici.';
}

// obrada forme samo pri POST zahtjevu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // dohvat i čišćenje unesenih podataka
    $email   = trim($_POST['email']   ?? '');
    $lozinka = trim($_POST['lozinka'] ?? '');

    // provjera jesu li polja popunjena
    if (empty($email) || empty($lozinka)) {
        $greska = 'Molimo unesite e-mail i lozinku.';
    } else {
        // traženje korisnika u bazi prema e-mailu
        $stmt = $pdo->prepare("SELECT * FROM korisnici WHERE email = ?");
        $stmt->execute([$email]);
        $korisnik = $stmt->fetch();

        // provjera lozinke i pokretanje sesije
        if ($korisnik && password_verify($lozinka, $korisnik['lozinka'])) {
            // regeneracija id-a sesije radi zaštite od session fixation napada
            session_regenerate_id(true);
            postavljiSesiju($korisnik);

            // preusmjeri admina na admin panel, ostale na početnu
            if ($korisnik['uloga'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin/index.php');
            } else {
                header('Location: ' . BASE_URL . '/index.php');
            }
            exit;
        } else {
            // pogrešni podaci — ne otkrivaj koji je podatak neispravan
            $greska = 'Pogrešan e-mail ili lozinka.';
        }
    }
}

// uključi zajedničko zaglavlje
include 'includes/header.php';
?>

<div class="forma-omotac">
    <h2>Prijava</h2>
    <p class="podnaslov-forme">Prijavite se na vaš račun</p>

    <?php // prikaži info poruku ako korisnik nije prijavljen ?>
    <?php if ($porukaNeprijavljen): ?>
        <div class="poruka-info"><i class="fa fa-info-circle"></i> <?= ocisti($porukaNeprijavljen) ?></div>
    <?php endif; ?>

    <?php // prikaži grešku ako prijava nije uspjela ?>
    <?php if ($greska): ?>
        <div class="poruka-greska"><i class="fa fa-exclamation-circle"></i> <?= ocisti($greska) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/prijava.php" method="POST">

        <div class="forma-grupa">
            <label for="email"><i class="fa fa-envelope"></i> E-mail adresa</label>
            <input type="email"
                   id="email"
                   name="email"
                   value="<?= ocisti($_POST['email'] ?? '') ?>"
                   placeholder="vasa@email.hr"
                   required
                   autocomplete="email">
        </div>

        <div class="forma-grupa">
            <label for="lozinka"><i class="fa fa-lock"></i> Lozinka</label>
            <input type="password"
                   id="lozinka"
                   name="lozinka"
                   placeholder="••••••••"
                   required
                   autocomplete="current-password">
        </div>

        <button type="submit" class="btn btn-primarni" style="width: 100%; margin-top: 10px;">
            <i class="fa fa-sign-in-alt"></i> Prijavi se
        </button>
    </form>

    <div class="forma-podnozje">
        Nemaš račun? <a href="<?= BASE_URL ?>/registracija.php">Registriraj se</a>
    </div>

    <div class="poruka-info ne-skrivaj" style="margin-top: 20px; font-size: 0.85rem;">
        <strong>Testni podaci:</strong><br>
        Admin: admin@monetica.hr / admin123<br>
        Korisnik: korisnik@test.hr / test123
    </div>
</div>

<?php // uključi zajedničko podnožje
include 'includes/footer.php'; ?>
