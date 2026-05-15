<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if (jePrijavljen()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$naslovStranice = 'Registracija';
$greska  = '';
$poruka  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime      = trim($_POST['ime']      ?? '');
    $prezime  = trim($_POST['prezime']  ?? '');
    $email    = trim($_POST['email']    ?? '');
    $lozinka  = trim($_POST['lozinka']  ?? '');
    $potvrda  = trim($_POST['potvrda']  ?? '');

    if (empty($ime) || empty($prezime) || empty($email) || empty($lozinka)) {
        $greska = 'Molimo ispunite sva polja.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greska = 'Unesite valjanu e-mail adresu.';
    } elseif (mb_strlen($lozinka) < 6) {
        $greska = 'Lozinka mora imati najmanje 6 znakova.';
    } elseif ($lozinka !== $potvrda) {
        $greska = 'Lozinke se ne podudaraju.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM korisnici WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $greska = 'Korisnik s tom e-mail adresom već postoji.';
        } else {
            $hashLozinka = password_hash($lozinka, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO korisnici (ime, prezime, email, lozinka, uloga)
                VALUES (?, ?, ?, ?, 'korisnik')
            ");
            $stmt->execute([$ime, $prezime, $email, $hashLozinka]);

            $noviId   = $pdo->lastInsertId();
            $korisnik = $pdo->query("SELECT * FROM korisnici WHERE id = $noviId")->fetch();
            session_regenerate_id(true);
            postavljiSesiju($korisnik);

            header('Location: ' . BASE_URL . '/index.php?dobrodoslica=1');
            exit;
        }
    }
}

include 'includes/header.php';
?>

<div class="forma-omotac">
    <h2>Registracija</h2>
    <p class="podnaslov-forme">Kreirajte besplatni račun</p>

    <?php if ($greska): ?>
        <div class="poruka-greska"><i class="fa fa-exclamation-circle"></i> <?= ocisti($greska) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/registracija.php" method="POST">

        <div class="forma-2kol">
            <div class="forma-grupa">
                <label for="ime"><i class="fa fa-user"></i> Ime</label>
                <input type="text"
                       id="ime"
                       name="ime"
                       value="<?= ocisti($_POST['ime'] ?? '') ?>"
                       placeholder="Vaše ime"
                       required
                       autocomplete="given-name">
            </div>
            <div class="forma-grupa">
                <label for="prezime">Prezime</label>
                <input type="text"
                       id="prezime"
                       name="prezime"
                       value="<?= ocisti($_POST['prezime'] ?? '') ?>"
                       placeholder="Vaše prezime"
                       required
                       autocomplete="family-name">
            </div>
        </div>

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
            <label for="lozinka"><i class="fa fa-lock"></i> Lozinka <small style="color:var(--boja-siva)">(min. 6 znakova)</small></label>
            <input type="password"
                   id="lozinka"
                   name="lozinka"
                   placeholder="••••••••"
                   required
                   minlength="6"
                   autocomplete="new-password">
        </div>

        <div class="forma-grupa">
            <label for="potvrda"><i class="fa fa-lock"></i> Potvrdi lozinku</label>
            <input type="password"
                   id="potvrda"
                   name="potvrda"
                   placeholder="••••••••"
                   required
                   autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primarni" style="width: 100%; margin-top: 10px;">
            <i class="fa fa-user-plus"></i> Registriraj se
        </button>
    </form>

    <div class="forma-podnozje">
        Već imaš račun? <a href="<?= BASE_URL ?>/prijava.php">Prijavi se</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
