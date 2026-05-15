<?php

// cisti unos korisnika
function ocisti($tekst) {
    return htmlspecialchars(trim($tekst), ENT_QUOTES, 'UTF-8');
}

// skrati tekst na zadani broj znakova
function skratiTekst($tekst, $max = 150) {
    if (mb_strlen($tekst) <= $max) return $tekst;
    return mb_substr($tekst, 0, $max) . '...';
}

// formatiranje datuma
function formatirajDatum($datum) {
    $mjeseci = [
        1 => 'siječnja', 2 => 'veljače', 3 => 'ožujka',
        4 => 'travnja',  5 => 'svibnja', 6 => 'lipnja',
        7 => 'srpnja',   8 => 'kolovoza', 9 => 'rujna',
        10 => 'listopada', 11 => 'studenoga', 12 => 'prosinca'
    ];
    $ts = strtotime($datum);
    return date('j', $ts) . '. ' . $mjeseci[(int)date('n', $ts)] . ' ' . date('Y', $ts) . '.';
}

// pokušaj dobiti veću verziju slike (Guardian i slični mediji)
function povecajSlikuURL($url) {
    if (!$url) return $url;
    // Guardian: /NNN.jpg → /800.jpg
    $url = preg_replace('#/(\d{2,4})\.jpg(\?.*)?$#', '/800.jpg', $url);
    return $url;
}

// dohvat vijesti iz RSS feeda
function dohvatiRSS($url, $max = 6) {
    $vijesti = [];
    try {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Monetica/1.0)',
                CURLOPT_ENCODING       => 'gzip, deflate',
            ]);
            $sadrzaj = curl_exec($ch);
            curl_close($ch);
        } else {
            $ctx = stream_context_create(['http' => [
                'timeout'    => 10,
                'user_agent' => 'Mozilla/5.0 (compatible; Monetica/1.0)',
            ]]);
            $sadrzaj = @file_get_contents($url, false, $ctx);
        }

        if (!$sadrzaj) return $vijesti;

        $xml = simplexml_load_string($sadrzaj, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml) return $vijesti;

        $stavke = $xml->channel->item ?? [];
        $br = 0;
        foreach ($stavke as $stavka) {
            if ($br >= $max) break;

            // pokušaj izvući sliku iz media:content (Yahoo Media RSS namespace)
            $slika = '';
            try {
                $mediaNs = $stavka->children('http://search.yahoo.com/mrss/');
                if (!empty($mediaNs->content)) {
                    $mAttr = $mediaNs->content->attributes();
                    $slika = (string)($mAttr['url'] ?? '');
                }
            } catch (Exception $e) {}

            // fallback: enclosure tag
            if (!$slika && isset($stavka->enclosure)) {
                $eAttr = $stavka->enclosure->attributes();
                $tip   = (string)($eAttr['type'] ?? '');
                if (strpos($tip, 'image') !== false) {
                    $slika = (string)($eAttr['url'] ?? '');
                }
            }

            $slika = povecajSlikuURL($slika);

            $vijesti[] = [
                'naslov' => (string)$stavka->title,
                'opis'   => strip_tags((string)$stavka->description),
                'link'   => (string)$stavka->link,
                'datum'  => (string)$stavka->pubDate,
                'slika'  => $slika,
            ];
            $br++;
        }
    } catch (Exception $e) {
        // Ako RSS ne radi, vrati prazan niz — stranica će i dalje raditi
    }
    return $vijesti;
}

// pretvori putanju slike iz XML-a u ispravnu URL za <img src>
// — ako je već http(s) URL, vrati ga kakav jest
// — inače dodaj BASE_URL prefix (relativna putanja od korijena projekta)
function urlSlike($putanja) {
    if (empty($putanja)) return '';
    if (strncmp($putanja, 'http', 4) === 0) return $putanja;
    return BASE_URL . '/' . ltrim($putanja, '/');
}

// ─── XML — namespace konstante ───
const GAL_NS = 'http://monetica.hr/galerija';
const RAD_NS = 'http://monetica.hr/radionice';

