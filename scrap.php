<?php
// URL target
$url = "https://news.detik.com/berita/d-7603488/kata-pihak-dini-soal-3-hakim-pemvonis-bebas-ronald-tannur-ditangkap-kejagung";

// Mengambil konten HTML dari halaman target menggunakan cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$html = curl_exec($ch);
curl_close($ch);

// Periksa apakah konten berhasil diambil
if ($html === false) {
    die("Error: Gagal mengambil konten dari $url");
}

// Parsing HTML dengan DOMDocument
$dom = new DOMDocument;
libxml_use_internal_errors(true); // Mengabaikan kesalahan HTML yang tidak valid
$dom->loadHTML($html);
libxml_clear_errors();

// Menggunakan DOMXPath untuk mengekstrak elemen tertentu
$xpath = new DOMXPath($dom);

// Ekstrak judul berita
$titleNode = $xpath->query('//h1[contains(@class, "detail__title")]');
$title = $titleNode->length > 0 ? trim($titleNode[0]->nodeValue) : 'Judul tidak ditemukan';

// Ekstrak tanggal berita
$dateNode = $xpath->query('//div[contains(@class, "detail__date")]');
$date = $dateNode->length > 0 ? trim($dateNode[0]->nodeValue) : 'Tanggal tidak ditemukan';

// Ekstrak isi berita
$contentNodes = $xpath->query('//div[contains(@class, "detail__body-text")]/p');
$content = '';
foreach ($contentNodes as $paragraph) {
    $content .= "<p>" . trim($paragraph->nodeValue) . "</p>";
}

// Ekstrak semua link
$links = $xpath->query('//a[@href]');
$linkList = '';
if ($links->length > 0) {
    foreach ($links as $link) {
        if ($link instanceof DOMElement) {
            $href = $link->getAttribute('href');
            $text = trim($link->nodeValue);
            $linkList .= "<li><a href=\"$href\" target=\"_blank\">$text</a> - <small>($href)</small></li>";
        }
    }
}

// Tampilkan output dengan format HTML
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scraping Berita Detik</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            font-size: 24px;
            color: #333;
        }

        h3 {
            font-size: 18px;
            color: #666;
            margin-top: 20px;
        }

        p {
            line-height: 1.6;
            color: #333;
        }

        ul {
            padding-left: 20px;
        }

        li {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <h3><?php echo htmlspecialchars($date); ?></h3>
    <div>
        <?php echo $content; ?>
    </div>
    <h3>Semua Link di Halaman Ini:</h3>
    <ul>
        <?php echo $linkList; ?>
    </ul>
</body>

</html>