<?php
/**
 * Script d'extraction d'emails depuis une liste d'URLs
 * Version améliorée : recherche aussi sur /contact si aucun email trouvé
 *
 * Usage: php extract_emails.php
 */

// Configuration
$timeout = 10;
$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

// Pages alternatives à vérifier si aucun email trouvé
$alternativePages = [
    '/contact',
    '/contact/',
    '/contact-us',
    '/contact-us/',
    '/contactez-nous',
    '/contactez-nous/',
    '/nous-contacter',
    '/nous-contacter/',
    '/a-propos',
    '/a-propos/',
    '/about',
    '/about/',
    '/about-us',
    '/about-us/',
];

// Liste des URLs
$urls = [
    "http://balthusaccordsdivins.fr/",
    "http://debocollection.com/",
    "http://www.agathetcolette.com/",
    "http://www.le-mariage-des-marioche.fr/",
    "http://www.loveisfresh.com/a-propos-de-la-blogueuse/",
    "https://adeuxmains-events.fr/a-propos/",
    "https://aeternam-event.com/",
    "https://alice-marty.com/",
    "https://ambrosinoalisea.fr/",
    "https://ameleventparis.fr/",
    "https://ameliedwedding.com/",
    "https://aneiaevents.com/",
    "https://angegimenez.com/",
    "https://aromatique-fleuriste.com/publications/",
    "https://artmaniafleur.fr/",
    "https://atelier-julie-bihler.fr/presse/",
    "https://audreycarnoy.fr/",
    "https://augustine-mariagealacampagne.fr/",
    "https://aymee.com/collections/collection-chaussure-mariage",
    "https://belesprit.fr/",
    "https://belleaurorebijoux.com/",
    "https://belleaurorebijoux.com/pages/a-propos",
    "https://blushfashion.boutique/pages/featured-in",
    "https://boldaslove-weddings.fr/",
    "https://boumetc.fr/",
    "https://brindevanille.fr/",
    "https://bulledevie.net/",
    "https://bullesetconfettis.fr/",
    "https://burdimilion.com/",
    "https://charlotteviguier.fr/",
    "https://chateaudefajac.com/",
    "https://cheveuxdange.webnode.fr/",
    "https://citronpavot-traiteur.com/",
    "https://claramartignyphotographie.fr/",
    "https://clemencegadot.com/index.php/publications/",
    "https://cmeventcoaching.com/",
    "https://corneloup-paris.fr/",
    "https://cymbeline.com/revuedepresse/la-soeur-de-la-mariee-fevrier-2024/",
    "https://damouretdevenements.fr/",
    "https://dansetousstyles.be/",
    "https://delamour-danslair.com/",
    "https://delphineclosse.com/",
    "https://ecumeevents.com/en/home/",
    "https://effetnoeudpap.com/pages/on-parle-de-nous",
    "https://eirinphotography.com/en/",
    "https://eleonoregarat.fr/",
    "https://elsagary.fr/",
    "https://elvengardenflower.fr/",
    "https://encre-les-lignes.fr/",
    "https://enjoy-evenements.fr/",
    "https://enpetitcomite76.fr/mes-realisations-266/",
    "https://etsionsemariait.com/",
    "https://evamartinez.fr/",
    "https://evjf.org/my-evjf-presse/",
    "https://faire-part-selection.fr/",
    "https://fannycalligraphie.com/presse/",
    "https://gael-hidalgo.com/",
    "https://harmoniephotography.fr/infos/",
    "https://hocdie.com/wedding-planner-toulouse/",
    "https://idyle-weddingplanner.com/",
    "https://ilkeys.fr/",
    "https://imaginaridesign.fr/",
    "https://ingridthierry.fr/",
    "https://insiemeceremonies.com/",
    "https://instant-cocktail.com/",
    "https://izii-inspirationsjolies.com/parutions-presse/",
    "https://jourjetcie.com/",
    "https://kaligrafia.fr/",
    "https://katerynaphotos.com/a-propos/",
    "https://kywwiefilms.fr/",
    "https://la-reine-rit.com/",
    "https://la-seve.fr/a-propos/",
    "https://lacasaflorale.fr/",
    "https://ladum-atelier.com/odelia-maxime",
    "https://lafabriqueasachets.com/espace-presse-partenariats/",
    "https://lafabriquedesinstants.com/",
    "https://lamaisonchanteclair.fr/",
    "https://lanadra.co/",
    "https://lapetiteetincelleevent.fr/",
    "https://lapieceur.com/",
    "https://lartquipousse.com/presse/",
    "https://latelierbleuet.com/",
    "https://latelierdunsouhait.fr/2018/01/03/presse/",
    "https://lauracbphotographie.com/",
    "https://laurajaffret.com/",
    "https://lauralericheevents.com/",
    "https://lauremariage.com/",
    "https://laurinebailly.fr/",
    "https://lebonheurcommenceici.com/",
    "https://leclosvegetal.com/",
    "https://lejardindaudrey.com/en/event-floral-designer-in-paris/",
    "https://lesjoyauxpurs.com/",
    "https://lesmariesphotographies.fr/",
    "https://lespremicesdem.fr/",
    "https://lheure-doree.fr/",
    "https://lilasboheme.fr/",
    "https://lorangeriedumanoir.com/",
    "https://loscaballerosweddings.com/",
    "https://madame-etincelle.fr/",
    "https://mademoiselle-dit-oui.fr/",
    "https://madeorganisation.com/",
    "https://maisongelis.fr/",
    "https://maitebailleul.com/presse/",
    "https://manalparis.com/pages/presse",
    "https://manoirsaintemarie.fr/les-partenaires/",
    "https://mariedesaunay.fr/",
    "https://marielp.com/",
    "https://marineszczepaniak.com/blog/",
    "https://masaccessoires.com/",
    "https://mathilde-marie.com/presse/",
    "https://mathildebphotography.com/fr",
    "https://merylmphotographie.com/",
    "https://mickaelbarbier.fr/",
    "https://mygreenevent.fr/",
    "https://nasandcosevents.com/",
    "https://nicolasterraes.com/partenaires/",
    "https://noeldoiziphotographie.fr/a-propos-de-moi/",
    "https://o-dela-des-fleurs.com/",
    "https://officiant-ceremonie.fr/temoignages-galerie/",
    "https://ohlescoeurs-deco.fr/",
    "https://ohmygirls-events.com/",
    "https://organisation-dday.com/presse/",
    "https://photographie.chloeldn.fr/",
    "https://pierrefrogerfilms.com/",
    "https://pierregobled.com/",
    "https://poppyblossomphoto.fr/",
    "https://poppyetdaisy.fr/",
    "https://promessegraphique.com/",
    "https://provence-emoi.com/by-the-sea",
    "https://prune-wedding.fr/",
    "https://recordyou.fr/",
    "https://renauld-photographie.com/",
    "https://reveriesetbois.fr/",
    "https://rosalyne-creations.fr/",
    "https://rosefushiaphotographie.com/publications/",
    "https://sandra-berete.com/",
    "https://sayido.fr/",
    "https://septfevrier.com/",
    "https://silvenehedon.com/univers/on-parle-de-nous/",
    "https://soniablanc.com/revue-de-presse/",
    "https://sophie-brioudes.com/",
    "https://soufianezaidi.com/",
    "https://sous-le-charme.fr/",
    "https://storybyludi.fr/wedding-planner-provence/",
    "https://streetfocus.fr/a-propos/",
    "https://sylviacalmet.com/",
    "https://the-quirky.com/infos/",
    "https://thememories.fr/",
    "https://thewitness.fr/",
    "https://thierrynade.fr/",
    "https://trentieme-etage.com/",
    "https://tropicaliapapeterie.com/",
    "https://velvetrendezvous.fr/nos-realisations/medias/",
    "https://vincentmontagne.com/about-2/",
    "https://vivancia-events.fr/",
    "https://vrsurmesure.fr/",
    "https://wedays.com/blog-mariage/",
    "https://whole.fr/pages/parutions-press-releases",
    "https://www.adeline-mariage.com/",
    "https://www.adrianamoraisphotography.com/",
    "https://www.agathehelleux.com/prestations",
    "https://www.amarildine.com/",
    "https://www.amethik.com/",
    "https://www.ana-des-cabanasses.fr/",
    "https://www.annoncesdentelle.fr/",
    "https://www.apparenceforevent.com/",
    "https://www.apres-lebip.ch/fr/",
    "https://www.artetfacts.com/actualites.html",
    "https://www.atelier-douceurs-caroline.fr/",
    "https://www.atelier-swan.com/",
    "https://www.atelieralexandrafabbri.com/",
    "https://www.atelierlilac.com/en/pages/a-propos",
    "https://www.audreycoppee.com/",
    "https://www.audreyvkb.com/",
    "https://www.august-events.fr/",
    "https://www.aureliaprado.com/en",
    "https://www.aurored-photographie.fr/",
    "https://www.baptistehauville.com/",
    "https://www.bastienanguiano.com/avis",
    "https://www.benaproduction.com/mariage/on-parle-de-nous.html",
    "https://www.blanchefleur.com/mariages",
    "https://www.blossomevents-erika.com/",
    "https://www.bonjour-suzanne.fr/on-parle-de-nous/",
    "https://www.borne-photo-nantes.fr/",
    "https://www.c-joly.fr/",
    "https://www.camillerecolin.fr/la-presse/",
    "https://www.capturelife.fr/",
    "https://www.cecileschuhmann.com/a-propos/",
    "https://www.cejourunique.fr/",
    "https://www.celebrerlavie.com/",
    "https://www.celine-ceremonies.fr/temoignages",
    "https://www.celinezed.com/about-me/",
    "https://www.cesticisallealouer.com/",
    "https://www.charliebillie.com/",
    "https://www.charlottealaoui.com/",
    "https://www.charlottedo.fr/",
    "https://www.chrissmartevent.fr/index.html",
    "https://www.christellevasseur.com/blogs/la-presse-parle-de-nous/collection-2018-robes-de-mariee-bordeaux",
    "https://www.cigales-petitsfours.com/",
    "https://www.clemenceduboisphotographie.com/",
    "https://www.cleolebrun.fr/",
    "https://www.comuneorchidee.com/nos-partenaires/",
    "https://www.coquelicotte.fr/",
    "https://www.coralielescieux.com/gallery/aurea-benjamin/",
    "https://www.d-we.fr/",
    "https://www.dancepolice.fr/",
    "https://www.dans-ma-tribu.fr/",
    "https://www.davidone.fr/contact",
    "https://www.denuancesetdeglamour.fr/agence/",
    "https://www.dmk-destinationart.com/",
    "https://www.domainedepetiosse.com/domaine-landes/partenaires/",
    "https://www.dorsetdesoie.com/",
    "https://www.elielle.fr/",
    "https://www.elisejulliard-photographies.fr/",
    "https://www.elleaditoui.fr/",
    "https://www.elleetnous.com/wedding-event-planner-in-provence/",
    "https://www.fannyauer.com/en/about/",
    "https://www.flore-et-zephyr.com/blogs/journal",
    "https://www.florentcattelain.com/",
    "https://www.florentfauqueux.fr/",
    "https://www.ganza.fr/pages/revue-de-presse",
    "https://www.graine-de-coton.com/category/dossier-presse/article-blog/",
    "https://www.gwenaellemichels.com/parutions/",
    "https://www.idea-lisa.fr/",
    "https://www.instant-loc.fr/",
    "https://www.jaimemarobe.com/pages/presse",
    "https://www.jerometarakci.com/",
    "https://www.jodieatelier.fr/",
    "https://www.joker-artifices.com/en/",
    "https://www.juliecostet.com/",
    "https://www.kaacouture.com/presse/",
    "https://www.kokoro-berlin.com/de-fr/pages/presse-veroffentlichungen",
    "https://www.lacroixdeschamps.fr/ils-parlent-de-nous/",
    "https://www.lafabriqueamariage.fr/",
    "https://www.lafilleaunoeudrouge.fr/publication-web-presse-faire-part-sur-mesure-la-fille-au-noeud-rouge/",
    "https://www.larochelle-mariage.fr/",
    "https://www.latelierdhiris.fr/",
    "https://www.latelierdhiris.fr/videos/",
    "https://www.lauraleclairdelord.com/a-propos/",
    "https://www.lauren-gabriele.com/",
    "https://www.laurentkapelski.com/presse",
    "https://www.lauriane-lespinasse.fr/",
    "https://www.lecomptoirdubonheur.com/",
    "https://www.lehangarlocations.com/",
    "https://www.lesateliersdulux.fr/",
    "https://www.lesbandits.fr/",
    "https://www.lesbeautesparis.com/",
    "https://www.lesdeuxoursons.com/revue-de-presse/",
    "https://www.lesfleursdemilijolie.fr/",
    "https://www.lesmariesdelavieenrose.com/accueil",
    "https://www.lesombelles.com/",
    "https://www.letandemdesdemoiselles.fr/accueil/",
    "https://www.letsrockwedding.com/",
    "https://www.lilaswood.com/",
    "https://www.lisadawn.co.uk/",
    "https://www.lisahoshi-photographie.com/",
    "https://www.loispoch.com/",
    "https://www.lorenzoaccardi.com/fr/about/",
    "https://www.loveisall-events.com/",
    "https://www.loveishappiness.f/tarifs/",
    "https://www.lyloomaloe.com/ils-parlent-de-nous/",
    "https://www.madecochic.com/",
    "https://www.maison-nans.fr/a-propos/",
    "https://www.maldemephotographe.fr/",
    "https://www.marcribis.com/",
    "https://www.margauxgatti.fr/",
    "https://www.mariage-a-soie.fr/",
    "https://www.mariage-etc.com/en-savoir-plus.php",
    "https://www.marie-myrtille.com/",
    "https://www.marionfort.com/",
    "https://www.marleneberthelot.com/",
    "https://www.mathieubonnaric.fr/",
    "https://www.matierepremierelatelier.fr/",
    "https://www.maudbonnard.com/partenaires-mariage/",
    "https://www.melanie-orsini.fr",
    "https://www.melaniebultez.com/",
    "https://www.mesplusbeauxsouvenirs.com/partenaires/",
    "https://www.mestiteslilis.com/mestiteslilis-on-en-parle/",
    "https://www.mixnight.net/a-propos/",
    "https://www.nebbiastudio.com/",
    "https://www.noella-wonderevents.fr/",
    "https://www.nuancesfactory.fr/",
    "https://www.nympheakinds.fr/",
    "https://www.ongi-ceremonie.be/blog/nos-partenaires/",
    "https://www.ot-pays-de-collonges-la-rouge.fr/",
    "https://www.ouienprovence.com/",
    "https://www.ouimaperle.com/",
    "https://www.pantaiaevents.com/",
    "https://www.perraud-ussel.com/",
    "https://www.petalesdeprovence.eu/",
    "https://www.petite-fleur.fr/a-propos/",
    "https://www.phaedraevents.gr/press/",
    "https://www.photographybychloe.com/publications-photographe-mariage-reportage/",
    "https://www.plumeparis.fr/",
    "https://www.pondicheri-weddings.fr/",
    "https://www.pour-loccasion.fr/",
    "https://www.psbylamaleta.fr/papeterie-mariage",
    "https://www.receptionelles.com/",
    "https://www.reveriesetbois.fr/",
    "https://www.riverfabric.com/",
    "https://www.rosa-eventdesign.com/",
    "https://www.roudouic.com/partenaires.html",
    "https://www.samanthapastoor.com/",
    "https://www.sartistphotography.com/",
    "https://www.saveyourdate.fr/partenaires/",
    "https://www.sbj-decoration.com/",
    "https://www.scclemence.fr/mes-partenaires/",
    "https://www.sjstudio.fr/",
    "https://www.souslesbranches.fr/",
    "https://www.studio-ap2c.com/liens/",
    "https://www.studiobalzac.fr/",
    "https://www.sweet-life-events.com/a-propos/c-25.html",
    "https://www.ulrike-photographe.com/",
    "https://www.unikday.fr/",
    "https://www.unio-preparation.com/",
    "https://www.uniparis.com/pages/presse",
    "https://www.violette-berlingot.com/partenaires/",
    "https://www.weddingsbymatilda.pt/",
    "https://www.wildinlovefestival.com/",
    "https://www.yeswebloom.com/temoignages/",
    "https://yanngilquin.com/",
    "https://zeiphotographie.fr/",
    "https://zenlove.io/aider-couples/",

    // Nouveau site
    "https://glamping-dome.fr",
    "https://domedenamur.be"
];