// dohvat svih djela (gal:djelo) iz XML-a
function dohvatiDjelaXML($kategorija = null) {
    $putanja = __DIR__ . '/../data/galerija.xml';
    if (!file_exists($putanja)) return [];

    $xml = simplexml_load_file($putanja);
    if (!$xml) return [];

    $xml->registerXPathNamespace('gal', GAL_NS);
    $nodes = $xml->xpath('//gal:djelo');

    $djela = [];
    foreach ($nodes as $djelo) {
        $ch  = $djelo->children(GAL_NS);
        $kat = (string)$ch->kategorija;
        if ($kategorija && $kat !== $kategorija) continue;
        $djela[] = [
            'id'        => (string)$djelo['id'],
            'naslov'    => (string)$ch->naslov,
            'autor'     => (string)$ch->autor,
            'tehnika'   => (string)$ch->tehnika,
            'godina'    => (string)$ch->godina,
            'dimenzije' => (string)$ch->dimenzije,
            'kategorija'=> $kat,
            'slika'     => (string)$ch->slika,
            'opis'      => (string)$ch->opis,
        ];
    }
    return $djela;
}

// dohvat radionica (rad:radionica) iz XML-a
function dohvatiRadioniceXML() {
    $putanja = __DIR__ . '/../data/galerija.xml';
    if (!file_exists($putanja)) return [];

    $xml = simplexml_load_file($putanja);
    if (!$xml) return [];

    $xml->registerXPathNamespace('rad', RAD_NS);
    $nodes = $xml->xpath('//rad:radionica');

    $radionice = [];
    foreach ($nodes as $r) {
        $ch = $r->children(RAD_NS);
        $radionice[] = [
            'id'     => (string)$r['id'],
            'naslov' => (string)$ch->naslov,
            'opis'   => (string)$ch->opis,
            'slika'  => (string)$ch->slika,
        ];
    }
    return $radionice;
}

// dohvat kategorija iz XML-a
function dohvatiKategorijeXML() {
    $kategorije = array_unique(array_column(dohvatiDjelaXML(), 'kategorija'));
    sort($kategorije);
    return $kategorije;
}

// spremanje novog djela u XML datoteku
function spremiDjeloXML($djelo) {
    $putanja = __DIR__ . '/../data/galerija.xml';
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->load($putanja);

    $maxId = 0;
    foreach ($dom->getElementsByTagNameNS(GAL_NS, 'djelo') as $d) {
        $id = (int)$d->getAttribute('id');
        if ($id > $maxId) $maxId = $id;
    }

    $novoDjelo = $dom->createElementNS(GAL_NS, 'gal:djelo');
    $novoDjelo->setAttribute('id', $maxId + 1);

    foreach (['naslov','autor','tehnika','godina','dimenzije','kategorija','slika','opis'] as $f) {
        $el = $dom->createElementNS(GAL_NS, "gal:{$f}");
        $el->appendChild($dom->createTextNode($djelo[$f] ?? ''));
        $novoDjelo->appendChild($el);
    }

    $dom->documentElement->appendChild($novoDjelo);
    $dom->formatOutput = true;
    return $dom->save($putanja);
}

// uređivanje postojećeg djela u XML-u
function urediDjeloXML($id, $podaci) {
    $putanja = __DIR__ . '/../data/galerija.xml';
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->load($putanja);

    foreach ($dom->getElementsByTagNameNS(GAL_NS, 'djelo') as $djelo) {
        if ($djelo->getAttribute('id') !== (string)$id) continue;

        foreach (['naslov','autor','tehnika','godina','dimenzije','kategorija','opis'] as $f) {
            $els = $djelo->getElementsByTagNameNS(GAL_NS, $f);
            if ($els->length > 0) $els->item(0)->textContent = $podaci[$f] ?? '';
        }
        if (!empty($podaci['slika'])) {
            $els = $djelo->getElementsByTagNameNS(GAL_NS, 'slika');
            if ($els->length > 0) $els->item(0)->textContent = $podaci['slika'];
        }
        break;
    }

    $dom->formatOutput = true;
    return $dom->save($putanja);
}

// brisanje djela iz XML-a
function izbrisiDjeloXML($id) {
    $putanja = __DIR__ . '/../data/galerija.xml';
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->load($putanja);

    $cvor = null;
    foreach ($dom->getElementsByTagNameNS(GAL_NS, 'djelo') as $djelo) {
        if ($djelo->getAttribute('id') === (string)$id) { $cvor = $djelo; break; }
    }
    if ($cvor) $cvor->parentNode->removeChild($cvor);

    $dom->formatOutput = true;
    return $dom->save($putanja);
}

// provjeri je li djelo u favoritima korisnika
function jeUFavoritima($pdo, $korisnikId, $djeloId, $izvor) {
    $stmt = $pdo->prepare("SELECT id FROM favoriti WHERE korisnik_id = ? AND djelo_id = ? AND izvor = ?");
    $stmt->execute([$korisnikId, $djeloId, $izvor]);
    return $stmt->fetch() !== false;
}
