<?php

$url = "";
$login = "";
$password = "";
// Set all var below
include "config_webarchive_images.php";

if ($url[strlen($url) - 1] != "/") {
    $url = $url . "/";
}

$page = 1;
do {
    $apiUrl = $url . "wp-json/wp/v2/posts?page=" . $page . "&per_page=100&context=edit";
//$apiUrl = $url . "wp-json/wp/v2/posts/152";
    $page++;

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERPWD, $login . ":" . $password);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $return = curl_exec($ch);
    curl_close($ch);

    // Get post content
    $posts = json_decode($return, true, 512, JSON_THROW_ON_ERROR);
    foreach ($posts as $post) {
        $content = $post["content"]["raw"];
        $newContent = $content;
        if (strpos($content, "web.archive.org")) {
            //            echo "Web archive\n";
            echo $post["link"] . "\n";

            $re = '/srcs?e?t?="([^"]*\/\/web\.archive\.org\/web\/[^\/]*\/)(http[^"]*)/';
            preg_match_all($re, $content, $matchesWbImg, PREG_SET_ORDER, 0);
//            var_dump($matchesWbImg);
            foreach ($matchesWbImg as $matcheWbImg) {
                echo "Remove ";
                echo $matcheWbImg[1];
                echo "\n\n";
                $newContent = str_replace($matcheWbImg[1], "", $newContent);
            }

            $re = '/href="([^"]*\/\/web\.archive\.org\/web\/[^\/]*\/)(http[^"]*)/';
            preg_match_all($re, $content, $matcheWbLink, PREG_SET_ORDER, 0);
//            var_dump($matcheWbLink);

            if (isset($matcheWbLink[0])) {
                $re = '/<a.*' . str_replace(".", "\.", str_replace("/", "\/", $matcheWbLink[0][0])) . '".*>(.*)<\/a>/U';
                echo $re;
                preg_match_all($re, $content, $matchesfullLink, PREG_SET_ORDER, 0);
//                var_dump($matchesfullLink);
                foreach ($matchesfullLink as $matchefullLink) {
                    echo "Replace ";
                    echo $matchefullLink[0];
                    echo "\nby\n";
                    echo $matchefullLink[1];
                    echo "\n\n";
                    $newContent = str_replace($matchefullLink[0], $matchefullLink[1], $newContent);
                }
            }
        }
        if ($newContent != $content) {

            $ch_update = curl_init($url . "wp-json/wp/v2/posts/" . $post["id"]);
            curl_setopt($ch_update, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));

            curl_setopt($ch_update, CURLOPT_USERPWD, $login . ":" . $password);
            curl_setopt($ch_update, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch_update, CURLOPT_POSTFIELDS, json_encode([
                "content" => $newContent,
            ]));
            curl_setopt($ch_update, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($ch_update);
            curl_close($ch_update);

            echo "Update\n\n\n-----------\n";
        }
    }
} while (count($posts) == 100);


?>
