<?php

// Désactiver l'affichage des erreurs pour éviter de polluer le corps de la réponse
//ini_set('display_errors', 0);

// Définir le répertoire de stockage des requêtes
$ROOT = dirname(__DIR__, 2);
$storage_dir = $ROOT . '/requests/';


// Créer le dossier s'il n'existe pas
if (!is_dir($storage_dir)) {
    //echo("Storage dir do not exist, creating it\n");
    if (!mkdir($storage_dir, 0775, true)) {
        //echo("Failed to create storage dir\n");
    }
}

// 1. Récupérer les headers de la requête
$headers = [];
// Compatible avec tous les serveurs web (Apache, Nginx, etc.)
if (function_exists('getallheaders')) {
    $headers = getallheaders();
} else {
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $header_name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
            $headers[$header_name] = $value;
        } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
            $header_name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
            $headers[$header_name] = $value;
        }
    }
}

// 2. Récupérer le corps (body) de la requête
$body = file_get_contents('php://input');

// 3. Préparer le nom du fichier basé sur la date, l'heure et un compteur
$timestamp = microtime(true);
$date_str = date('Y-m-d_H-i-s', (int)$timestamp);

$counter = 0;
do {
    // Suffixe avec le compteur si plusieurs requêtes arrivent dans la même seconde
    $filename = $storage_dir . 'req_' . $date_str . ($counter > 0 ? "_$counter" : '') . '.txt';
    $counter++;
} while (file_exists($filename));

// 4. Formater le contenu à enregistrer
$request_content = "--- METHODE & URI ---\n";
$request_content .= $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . "\n\n";

$request_content .= "--- HEADERS ---\n";
foreach ($headers as $name => $value) {
    $request_content .= "$name: $value\n";
}

$request_content .= "\n--- BODY ---\n";
$request_content .= $body;

// Enregistrer dans le fichier distinct
file_put_contents($filename, $request_content);
chmod($filename, 0664);

// 5. Répondre avec un code 200, un body vide et supprimer le Content-Type par défaut
header_remove('Content-Type'); // Supprime le content-type par défaut (ex: text/html)
http_response_code(200);
exit;

?>
