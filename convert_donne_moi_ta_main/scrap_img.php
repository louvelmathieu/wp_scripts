<?php
// Dossier où tu veux enregistrer les images
$saveDir = __DIR__ . "/images/";
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0777, true);
}

for ($i = 1; $i <= 56; $i++) {
    // URL source
    $url = "https://www.lasoeurdelamariee.com/wp-content/uploads/2017/11/Shooting-inspiration-automne-la-soeur-de-la-mariee-blog-mariage-{$i}.jpg";

    // Nouveau nom de fichier (remplace dans l’URL)
    $filename = basename(str_replace("la-soeur-de-la-mariee", "donne-moi-ta-main", $url));

    // Chemin complet de destination
    $savePath = $saveDir . $filename;

    // Téléchargement de l’image
    $imageData = @file_get_contents($url);
    if ($imageData !== false) {
        file_put_contents($savePath, $imageData);
        echo "Image $i téléchargée et enregistrée : $filename\n";
    } else {
        echo "Échec du téléchargement de l’image $i\n";
    }
}
?>
