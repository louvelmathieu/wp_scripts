<?php

// Add this function to your function.php
add_filter('the_content', 'add_faq_snippets', 1);
function add_faq_snippets($content)
{
    // Check if we're inside the main loop in a single Post.
    if (is_singular() && in_the_loop() && is_main_query()) {
        $reFaq = '/<h2.*<\/h2>/U';
        preg_match_all($reFaq, $content, $matchesTitle, PREG_SET_ORDER, 0);

        $faqStr = "";
        foreach ($matchesTitle as $title) {
            if (strip_tags($title[0]) == "FAQ") {
                $faqStr = $title[0];
            }
        }

        if ($faqStr != "") {
            $subText = substr($content, strpos($content, $faqStr));
        } else {
            return $content;
        }

        $reQ = '/<h3.*<\/h3>/U';
        preg_match_all($reQ, $subText, $matchesQuestion, PREG_SET_ORDER, 0);

        $faq = [];
        for ($i = 0; $i < count($matchesQuestion); $i++) {
            $question = strip_tags($matchesQuestion[$i][0]);
            if ($i == count($matchesQuestion) - 1) {
                $answer = trim(strip_tags(substr($subText, strpos($subText, $matchesQuestion[$i][0]) + strlen($matchesQuestion[$i][0]))));
            } else {
                $pQ = strpos($subText, $matchesQuestion[$i][0]);
                $pN = strpos($subText, $matchesQuestion[$i + 1][0]);
                $answer = trim(strip_tags(substr($subText, $pQ + strlen($matchesQuestion[$i][0]), $pN - ($pQ + strlen($matchesQuestion[$i][0])))));
            }
            $faq[] = [
                'q' => $question,
                'a' => $answer,
            ];
        }

        if (count($faq) > 0) {
            $snippets = [
                "@context" => "https://schema.org",
                "@type" => "FAQPage",
                "mainEntity" => [],
            ];
            foreach ($faq as $f) {
                $snippets["mainEntity"][] = [
                    "@type" => "Question",
                    "name" => $f['q'],
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => $f['a'],
                    ]
                ];
            }
            return $content . '<script type="application/ld+json">' . json_encode($snippets) . '</script>';
        }
    }
    return $content;
}
