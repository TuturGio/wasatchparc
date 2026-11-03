<?php
declare(strict_types=1);

/*
 * Formulaire de contact : envoi par SMTP avec la boîte Hostinger (réglages dans le fichier privé, voir config.php),
 * aucun stockage des messages. Anti-spam : champ piège + jeton signé (délai minimum, usage unique)
 * et nombre d'envois limité par adresse IP (fichiers dans le dossier privé, hors web et hors git).
 */

const CONTACT_MESSAGES = [
    'invalide' => 'Please check the highlighted fields and try again.',
    'echec' => 'Sorry, your message could not be sent. Please try again later or call us at 800-391-9954.',
    'spam' => 'Sorry, your message could not be sent. Please try again later or call us at 800-391-9954.',
    'limite' => 'You have sent too many messages. Please try again later.',
    'requis' => 'This field is required.',
    'email' => 'Please enter a valid email address.',
    'trop_long' => 'This field is too long.',
];

const CONTACT_LIMITES = ['prenom' => 100, 'nom' => 100, 'email' => 254, 'message' => 5000];
const CONTACT_DELAI_MIN = 3;          // secondes entre l'affichage du formulaire et l'envoi
const CONTACT_DELAI_MAX = 86400;      // validité d'un jeton
const CONTACT_LIMITE_HEURE = 3;
const CONTACT_LIMITE_JOUR = 10;

/** État du formulaire pour la requête courante (rempli par contact_traiter()). */
function contact_etat(?array $nouvel_etat = null): array
{
    static $etat = ['statut' => 'init', 'valeurs' => [], 'erreurs' => []];
    if ($nouvel_etat !== null) {
        $etat = $nouvel_etat;
    }
    return $etat;
}

/** Jeton « horodatage.aléa.signature » : l'aléa rend chaque jeton unique, même à la même seconde. */
function contact_jeton(?int $horodatage = null, ?string $alea = null): string
{
    $horodatage ??= time();
    $alea ??= bin2hex(random_bytes(8));
    return $horodatage . '.' . $alea . '.' . hash_hmac('sha256', 'contact|' . $horodatage . '|' . $alea, (string) prive('cle_secrete'));
}

function contact_jeton_valide(string $jeton): bool
{
    [$horodatage, $alea] = explode('.', $jeton, 3) + ['', ''];
    if ((string) prive('cle_secrete') === '' || !ctype_digit($horodatage) || !ctype_xdigit($alea) || strlen($alea) !== 16
        || !hash_equals(contact_jeton((int) $horodatage, $alea), $jeton)) {
        return false;
    }
    $age = time() - (int) $horodatage;
    return $age >= CONTACT_DELAI_MIN && $age <= CONTACT_DELAI_MAX;
}

/*
 * Limites d'envoi, sans base de données : un fichier par jeton utilisé, un fichier par adresse IP.
 * Les noms de fichiers sont des HMAC (ni jeton ni IP en clair). Si le dossier n'est pas accessible
 * en écriture, les limites sont ignorées (journalisé) plutôt que de bloquer le formulaire.
 */

function contact_stockage(string $sous_dossier): ?string
{
    $dossier = DOSSIER_PRIVE . '/contact/' . $sous_dossier;
    if (!is_dir($dossier) && !@mkdir($dossier, 0700, true) && !is_dir($dossier)) {
        error_log('contact : dossier de stockage inaccessible : ' . $dossier);
        return null;
    }
    // Ménage occasionnel : fichiers plus vieux que la validité d'un jeton.
    if (random_int(1, 50) === 1) {
        foreach (glob($dossier . '/*') ?: [] as $f) {
            if (filemtime($f) < time() - CONTACT_DELAI_MAX) {
                @unlink($f);
            }
        }
    }
    return $dossier;
}

/** Marque le jeton comme utilisé ; faux s'il l'était déjà (création atomique du fichier). */
function contact_consommer_jeton(string $jeton): bool
{
    $dossier = contact_stockage('jetons');
    if ($dossier === null) {
        return true;
    }
    $f = @fopen($dossier . '/' . hash_hmac('sha256', 'jeton|' . $jeton, (string) prive('cle_secrete')), 'x');
    if ($f === false) {
        return false;
    }
    fclose($f);
    return true;
}

/** Adresse du visiteur ; en IPv6, le préfixe /64 (une connexion en dispose en entier). */
function contact_ip(): string
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    $binaire = @inet_pton($ip);
    if ($binaire !== false && strlen($binaire) === 16) {
        return bin2hex(substr($binaire, 0, 8)) . '::/64';
    }
    return $ip;
}