/**
 * Extraire la base URL (scheme + host)
 */
function getBaseUrl($url)
{
    $parsed = parse_url($url);
    $scheme = isset($parsed['scheme']) ? $parsed['scheme'] : 'https';
    $host = isset($parsed['host']) ? $parsed['host'] : '';
    return $scheme . '://' . $host;
}

/**
 * Fonction pour extraire les emails d'un contenu HTML
 */
function extractEmails($content)
{
    $emails = [];

    // Pattern pour emails standards
    $standardPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

    // Recherche des emails standards
    if (preg_match_all($standardPattern, $content, $matches)) {
        $emails = array_merge($emails, $matches[0]);
    }

    // Recherche des emails dans les liens mailto:
    if (preg_match_all('/mailto:([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $content, $matches)) {
        $emails = array_merge($emails, $matches[1]);
    }

    // Recherche des emails obfusqués [at] ou (at)
    if (preg_match_all('/[a-zA-Z0-9._%+-]+\s*[\[\(]\s*at\s*[\]\)]\s*[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/i', $content, $matches)) {
        foreach ($matches[0] as $obfuscated) {
            $clean = preg_replace('/\s*[\[\(]\s*at\s*[\]\)]\s*/i', '@', $obfuscated);
            $emails[] = $clean;
        }
    }

    // Recherche des emails avec [dot] ou (dot)
    if (preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\s*[\[\(]\s*dot\s*[\]\)]\s*[a-zA-Z]{2,}/i', $content, $matches)) {
        foreach ($matches[0] as $obfuscated) {
            $clean = preg_replace('/\s*[\[\(]\s*dot\s*[\]\)]\s*/i', '.', $obfuscated);
            $emails[] = $clean;
        }
    }

    // Nettoyer et dédupliquer
    $emails = array_map('strtolower', $emails);
    $emails = array_map('trim', $emails);
    $emails = array_unique($emails);

    // Filtrer les faux positifs
    $emails = array_filter($emails, function ($email) {
        // Exclure les extensions de fichiers
        if (preg_match('/\.(png|jpg|jpeg|gif|svg|css|js|webp|woff|woff2|ttf|eot|ico|pdf)$/i', $email)) {
            return false;
        }
        // Exclure les emails génériques/exemples
        if (preg_match('/(example\.|test@|user@|email@|your@|name@|info@example|noreply@example)/i', $email)) {
            return false;
        }
        // Exclure les faux emails de type 2x@
        if (preg_match('/^\d+x@/', $email)) {
            return false;
        }
        // Valider le format
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });

    return array_values($emails);
}

/**
 * Extraire les liens de contact depuis le HTML
 */
function extractContactLinks($content, $baseUrl)
{
    $contactLinks = [];

    // Patterns pour trouver des liens de contact dans le HTML
    $patterns = [
        '/href=["\']([^"\']*contact[^"\']*)["\']/',
        '/href=["\']([^"\']*contactez[^"\']*)["\']/',
        '/href=["\']([^"\']*nous-contacter[^"\']*)["\']/',
        '/href=["\']([^"\']*a-propos[^"\']*)["\']/',
        '/href=["\']([^"\']*about[^"\']*)["\']/',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $link) {
                // Ignorer les liens mailto et javascript
                if (strpos($link, 'mailto:') === 0 || strpos($link, 'javascript:') === 0) {
                    continue;
                }
                // Convertir en URL absolue si nécessaire
                if (strpos($link, 'http') !== 0) {
                    if (strpos($link, '/') === 0) {
                        $link = $baseUrl . $link;
                    } else {
                        $link = $baseUrl . '/' . $link;
                    }
                }
                $contactLinks[] = $link;
            }
        }
    }

    return array_unique($contactLinks);
}

