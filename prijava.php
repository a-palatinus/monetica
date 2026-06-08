<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if (jePrijavljen()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$naslovStranice = 'Prijava';
$greska = '';
$porukaNeprijavljen = '';

if (isset($_GET['poruka']) && $_GET['poruka'] === 'morate_se_prijaviti') {
    $porukaNeprijavljen = 'Morate se prijaviti za pristup toj stranici.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = trim($_POST['email']   ?? '');
    $lozinka = trim($_POST['lozinka'] ?? '');

    if (empty($email) || empty($lozinka)) {
        $greska = 'Molimo unesite e-mail i lozinku.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM korisnici WHERE email = ?");
        $stmt->execute([$email]);
        $korisnik = $stmt->fetch();

        if ($korisnik && password_verify($lozinka, $korisnik['lozinka'])) {
            session_regenerate_id(true);
            postavljiSesiju($korisnik);

            if ($korisnik['uloga'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin/index.php');
            } else {
                header('Location: ' . BASE_URL . '/index.php');
            }
            exit;
        } else {
            $greska = 'Pogrešan e-mail ili lozinka.';
        }
    }
}

include 'includes/header.php';
?>

<div class="forma-omotac">
    <h2>Prijava</h2>
    <p class="podnaslov-forme">Prijavite se na vaš račun</p>

    
    <?php if ($porukaNeprijavljen): ?>
        <div class="poruka-info"><i class="fa fa-info-circle"></i> <?= ocisti($porukaNeprijavljen) ?></div>
    <?php endif; ?>

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

</div>

<?php
include 'includes/footer.php'; ?>
