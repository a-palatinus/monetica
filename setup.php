<?php
$baseUrl = rtrim(str_replace('\\', '/', substr(__DIR__, strlen(rtrim($_SERVER['DOCUMENT_ROOT'], '/\\')))), '/');

// podaci za spajanje na mysql server
$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbName = 'monetica';

try {
    // spajanje na mysql bez odabira baze (baza možda još ne postoji)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // kreiranje baze ako ne postoji
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_croatian_ci");
    $pdo->exec("USE `$dbName`");

    // tablica korisnika — čuva sve registrirane korisnike i admina
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS korisnici (
            id                INT AUTO_INCREMENT PRIMARY KEY,
            ime               VARCHAR(100) NOT NULL,
            prezime           VARCHAR(100) NOT NULL,
            email             VARCHAR(150) NOT NULL UNIQUE,
            lozinka           VARCHAR(255) NOT NULL,
            uloga             ENUM('korisnik', 'admin') NOT NULL DEFAULT 'korisnik',
            profilna_slika    VARCHAR(255) DEFAULT NULL,
            bio               TEXT DEFAULT NULL,
            datum_registracije DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // tablica favorita — veza između korisnika i omiljenih djela
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS favoriti (
            id               INT AUTO_INCREMENT PRIMARY KEY,
            korisnik_id      INT NOT NULL,
            djelo_id         VARCHAR(100) NOT NULL,
            izvor            VARCHAR(20)  NOT NULL,
            datum_dodavanja  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (korisnik_id) REFERENCES korisnici(id) ON DELETE CASCADE,
            UNIQUE KEY uq_fav (korisnik_id, djelo_id, izvor)
        )
    ");

    // tablica newsletter pretplatnika
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS newsletter (
            id               INT AUTO_INCREMENT PRIMARY KEY,
            email            VARCHAR(150) NOT NULL UNIQUE,
            datum_pretplate  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            aktivan          TINYINT(1) NOT NULL DEFAULT 1
        )
    ");

    // tablica kontakt poruka primljenih putem forme
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kontakt_poruke (
            id           INT AUTO_INCREMENT PRIMARY KEY,
            ime          VARCHAR(100) NOT NULL,
            email        VARCHAR(150) NOT NULL,
            poruka       TEXT NOT NULL,
            datum_slanja DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            procitano    TINYINT(1) NOT NULL DEFAULT 0
        )
    ");

    // tablica vijesti koje admin objavljuje na stranici
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS vijesti (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            naslov     VARCHAR(255) NOT NULL,
            tekst      TEXT NOT NULL,
            slika      VARCHAR(255) DEFAULT NULL,
            datum      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            autor_id   INT DEFAULT NULL,
            FOREIGN KEY (autor_id) REFERENCES korisnici(id) ON DELETE SET NULL
        )
    ");

    // kreiranje admin korisnika ako već ne postoji
    $adminPostoji = $pdo->query("SELECT id FROM korisnici WHERE email = 'admin@monetica.hr'")->fetch();
    if (!$adminPostoji) {
        // hashiranje lozinke prije unosa
        $lozinka = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("
            INSERT INTO korisnici (ime, prezime, email, lozinka, uloga)
            VALUES ('Admin', 'Monetica', 'admin@monetica.hr', ?, 'admin')
        ")->execute([$lozinka]);
        echo "<p style='color:green'>Admin korisnik kreiran.</p>";
    } else {
        echo "<p style='color:orange'>⚠ Admin korisnik već postoji.</p>";
    }

    // kreiranje testnog korisnika za razvoj
    $korisnikPostoji = $pdo->query("SELECT id FROM korisnici WHERE email = 'korisnik@test.hr'")->fetch();
    if (!$korisnikPostoji) {
        $lozinka = password_hash('test123', PASSWORD_DEFAULT);
        $pdo->prepare("
            INSERT INTO korisnici (ime, prezime, email, lozinka, uloga)
            VALUES ('Testni', 'Korisnik', 'korisnik@test.hr', ?, 'korisnik')
        ")->execute([$lozinka]);
        echo "<p style='color:green'>Testni korisnik kreiran.</p>";
    }

    // unos primjera vijesti ako tablica još nije popunjena
    $brVijesti = $pdo->query("SELECT COUNT(*) FROM vijesti")->fetchColumn();
    if ($brVijesti == 0) {
        $pdo->exec("
            INSERT INTO vijesti (naslov, tekst, slika, datum) VALUES
            (
                'Izložba članova Monetice u Galeriji Klovićevi dvori',
                'S ponosom objavljujemo da će radovi naših članova biti izloženi u prestižnoj Galeriji Klovićevi dvori od 15. do 30. lipnja. Ovo je izvanredna prilika za sve ljubitelje likovnih umjetnosti da se upoznaju s najboljim radovima naših umjestnika. Izložba će prikazati više od 50 djela različitih tehnika i stilova.',
                'https://images.unsplash.com/photo-1577083552431-6e5fd01988ec?w=600&q=80',
                NOW() - INTERVAL 3 DAY
            ),
            (
                'Radionica akvarela — prijavite se do kraja tjedna!',
                'Nastavnica Vesna Mikić organizira dvodnebnu radionicu akvarela za sve razine znanja. Radionica će se održati 10. i 11. lipnja u prostorima društva. Broj mjesta je ograničen na 15 polaznika, stoga pohitajte s prijavom! Materijal je osiguran.',
                'https://images.unsplash.com/photo-1460627390041-532a28402358?w=600&q=80',
                NOW() - INTERVAL 7 DAY
            ),
            (
                'Novo: Digitalna galerija na mrežnoj stranici',
                'Pokrenuli smo novu digitalnu galeriju koja prikazuje radove naših članova. Svaki član može prijaviti svoje radove za objavu. Uz to, galerija sada prikazuje i izbor svjetskih remek-djela iz javnih zbirki, kako biste se mogli inspirirati i usporediti stilove.',
                'https://images.unsplash.com/photo-1561214115-f2f134cc4912?w=600&q=80',
                NOW() - INTERVAL 14 DAY
            )
        ");
        echo "<p style='color:green'>✓ Primjeri vijesti uneseni.</p>";
    }

    // ispis završnog izvještaja
    echo "<hr style='margin:20px 0'>";
    echo "<h2 style='color:green'>Baza je uspješno postavljenja!</h2>";
    echo "<p>Sada možeš otvoriti: <a href='{$baseUrl}/index.php'>{$baseUrl}/index.php</a></p>";
    echo "<p>Admin prijava: <strong>admin@monetica.hr</strong> / <strong>admin123</strong></p>";
    echo "<p>Test korisnik: <strong>korisnik@test.hr</strong> / <strong>test123</strong></p>";
    echo "<p style='color:red'><strong>⚠ Obriši ili zaštiti setup.php nakon postavljanja!</strong></p>";

} catch (PDOException $e) {
    // ispiši poruku greške ako spajanje ili sql ne uspije
    echo "<h2 style='color:red'>Greška!</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Provjeri jesu li XAMPP MySQL i Apache pokrenuti.</p>";
}