/** Vrai si l'IP peut encore envoyer ; si oui et $enregistrer, compte cet envoi. */
function contact_quota_ip(bool $enregistrer): bool
{
    $dossier = contact_stockage('ip');
    if ($dossier === null) {
        return true;
    }
    $f = @fopen($dossier . '/' . hash_hmac('sha256', 'ip|' . contact_ip(), (string) prive('cle_secrete')), 'c+');
    if ($f === false) {
        return true;
    }
    flock($f, LOCK_EX);
    $maintenant = time();
    $envois = array_filter(
        array_map('intval', explode(',', (string) stream_get_contents($f))),
        fn(int $t) => $t > $maintenant - 86400
    );
    $derniere_heure = count(array_filter($envois, fn(int $t) => $t > $maintenant - 3600));
    $autorise = $derniere_heure < CONTACT_LIMITE_HEURE && count($envois) < CONTACT_LIMITE_JOUR;
    if ($autorise && $enregistrer) {
        $envois[] = $maintenant;
    }
    ftruncate($f, 0);
    rewind($f);
    fwrite($f, implode(',', $envois));
    flock($f, LOCK_UN);
    fclose($f);
    return $autorise;
}

/** Vrai si la chaîne est de l'UTF-8 valide. */
function contact_utf8_valide(string $s): bool
{
    return preg_match('//u', $s) === 1;
}

/** Longueur en caractères (et non en octets) d'une chaîne UTF-8 valide. */
function contact_longueur(string $s): int
{
    return (int) preg_match_all('/./us', $s);
}

/** En-tête encodé RFC 2047 (=?UTF-8?B?…?=) si nécessaire, en mots de 45 octets maximum sans couper de caractère. */
function contact_encoder_entete(string $s): string
{
    if (!preg_match('/[^\x20-\x7E]/', $s)) {
        return $s;
    }
    preg_match_all('/./us', $s, $caracteres);
    $mots = [];
    $courant = '';
    foreach ($caracteres[0] as $c) {
        if (strlen($courant . $c) > 45) {
            $mots[] = $courant;
            $courant = '';
        }
        $courant .= $c;
    }
    $mots[] = $courant;
    return implode("\r\n ", array_map(fn($m) => '=?UTF-8?B?' . base64_encode($m) . '?=', $mots));
}

/** Supprime les caractères de contrôle ; $multiligne conserve les retours à la ligne. */
function contact_nettoyer(mixed $valeur, bool $multiligne = false): string
{
    $valeur = is_string($valeur) ? $valeur : '';
    if (!contact_utf8_valide($valeur)) {
        return '';
    }
    $valeur = str_replace("\r\n", "\n", $valeur);
    $motif = $multiligne ? '/[\x00-\x09\x0B-\x1F\x7F]/u' : '/[\x00-\x1F\x7F]/u';
    return trim(preg_replace($motif, $multiligne ? '' : ' ', $valeur));
}

/** Traite un POST. Renvoie l'URL de redirection après succès (PRG), sinon null. */
function contact_traiter(array $post): ?string
{
    $valeurs = [
        'prenom' => contact_nettoyer($post['prenom'] ?? ''),
        'nom' => contact_nettoyer($post['nom'] ?? ''),
        'email' => contact_nettoyer($post['email'] ?? ''),
        'message' => contact_nettoyer($post['message'] ?? '', true),
    ];

    // Champ piège rempli ou jeton absent / trop récent / expiré : rejet silencieux.
    if (($post['site'] ?? '') !== '' || !contact_jeton_valide((string) ($post['_jeton'] ?? ''))) {
        contact_etat(['statut' => 'spam', 'valeurs' => $valeurs, 'erreurs' => []]);
        return null;
    }

    $erreurs = [];
    foreach (['prenom', 'nom', 'email'] as $champ) {
        if ($valeurs[$champ] === '') {
            $erreurs[$champ] = CONTACT_MESSAGES['requis'];
        }
    }
    if (!isset($erreurs['email']) && filter_var($valeurs['email'], FILTER_VALIDATE_EMAIL) === false) {
        $erreurs['email'] = CONTACT_MESSAGES['email'];
    }
    foreach (CONTACT_LIMITES as $champ => $max) {
        if (!isset($erreurs[$champ]) && contact_longueur($valeurs[$champ]) > $max) {
            $erreurs[$champ] = CONTACT_MESSAGES['trop_long'];
        }
    }
    if ($erreurs) {
        contact_etat(['statut' => 'invalide', 'valeurs' => $valeurs, 'erreurs' => $erreurs]);
        return null;
    }

    // Limite par IP avant de consommer le jeton : un visiteur bloqué garde son message à l'écran.
    if (!contact_quota_ip(false)) {
        contact_etat(['statut' => 'limite', 'valeurs' => $valeurs, 'erreurs' => []]);
        return null;
    }
    // Jeton déjà utilisé (rejeu, double clic) : rejet silencieux, comme le champ piège.
    if (!contact_consommer_jeton((string) $post['_jeton'])) {
        contact_etat(['statut' => 'spam', 'valeurs' => $valeurs, 'erreurs' => []]);
        return null;
    }
    if (!contact_quota_ip(true)) {
        contact_etat(['statut' => 'limite', 'valeurs' => $valeurs, 'erreurs' => []]);
        return null;
    }

    if (!contact_envoyer($valeurs)) {
        contact_etat(['statut' => 'echec', 'valeurs' => $valeurs, 'erreurs' => []]);
        return null;
    }
    return '/contact/?sent=1#formulaire';
}

