<?php


// Chat GPT API key : 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
// https://www.commentcoder.com/api-chatgpt/


require "vendor/autoload.php";

use PHPHtmlParser\Dom;

$slug = "/mariage-normandie-manche-robedemariee-lauredesagazan-preparatifs-decoration-photographe-oisingormally-blog-blogueuse-mariage/";
//$category = 110; // "Anniversaires de mariage";
//$category = 134; // "shooting-d'inspiration";
$category = 128; // "shooting-de-mariages";
//$category = 123; // "robe de marié";

$dom = new Dom;
$dom->loadFromUrl("https://old.donnemoitamain.fr" . $slug);

$title = $dom->find(".eltdf-current")->text;
$a = $dom->find('.eltdf-post-text-main');

if (count($dom->find('img')) == 0) {
    echo "No image, abort";
    die();
}

$site_dest_url = "https://www.donnemoitamain.fr/";
$site_dest_login = 'edition.sites+donnemoitamain@gmail.com';
$site_dest_password = 'apWO vRx6 lF8P RrVV xzPM vrFj';

$innerHtml = preg_replace('/data-safe-src=".*?"/', '', $a[0]->innerHtml);

$ch = curl_init($site_dest_url . "wp-json/wp/v2/media/?order=desc&orderby=date");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_USERPWD, $site_dest_login . ":" . $site_dest_password);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$return = curl_exec($ch);
$response = json_decode($return);

$previous_image = $response[0]->id;
echo "Last image";
var_dump($previous_image);

$ch = curl_init($site_dest_url . "wp-json/wp/v2/posts/");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_USERPWD, $site_dest_login . ":" . $site_dest_password);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, '{
    "content": "' . str_replace('"', '\"', $innerHtml) . '",
    "slug": "' . $slug . '",
    "title": "' . $title . '",
    "format": "' . ($category == 110 || $category == 134 || $category == 128 ? "gallery" : "standard"). '",
    "categories": [' . $category . ']
}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
// Issue with Yoast : https://github.com/Yoast/wordpress-seo/issues/17721
$return = curl_exec($ch);
$response = json_decode($return);
curl_close($ch);

$post_id = $response->id;

$ch = curl_init($site_dest_url . "wp-json/wp/v2/media/?order=desc&orderby=date&per_page=100");
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_USERPWD, $site_dest_login . ":" . $site_dest_password);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$return = curl_exec($ch);
$response = json_decode($return);

$main_img = '';
$str_gallery = '';
foreach ($response as $r) {
    if ($r->id > $previous_image) {
        $str_gallery = $r->id . "," . $str_gallery ;
        $main_img = $r->id;
    }
}
echo $str_gallery;
echo "\n";

if (strlen($str_gallery) > 0) {
    $str_gallery = substr($str_gallery, 0, -1);
}

while (count($img = $dom->find('img')) > 0) {
    $img[0]->delete();
    unset($img[0]);
}

while (count($img = $dom->find('picture')) > 0) {
    $img[0]->delete();
    unset($img[0]);
}

while (count($img = $dom->find('figure')) > 0) {
    $img[0]->delete();
    unset($img[0]);
}

$a = $dom->find('.eltdf-post-text-main');


// Regex title pour avoir les prenom + template de titre
// Tous le h1 en sous titre
// Regex pour tags mariage

// Regex Ils ont participé au shooting pour passer sous la gallerie + regex non du photographe


$before = '[vc_row][vc_column][vc_empty_space][eltdf_section_title dots="no" position="" title_tag="h1" disable_break_words="no" text_tag="" text_font_weight="" image="1005" title="Shooting de Mariage : TODO" text="' . $title . '"][/vc_column][/vc_row][vc_row][vc_column][vc_empty_space height="16px"][vc_raw_html]';
$after = '[/vc_raw_html][vc_empty_space][/vc_column][/vc_row][vc_row][vc_column][eltdf_image_gallery type="masonry" enable_image_shadow="no" image_behavior="lightbox" number_of_columns="two" space_between_items="tiny" images="' . $str_gallery . '"][vc_empty_space][vc_raw_html]';
$end = '[/vc_raw_html][vc_empty_space height="16px"][eltdf_section_title dots="no" position="" title_tag="" disable_break_words="no" text_tag="" text_font_weight="" image="1005" title="Mariages bohèmes publiés sur le blog" text="Nos autres shootings de mariages bohème"][vc_empty_space height="16px"][eltdf_blog_list type="standard" number_of_columns="four" space_between_items="medium" orderby="rand" order="DESC" image_size="theaisle_elated_image_landscape" post_info_date="no" number_of_posts="4" tag="mariage-boheme" excerpt_length="0"][/vc_column][/vc_row]';

$html = trim($a[0]->innerHtml);
if (strpos($html, "<h2") === 0) {
    $h2 = $dom->find('h2');
    $h2[0]->delete();
    unset($h2[0]);
    $html = trim($a[0]->innerHtml);
}

$html_end = '';
$pos = stripos($html, '<h2 class="wp-block-heading">Ils ont participé au shooting</h2>');
if ($pos !== false) {
    $html_end = substr($html, $pos);
    $html = substr($html, 0, $pos);
} else {
    $pos = stripos($html, '<h2 class="wp-block-heading">LA LISTE DES PRESTATAIRES</h2>');
    if ($pos !== false) {
        $html_end = substr($html, $pos);
        $html = substr($html, 0, $pos);
    } else {
        $pos = stripos($html, '<h2 class="wp-block-heading">LES PRESTATAIRES DU MARIAGE</h2>');
        if ($pos !== false) {
            $html_end = substr($html, $pos);
            $html = substr($html, 0, $pos);
        } else {
            $pos = stripos($html, '<h2 class="wp-block-heading">La liste de vos prestataires</h2>');
            if ($pos !== false) {
                $html_end = substr($html, $pos);
                $html = substr($html, 0, $pos);
            }
        }
    }
}
var_dump($pos);
var_dump($html);
var_dump($html_end);

$ch = curl_init($site_dest_url . "wp-json/wp/v2/posts/" . $post_id);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_USERPWD, $site_dest_login . ":" . $site_dest_password);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');


if ($category == 110 || $category == 134 || $category == 128) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{
    "content": "' . str_replace('"', '\"', $before . base64_encode($html) . $after . base64_encode($html_end) . $end) . '",
    "featured_media": ' . $main_img . '
}');
} else {
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{
        "content": "' . str_replace('"', '\"', $html) . '",
        "featured_media": ' . $main_img . '
    }');
}
//    "content": "' . str_replace('"', '\"', $before.base64_encode($a[0]->innerHtml).$after). '",
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
// Issue with Yoast : https://github.com/Yoast/wordpress-seo/issues/17721
$return = curl_exec($ch);
$response = json_decode($return);
curl_close($ch);

if ($category == 110 || $category == 134 || $category == 128) {
    $pdo = new PDO("mysql:host=lm45501-001.eu.clouddb.ovh.net;port=35153;dbname=dmtm", 'dmtm', '3Co0z54oCr44');
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    $stmt = $pdo->prepare("INSERT INTO `wp42v2_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, "eltdf_show_title_area_blog_meta", "no"]);
    $stmt = $pdo->prepare("INSERT INTO `wp42v2_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, "eltdf_blog_single_sidebar_layout_meta", "no-sidebar"]);
    $stmt = $pdo->prepare("INSERT INTO `wp42v2_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, "eltdf_page_background_image_meta", "https://www.donnemoitamain.fr/wp-content/uploads/2024/02/Screenshot-from-2024-02-18-16-41-55.png"]);
}
