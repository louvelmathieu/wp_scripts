<?php

$url = "";
$login = "";
$password = "";
// Set all var below
include "config_download_images.php";

if ($url[strlen($url) - 1] != "/") {
    $url = $url . "/";
}

// Get image from wayback machine
$urlWb = "https://web.archive.org/web/timemap/json?url=" . urlencode($url) . "&matchType=prefix&collapse=urlkey&output=json&fl=original%2Cmimetype%2Ctimestamp%2Cendtimestamp%2Cgroupcount%2Cuniqcount&filter=!statuscode%3A%5B45%5D..&limit=10000&_=1687599735052";
$ch = curl_init($urlWb);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$return = curl_exec($ch);
curl_close($ch);
$urlsOnWb = json_decode($return);

$page = 1;
do {
    $apiUrl = $url . "wp-json/wp/v2/posts?page=" . $page . "&per_page=100";
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
//    $post = json_decode($return, true, 512, JSON_THROW_ON_ERROR);
    $posts = json_decode($return, true, 512, JSON_THROW_ON_ERROR);
    foreach ($posts as $post) {
        $content = $post["content"]["rendered"];

        // Extract all images
        $regexImage = '/<img.*src="(' . str_replace(".", "\.", str_replace("/", "\/", $url)) . 'wp-content\/uploads\/.*\.jpg)"/';
        preg_match_all($regexImage, $content, $matcheImages, PREG_SET_ORDER, 0);

        // test all images
        foreach ($matcheImages as $matcheImage) {
            echo $matcheImage[1] . " : ";
            if (@file_get_contents($matcheImage[1]) == false) {
                echo "Dont exist\n";
                // Test on wayback machine
                if (($r = @file_get_contents("https://web.archive.org/web/" . date("Ymd") . "040930if_/" . $matcheImage[1])) == false) {
                    $p = strrpos($matcheImage[1], "/");
                    $imageNameOnly = substr($matcheImage[1], $p);

                    var_dump($imageNameOnly);
                    echo "Dont exist on Webbackmachine\n";
                    if (strpos($imageNameOnly, "-") !== false) {
                        $end = strrpos($matcheImage[1], "-");
                    } else {
                        $end = strrpos($matcheImage[1], ".");
                    }

                    $start = strpos($matcheImage[1], "www.") + 4;
                    $imageFullPath = substr($matcheImage[1], $start, $end - $start);
                    echo $imageFullPath . "\n";
                    $found = 0;
                    foreach ($urlsOnWb as $urlOnWb) {
                        $found = findAndDl($urlOnWb[0], $imageFullPath, $matcheImage[1], $found);
                    }

                    if ($found == 0) {
                        echo $imageNameOnly . "\n";
                        foreach ($urlsOnWb as $urlOnWb) {
                            $found = findAndDl($urlOnWb[0], $imageNameOnly, $matcheImage[1], $found);
                        }
                        if ($found == 0) {
                            echo "Cant find on WayBack " . $matcheImage[1] . " : " . $imageNameOnly . "\n";
                        }
                    }
                }
            } else {
                echo "Exist\n";
            }
        }
    }
} while (count($posts) == 100);

function findAndDl($urlOnWb, $toFind, $matcheImage, $found)
{
    if (strlen($toFind) < 3) {
        return $found;
    }
    if (strpos($urlOnWb, "/wp-content/uploads/") && strpos($urlOnWb, $toFind)) {
        echo "Image found : " . $urlOnWb . "\n";
        if (($r1 = file_get_contents("https://web.archive.org/web/" . date("Ymd") . "040930if_/" . $urlOnWb)) == false) {
            echo "Impossible de trouver l'image\n";
        } else {
            $folders = explode("/", substr($matcheImage, strpos($matcheImage, "wp-content/uploads/")));
            $startPath = "";
            for ($i = 0; $i <= count($folders) - 2; $i++) {
                if (!is_dir($startPath . $folders[$i])) {
                    mkdir($startPath . $folders[$i]);
                }
                $startPath .= $folders[$i] . "/";
            }
            $found++;
            if ($found > 1) {
                file_put_contents("./" . $startPath . str_replace(".jpg", "-" . $found . ".jpg", $folders[count($folders) - 1]), $r1);
                echo "\n###\n###\nImage double\n";
                echo $startPath . str_replace(".jpg", "-" . $found . ".jpg", $folders[count($folders) - 1]) . "\n";
                echo "###\n###\n";
            } else {
                echo "Image download\n";
                file_put_contents("./" . $startPath . $folders[count($folders) - 1], $r1);
            }
        }
    }
    return $found;
}

?>
