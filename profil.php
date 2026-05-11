<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

zahtijevajPrijavu();

$naslovStranice = 'Moj profil';
$poruka = '';
$greska = '';

$stmt = $pdo->prepare("SELECT * FROM korisnici WHERE id = ?");
$stmt->execute([$_SESSION['korisnik_id']]);
$korisnik = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcija = $_POST['akcija'] ?? '';

    if ($akcija === 'uredi_profil') {
        $ime     = trim($_POST['ime']     ?? '');
        $prezime = trim($_POST['prezime'] ?? '');
        $bio     = trim($_POST['bio']     ?? '');

        if (empty($ime) || empty($prezime)) {
            $greska = 'Ime i prezime su obavezni.';
        } else {
            $stmt = $pdo->prepare("UPDATE korisnici SET ime = ?, prezime = ?, bio = ? WHERE id = ?");
            $stmt->execute([$ime, $prezime, $bio, $_SESSION['korisnik_id']]);

            $_SESSION['ime']     = $ime;
            $_SESSION['prezime'] = $prezime;
            $korisnik['ime']     = $ime;
            $korisnik['prezime'] = $prezime;
            $korisnik['bio']     = $bio;

            $poruka = 'Profil je uspješno ažuriran.';
        }

    } elseif ($akcija === 'promjena_lozinke') {
        $staraLozinka = $_POST['stara_lozinka'] ?? '';
        $novaLozinka  = $_POST['nova_lozinka']  ?? '';
        $potvrda      = $_POST['potvrda']        ?? '';

        if (!password_verify($staraLozinka, $korisnik['lozinka'])) {
            $greska = 'Stara lozinka nije ispravna.';
        } elseif (mb_strlen($novaLozinka) < 6) {
            $greska = 'Nova lozinka mora imati najmanje 6 znakova.';
        } elseif ($novaLozinka !== $potvrda) {
            $greska = 'Nova lozinka i potvrda se ne podudaraju.';
        } else {
            $hash = password_hash($novaLozinka, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE korisnici SET lozinka = ? WHERE id = ?")->execute([$hash, $_SESSION['korisnik_id']]);
            $poruka = 'Lozinka je uspješno promijenjena.';
        }
    }
}

$brFavorita = $pdo->prepare("SELECT COUNT(*) FROM favoriti WHERE korisnik_id = ?");
$brFavorita->execute([$_SESSION['korisnik_id']]);
$brFavorita = $brFavorita->fetchColumn();

include 'includes/header.php';
?>

<div class="profil-hero">
    <div class="kontejner">
        <img class="profil-avatar"
             src="<?= $korisnik['profilna_slika'] ? BASE_URL . '/' . ocisti($korisnik['profilna_slika']) : 'https://ui-avatars.com/api/?name=' . urlencode(punoIme()) . '&background=3d2b1f&color=c9a84c&size=100' ?>"
             alt="Profilna slika">
        <h1 class="profil-ime"><?= ocisti(punoIme()) ?></h1>
        <span class="profil-uloga"><?= $_SESSION['uloga'] === 'admin' ? 'Administrator' : 'Član' ?></span>
        <p style="color: rgba(255,255,255,0.7); margin-top: 10px; font-size: 0.9rem;">
            Član od <?= formatirajDatum($korisnik['datum_registracije']) ?>
            &nbsp;·&nbsp;
            <a href="<?= BASE_URL ?>/favoriti.php" style="color: var(--boja-akcent);">
                <i class="fa fa-heart"></i> <?= $brFavorita ?> favorita
            </a>
        </p>
    </div>
</div>

<section class="sekcija">
    <div class="kontejner" style="max-width: 800px; margin: 0 auto;">

        <!-- Poruke -->
        <?php if ($poruka): ?>
            <div class="poruka-uspjeh"><i class="fa fa-check-circle"></i> <?= ocisti($poruka) ?></div>
        <?php endif; ?>
        <?php if ($greska): ?>
            <div class="poruka-greska"><i class="fa fa-exclamation-circle"></i> <?= ocisti($greska) ?></div>
        <?php endif; ?>

        <div style="background: white; border-radius: 8px; padding: 35px; box-shadow: var(--sjena); margin-bottom: 30px;">
            <h2 style="font-size: 1.4rem; margin-bottom: 25px;">
                <i class="fa fa-user" style="color: var(--boja-akcent);"></i> Uredi profil
            </h2>

            <form action="<?= BASE_URL ?>/profil.php" method="POST">
                <input type="hidden" name="akcija" value="uredi_profil">

                <div class="forma-2kol">
                    <div class="forma-grupa">
                        <label for="ime">Ime</label>
                        <input type="text" id="ime" name="ime" value="<?= ocisti($korisnik['ime']) ?>" required>
                    </div>
                    <div class="forma-grupa">
                        <label for="prezime">Prezime</label>
                        <input type="text" id="prezime" name="prezime" value="<?= ocisti($korisnik['prezime']) ?>" required>
                    </div>
                </div>

                <div class="forma-grupa">
                    <label>E-mail adresa <small style="color: var(--boja-siva)">(ne može se mijenjati)</small></label>
                    <input type="email" value="<?= ocisti($korisnik['email']) ?>" disabled style="opacity: 0.6;">
                </div>

                <div class="forma-grupa">
                    <label for="bio">Kratka bio</label>
                    <textarea id="bio" name="bio" placeholder="Nešto o sebi..."><?= ocisti($korisnik['bio'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primarni" style="margin-top: 15px;">
                    <i class="fa fa-save"></i> Spremi promjene
                </button>
            </form>
        </div>

        <div style="background: white; border-radius: 8px; padding: 35px; box-shadow: var(--sjena);">
            <h2 style="font-size: 1.4rem; margin-bottom: 25px;">
                <i class="fa fa-lock" style="color: var(--boja-akcent);"></i> Promjena lozinke
            </h2>

            <form action="<?= BASE_URL ?>/profil.php" method="POST">
                <input type="hidden" name="akcija" value="promjena_lozinke">

                <div class="forma-grupa">
                    <label for="stara_lozinka">Trenutna lozinka</label>
                    <input type="password" id="stara_lozinka" name="stara_lozinka" placeholder="••••••••" required>
                </div>

                <div class="forma-2kol">
                    <div class="forma-grupa">
                        <label for="nova_lozinka">Nova lozinka</label>
                        <input type="password" id="nova_lozinka" name="nova_lozinka" placeholder="••••••••" required minlength="6">
                    </div>
                    <div class="forma-grupa">
                        <label for="potvrda">Potvrdi novu lozinku</label>
                        <input type="password" id="potvrda" name="potvrda" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-akcent">
                    <i class="fa fa-key"></i> Promijeni lozinku
                </button>
            </form>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>