/**
 * Fonction pour récupérer le contenu d'une URL
 */
function fetchUrl($url, $timeout, $userAgent)
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => $timeout,
        CURLOPT_USERAGENT => $userAgent,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: fr-FR,fr;q=0.9,en;q=0.8',
        ],
    ]);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    curl_close($ch);

    return [
        'success' => ($httpCode >= 200 && $httpCode < 400 && !empty($content)),
        'content' => $content,
        'http_code' => $httpCode,
        'error' => $error
    ];
}

/**
 * Rechercher des emails sur une URL et ses pages alternatives
 */
function searchEmailsForUrl($url, $alternativePages, $timeout, $userAgent)
{
    $result = [
        'emails' => [],
        'source' => null,
        'pages_checked' => [],
        'error' => null
    ];

    $baseUrl = getBaseUrl($url);

    // 1. D'abord essayer l'URL principale
    $response = fetchUrl($url, $timeout, $userAgent);
    $result['pages_checked'][] = $url;

    if ($response['success']) {
        $emails = extractEmails($response['content']);
        if (!empty($emails)) {
            $result['emails'] = $emails;
            $result['source'] = $url;
            return $result;
        }

        // 2. Chercher des liens de contact dans la page
        $contactLinks = extractContactLinks($response['content'], $baseUrl);
        foreach ($contactLinks as $contactLink) {
            if (in_array($contactLink, $result['pages_checked'])) {
                continue;
            }

            usleep(300000); // 0.3s pause
            $contactResponse = fetchUrl($contactLink, $timeout, $userAgent);
            $result['pages_checked'][] = $contactLink;

            if ($contactResponse['success']) {
                $emails = extractEmails($contactResponse['content']);
                if (!empty($emails)) {
                    $result['emails'] = $emails;
                    $result['source'] = $contactLink;
                    return $result;
                }
            }
        }
    } else {
        $result['error'] = $response['error'] ?: "HTTP {$response['http_code']}";
    }

    // 3. Essayer les pages alternatives standard
    foreach ($alternativePages as $altPage) {
        $altUrl = $baseUrl . $altPage;

        if (in_array($altUrl, $result['pages_checked'])) {
            continue;
        }

        usleep(300000); // 0.3s pause
        $altResponse = fetchUrl($altUrl, $timeout, $userAgent);
        $result['pages_checked'][] = $altUrl;

        if ($altResponse['success']) {
            $emails = extractEmails($altResponse['content']);
            if (!empty($emails)) {
                $result['emails'] = $emails;
                $result['source'] = $altUrl;
                return $result;
            }
        }
    }

    return $result;
}