/** « Nom » <adresse> : nom entre guillemets (ASCII) ou encodé RFC 2047 (UTF-8). */
function contact_adresse(string $nom, string $email): string
{
    $nom = !preg_match('/[^\x20-\x7E]/', $nom)
        ? '"' . addcslashes($nom, '"\\') . '"'
        : contact_encoder_entete($nom);
    return $nom . ' <' . $email . '>';
}

function contact_envoyer(array $v): bool
{
    $utilisateur = (string) prive('smtp_utilisateur');
    $destinataire = (string) prive('destinataire');
    if (!filter_var($utilisateur, FILTER_VALIDATE_EMAIL) || !filter_var($destinataire, FILTER_VALIDATE_EMAIL)
        || (string) prive('smtp_mot_de_passe') === '') {
        error_log('contact : réglages SMTP incomplets dans ' . FICHIER_PRIVE);
        return false;
    }
    $nom = trim($v['prenom'] . ' ' . $v['nom']);
    $entetes = [
        'Date' => date(DATE_RFC2822),
        'From' => contact_adresse('Wasatch Parc website', $utilisateur),
        'To' => $destinataire,
        'Reply-To' => contact_adresse($nom, $v['email']),
        'Subject' => contact_encoder_entete('Contact form: ' . $nom),
        'Message-ID' => '<' . bin2hex(random_bytes(12)) . '@wasatchparc.com>',
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'Content-Transfer-Encoding' => '8bit',
    ];
    $corps = "Name: $nom\nEmail: {$v['email']}\n\nMessage:\n{$v['message']}\n\n"
        . "-- \nSent from the contact form of https://wasatchparc.com/contact/";
    return smtp_envoyer($utilisateur, $destinataire, $entetes, $corps);
}

/**
 * Envoi d'un message par SMTP sur TLS (port 465) avec authentification LOGIN.
 * Renvoie faux, et journalise la réponse du serveur, à la première étape refusée.
 */
function smtp_envoyer(string $de, string $a, array $entetes, string $corps): bool
{
    $hote = (string) (prive('smtp_hote') ?? 'smtp.hostinger.com');
    $port = (int) (prive('smtp_port') ?? 465);
    $flux = @stream_socket_client("ssl://$hote:$port", $errno, $errstr, 15);
    if ($flux === false) {
        error_log("contact : connexion SMTP impossible ($hote:$port) : $errstr");
        return false;
    }
    stream_set_timeout($flux, 15);
    $lire = function () use ($flux): string {
        $reponse = '';
        while (($ligne = fgets($flux, 1024)) !== false) {
            $reponse .= $ligne;
            if (strlen($ligne) < 4 || $ligne[3] !== '-') {
                break;
            }
        }
        return $reponse;
    };
    $etape = function (?string $commande, string $attendu, string $nom) use ($flux, $lire): bool {
        if ($commande !== null) {
            fwrite($flux, $commande . "\r\n");
        }
        $reponse = $lire();
        if (strncmp($reponse, $attendu, 3) !== 0) {
            error_log("contact : SMTP refusé à l'étape $nom : " . trim($reponse));
            return false;
        }
        return true;
    };

    $donnees = '';
    foreach ($entetes as $cle => $valeur) {
        $donnees .= "$cle: $valeur\r\n";
    }
    // Lignes en CRLF ; un point en début de ligne est doublé (RFC 5321, 4.5.2).
    $donnees .= "\r\n" . preg_replace('/^\./m', '..', str_replace("\n", "\r\n", $corps)) . "\r\n.";

    $ok = $etape(null, '220', 'accueil')
        && $etape('EHLO wasatchparc.com', '250', 'EHLO')
        && $etape('AUTH LOGIN', '334', 'AUTH')
        && $etape(base64_encode($de), '334', 'utilisateur')
        && $etape(base64_encode((string) prive('smtp_mot_de_passe')), '235', 'mot de passe')
        && $etape("MAIL FROM:<$de>", '250', 'MAIL FROM')
        && $etape("RCPT TO:<$a>", '250', 'RCPT TO')
        && $etape('DATA', '354', 'DATA')
        && $etape($donnees, '250', 'message');
    fwrite($flux, "QUIT\r\n");
    fclose($flux);
    return $ok;
}

/** Message affiché au-dessus du formulaire après un envoi refusé (balisage des erreurs WPForms). */
function contact_message(): string
{
    $statut = contact_etat()['statut'];
    if (!isset(CONTACT_MESSAGES[$statut])) {
        return '';
    }
    return '<div class="wpforms-error-container" role="alert"><p>' . htmlspecialchars(CONTACT_MESSAGES[$statut]) . '</p></div>';
}

/** Valeur à remettre dans un champ après une erreur, échappée pour un attribut ou un textarea. */
function contact_valeur(string $champ): string
{
    return htmlspecialchars(contact_etat()['valeurs'][$champ] ?? '', ENT_QUOTES);
}

/** Erreur d'un champ, au balisage WPForms (classe et libellé sous le champ). */
function contact_erreur(string $champ): string
{
    $erreur = contact_etat()['erreurs'][$champ] ?? null;
    return $erreur === null ? '' : '<label class="wpforms-error" role="alert">' . htmlspecialchars($erreur) . '</label>';
}
