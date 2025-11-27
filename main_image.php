<?php

//'url' => 'https://mesdouceurs.fr/',
//            'login' => 'admin@mesdouceurs.fr',
//            'password' => 'TCPk h3YJ 9qQ7 GbaT aAeO 152r',


$site_source_url = "https://old.donnemoitamain.fr/";
$site_source_login = 'admin@donnemoitamain.fr';
$site_source_password = 'en6l 1VZ6 EdEq r8QE XqZI g5WH';

$site_dest_url = "https://www.donnemoitamain.fr/";
//$site_dest_login = 'admin@donnemoitamain.fr';
//$site_dest_password = 'LUMb nTBC fRd8 u424 XGI0 F0PL';

$site_dest_login = 'edition.sites+donnemoitamain@gmail.com';
$site_dest_password = 'apWO vRx6 lF8P RrVV xzPM vrFj';

$page = 1;
$posts_source = [];
do {
    $ch = curl_init($site_source_url . "wp-json/wp/v2/posts?post_status=publish&per_page=100&page=" . $page);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERPWD, $site_source_login . ":" . $site_source_password);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $return = curl_exec($ch);
    curl_close($ch);
    $posts = json_decode($return, true, 512, JSON_THROW_ON_ERROR);
    $posts_source = array_merge($posts_source, $posts);
    $page++;
} while (count($posts_source) == 100);
var_dump(count($posts_source));

$page = 1;
$posts_dest = [];
do {
    $ch = curl_init($site_dest_url . "wp-json/wp/v2/posts?post_status=publish&per_page=100&page=" . $page);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERPWD, $site_dest_login . ":" . $site_dest_password);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $return = curl_exec($ch);
    curl_close($ch);
    $posts = json_decode($return, true, 512, JSON_THROW_ON_ERROR);
    $posts_dest = array_merge($posts_dest, $posts);
    $page++;
} while (count($posts_dest) == 100);
var_dump(count($posts_dest));

foreach ($posts_source as $post_source) {

    foreach ($posts_dest as $post_dest) {
        if ($post_source["title"]["rendered"] == $post_dest["title"]["rendered"]) {
            file_put_contents("./logs.txt", json_encode($post_dest));
            echo $post_source["title"]["rendered"] . "\n";
            if (isset($post_source["yoast_head_json"]) && isset($post_source["yoast_head_json"]["og_image"])) {
                echo $post_source["yoast_head_json"]["og_image"][0]["url"] . "\n";
            } else {
                continue;
            }
            if (isset($post_dest["yoast_head_json"]) && isset($post_dest["yoast_head_json"]["og_image"])) {
                echo $post_dest["yoast_head_json"]["og_image"][0]["url"] . "\n";
                continue;
            }

            $img = file_get_contents($post_source["yoast_head_json"]["og_image"][0]["url"]);

            $names = explode("/", $post_source["yoast_head_json"]["og_image"][0]["url"]);
            // Upload image
            $ch = curl_init($site_dest_url . "wp-json/wp/v2/media");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: image/jpeg', 'Content-Disposition: attachment; filename="' . $names[count($names) - 1] . '"'));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_USERPWD, $site_dest_login . ":" . $site_dest_password);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $img);
            $return = curl_exec($ch);
            $r = json_decode($return, true);
            curl_close($ch);

            if (!isset($r['id'])) {
                var_dump("Error upload");
            } else {
                // Update post
                $ch_update = curl_init($site_dest_url . "wp-json/wp/v2/posts/" . $post_dest['id']);
                curl_setopt($ch_update, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json'
                ));

                curl_setopt($ch_update, CURLOPT_USERPWD, $site_dest_login . ":" . $site_dest_password);
                curl_setopt($ch_update, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch_update, CURLOPT_POSTFIELDS, '{
                        "featured_media": ' . $r['id'] . '
                    }');
                curl_setopt($ch_update, CURLOPT_RETURNTRANSFER, TRUE);
                // Issue with Yoast : https://github.com/Yoast/wordpress-seo/issues/17721
                $return = curl_exec($ch_update);
                curl_close($ch_update);


                $ch_update = curl_init($site_dest_url . "wp-json/wp/v2/posts/" . $post_dest['id']);
                curl_setopt($ch_update, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json'
                ));

                curl_setopt($ch_update, CURLOPT_USERPWD, $site_dest_login . ":" . $site_dest_password);
                curl_setopt($ch_update, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch_update, CURLOPT_POSTFIELDS, '{
                        "featured_media": ' . $r['id'] . '
                    }');
                curl_setopt($ch_update, CURLOPT_RETURNTRANSFER, TRUE);
                // Issue with Yoast : https://github.com/Yoast/wordpress-seo/issues/17721
                $return = curl_exec($ch_update);
                curl_close($ch_update);
            }
        }
    }
}