// ============================================
// EXECUTION PRINCIPALE
// ============================================

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║     EXTRACTEUR D'EMAILS - VERSION AMÉLIORÉE                      ║\n";
echo "║     (recherche aussi sur /contact et pages alternatives)         ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

$results = [];
$allEmails = [];
$totalUrls = count($urls);
$processed = 0;
$errors = 0;
$foundOnAlt = 0;

echo "Traitement de $totalUrls URLs...\n";
echo "Pages alternatives vérifiées : " . implode(', ', array_slice($alternativePages, 0, 5)) . "...\n\n";

foreach ($urls as $url) {
    $processed++;
    $progress = round(($processed / $totalUrls) * 100);

    echo "[$processed/$totalUrls] ($progress%) " . substr($url, 0, 50) . (strlen($url) > 50 ? '...' : '') . "\n";

    $searchResult = searchEmailsForUrl($url, $alternativePages, $timeout, $userAgent);

    if (!empty($searchResult['emails'])) {
        $isAlt = ($searchResult['source'] !== $url);
        if ($isAlt) {
            $foundOnAlt++;
            echo "    ✓ " . count($searchResult['emails']) . " email(s) trouvé(s) sur: " . $searchResult['source'] . "\n";
        } else {
            echo "    ✓ " . count($searchResult['emails']) . " email(s) trouvé(s)\n";
        }
        foreach ($searchResult['emails'] as $email) {
            echo "      → $email\n";
        }
        $results[$url] = [
            'emails' => $searchResult['emails'],
            'source' => $searchResult['source'],
            'pages_checked' => count($searchResult['pages_checked'])
        ];
        $allEmails = array_merge($allEmails, $searchResult['emails']);
    } elseif ($searchResult['error']) {
        $errors++;
        echo "    ✗ Erreur: {$searchResult['error']}\n";
        $results[$url] = [
            'emails' => [],
            'error' => $searchResult['error'],
            'pages_checked' => count($searchResult['pages_checked'])
        ];
    } else {
        echo "    ○ Aucun email trouvé (" . count($searchResult['pages_checked']) . " pages vérifiées)\n";
        $results[$url] = [
            'emails' => [],
            'source' => null,
            'pages_checked' => count($searchResult['pages_checked'])
        ];
    }

    // Pause entre les sites
    usleep(200000); // 0.2s
}

