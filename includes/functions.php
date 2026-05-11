<?php

function ocisti($tekst) {
    return htmlspecialchars(trim($tekst), ENT_QUOTES, 'UTF-8');
}

function skratiTekst($tekst, $max = 150) {
    if (mb_strlen($tekst) <= $max) return $tekst;
    return mb_substr($tekst, 0, $max) . '...';
}

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
            $vijesti[] = [
                'naslov' => (string)$stavka->title,
                'opis'   => strip_tags((string)$stavka->description),
                'link'   => (string)$stavka->link,
                'datum'  => (string)$stavka->pubDate,
            ];
            $br++;
        }
    } catch (Exception $e) {
    }
    return $vijesti;
}

function dohvatiDjelaXML($kategorija = null) {
    $putanja = __DIR__ . '/../data/galerija.xml';
    if (!file_exists($putanja)) return [];

    $xml = simplexml_load_file($putanja);
    if (!$xml) return [];

    $djela = [];
    foreach ($xml->djelo as $djelo) {
        if ($kategorija && (string)$djelo->kategorija !== $kategorija) continue;
        $djela[] = [
            'id'        => (string)$djelo['id'],
            'naslov'    => (string)$djelo->naslov,
            'autor'     => (string)$djelo->autor,
            'tehnika'   => (string)$djelo->tehnika,
            'godina'    => (string)$djelo->godina,
            'dimenzije' => (string)$djelo->dimenzije,
            'kategorija'=> (string)$djelo->kategorija,
            'slika'     => (string)$djelo->slika,
            'opis'      => (string)$djelo->opis,
        ];
    }
    return $djela;
}

function dohvatiKategorijeXML() {
    $djela = dohvatiDjelaXML();
    $kategorije = array_unique(array_column($djela, 'kategorija'));
    sort($kategorije);
    return $kategorije;
}

function spremiDjeloXML($djelo) {
    $putanja = __DIR__ . '/../data/galerija.xml';
    $xml = simplexml_load_file($putanja);

    $maxId = 0;
    foreach ($xml->djelo as $d) {
        $id = (int)$d['id'];
        if ($id > $maxId) $maxId = $id;
    }

    $novo = $xml->addChild('djelo');
    $novo->addAttribute('id', $maxId + 1);
    $novo->addChild('naslov',    htmlspecialchars($djelo['naslov']));
    $novo->addChild('autor',     htmlspecialchars($djelo['autor']));
    $novo->addChild('tehnika',   htmlspecialchars($djelo['tehnika']));
    $novo->addChild('godina',    htmlspecialchars($djelo['godina']));
    $novo->addChild('dimenzije', htmlspecialchars($djelo['dimenzije']));
    $novo->addChild('kategorija',htmlspecialchars($djelo['kategorija']));
    $novo->addChild('slika',     htmlspecialchars($djelo['slika']));
    $novo->addChild('opis',      htmlspecialchars($djelo['opis']));

    $dom = dom_import_simplexml($xml)->ownerDocument;
    $dom->formatOutput = true;
    return $dom->save($putanja);
}

function urediDjeloXML($id, $podaci) {
    $putanja = __DIR__ . '/../data/galerija.xml';
    $xml = simplexml_load_file($putanja);

    foreach ($xml->djelo as $djelo) {
        if ((string)$djelo['id'] === (string)$id) {
            $djelo->naslov    = htmlspecialchars($podaci['naslov']);
            $djelo->autor     = htmlspecialchars($podaci['autor']);
            $djelo->tehnika   = htmlspecialchars($podaci['tehnika']);
            $djelo->godina    = htmlspecialchars($podaci['godina']);
            $djelo->dimenzije = htmlspecialchars($podaci['dimenzije']);
            $djelo->kategorija= htmlspecialchars($podaci['kategorija']);
            $djelo->opis      = htmlspecialchars($podaci['opis']);
            if (!empty($podaci['slika'])) {
                $djelo->slika = htmlspecialchars($podaci['slika']);
            }
            break;
        }
    }

    $dom = dom_import_simplexml($xml)->ownerDocument;
    $dom->formatOutput = true;
    return $dom->save($putanja);
}

function izbrisiDjeloXML($id) {
    $putanja = __DIR__ . '/../data/galerija.xml';
    $xml = simplexml_load_file($putanja);

    $dom = dom_import_simplexml($xml)->ownerDocument;
    foreach ($xml->djelo as $djelo) {
        if ((string)$djelo['id'] === (string)$id) {
            $node = dom_import_simplexml($djelo);
            $node->parentNode->removeChild($node);
            break;
        }
    }
    $dom->formatOutput = true;
    return $dom->save($putanja);
}

function jeUFavoritima($pdo, $korisnikId, $djeloId, $izvor) {
    $stmt = $pdo->prepare("SELECT id FROM favoriti WHERE korisnik_id = ? AND djelo_id = ? AND izvor = ?");
    $stmt->execute([$korisnikId, $djeloId, $izvor]);
    return $stmt->fetch() !== false;
}