// Dédupliquer tous les emails
$allEmails = array_unique($allEmails);
sort($allEmails);

// ============================================
// RAPPORT FINAL
// ============================================

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                       RAPPORT FINAL                               ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

$sitesWithEmails = count(array_filter($results, fn($r) => !empty($r['emails'])));

echo "URLs traitées:              $processed\n";
echo "Sites avec email(s):        $sitesWithEmails\n";
echo "Emails trouvés sur /contact: $foundOnAlt\n";
echo "Erreurs:                    $errors\n";
echo "Emails uniques trouvés:     " . count($allEmails) . "\n\n";

echo "───────────────────────────────────────────────────────────────────\n";
echo "LISTE DES EMAILS UNIQUES:\n";
echo "───────────────────────────────────────────────────────────────────\n";

foreach ($allEmails as $email) {
    echo "$email\n";
}

// ============================================
// SAUVEGARDE DES FICHIERS
// ============================================

// CSV détaillé
$csvFile = 'emails_extraits.csv';
$fp = fopen($csvFile, 'w');
fputcsv($fp, ['URL', 'Email', 'Source', 'Pages Vérifiées']);

foreach ($results as $url => $data) {
    if (!empty($data['emails'])) {
        foreach ($data['emails'] as $email) {
            fputcsv($fp, [$url, $email, $data['source'], $data['pages_checked']]);
        }
    } else {
        $status = isset($data['error']) ? "(erreur: {$data['error']})" : '(aucun email)';
        fputcsv($fp, [$url, $status, '', $data['pages_checked']]);
    }
}

fclose($fp);

// Liste simple d'emails
$emailsFile = 'emails_liste.txt';
file_put_contents($emailsFile, implode("\n", $allEmails));

// JSON complet
$jsonFile = 'emails_resultats.json';
file_put_contents($jsonFile, json_encode([
    'date' => date('Y-m-d H:i:s'),
    'stats' => [
        'total_urls' => $totalUrls,
        'sites_with_emails' => $sitesWithEmails,
        'found_on_alternative_pages' => $foundOnAlt,
        'errors' => $errors,
        'total_unique_emails' => count($allEmails),
    ],
    'emails' => $allEmails,
    'details' => $results
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "\n───────────────────────────────────────────────────────────────────\n";
echo "Fichiers générés:\n";
echo "  • $csvFile      (détails par URL)\n";
echo "  • $emailsFile   (liste simple)\n";
echo "  • $jsonFile     (format JSON)\n";
echo "───────────────────────────────────────────────────────────────────\n";
