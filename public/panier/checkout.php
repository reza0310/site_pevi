<?php

$ROOT = dirname(__DIR__, 2);

include $ROOT.'includes/http.php';
include $ROOT.'includes/db.php';
include $ROOT.'includes/order_manager.php';
include $ROOT.'includes/product_manager.php';
include $ROOT.'includes/systempay.php';

include $ROOT.'config/config.php';
include $ROOT.'config/creds.php';

// Check HTTP request type
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(405); // Method Not Allowed
}

// Check HTTP post request content type
if ($_SERVER["CONTENT_TYPE"] !== 'application/json') {
    exit(415); // Unsupported Media Type
}

// Read HTTP post JSON body
$data = json_decode(file_get_contents('php://input'), true);
if ($data === null) {
    exit(400); // Bad Request
}

// Connect to DB
$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$order_manager = new OrderManager($db);
$product_manager = new ProductManager($db);

// Check products availability
$products = $data["products"];
foreach ($products as $product) {
    product_manager->get_product_stock($product['product_id']);
}

// Check ship and billing address

//$data['delivery_city'];
//$data['delivery_country'];
//$data['delivery_name'];
//$data['delivery_phone'];
//$data['delivery_state'];
//$data['delivery_street'];
//$data['delivery_zip'];

//$data['billing_address'];
//$data['billing_city'];
//$data['billing_country'];
//$data['billing_email'];
//$data['billing_name'];
//$data['billing_phone'];
//$data['billing_title']; // Civilité
//$data['billing_zip'];

// Check client email address

//$data['email'];

// Check price

//$data["price_amount"];
//$data["price_unit"];

// Create order in database

$order_manager->create_order(
    // TODO
);

// Create Systempay order and sign it

$systempay_order = [
    // SILENT : correspond au cas où l’acquisition des données carte est effectuée par le commerçant
    // INTERACTIVE: correspond au cas où l’acquisition des données carte est déléguée à la plateforme.
    'vads_action_mode' => 'INTERACTIVE',

    // Montant de la transaction exprimé en son unité indivisible (en cents pour l'Euro).
    vads_amount,

    // Paramètre facultatif. Permet de spécifier les langues disponibles sur la page de paiement
    // (affichage des drapeaux sur la page de paiement).
    // vads_available_languages

    // Paramètre facultatif indiquant le délai en nombre de jours avant remise en
    // banque. Si ce paramètre n’est pas transmis, alors la valeur par défaut sera
    // utilisée. Cette dernière est paramétrable dans l’outil de gestion de caisse
    // Cyberplus Paiement par toutes les personnes dûment habilitées.
    // vads_capture_delay

    // Paramètre facultatif permettant de spécifier pour chaque réseau d’acceptation,
    // le contrat commerçant à utiliser. Le formalisme du paramètre est le suivant :
    // RESEAU1=contratReseau1;RESEAU2=contratReseau2;RESEAU3=contratReseau3
    // vads_contracts

    // Information complémentaire facultative destinée à indiquer le nom de la
    // contribution utilisée lors du paiement (joomla, oscommerce...). Si vous utilisez une
    // implémentation propriétaire, ce champ peut accueillir votre numéro de version
    // interne, par exemple.
    // vads_contrib

    // Paramètre obligatoire indiquant le mode de sollicitation de la plateforme
    // de paiement :
    // - TEST : utilisation du mode test, nécessite d’employer le certificat de test
    // pour la signature.
    // - PRODUCTION : utilisation du mode production, nécessite d’employer le
    // certificat de production pour la signature.
    'vads_ctx_mode' => 'TEST',

    // Paramètre obligatoire indiquant la monnaie à utiliser, selon la norme ISO 4217 (code numérique).
    'vads_currency' => '978', // Euro

    // Paramètre facultatif. Adresse postale du client
    //vads_cust_address,

    // Paramètre facultatif. Numéro de téléphone mobile du client (longueur
    // 32 caractères / type : alpha numérique)
    //vads_cust_cell_phone,

    // Paramètre facultatif. Ville du client
    //vads_cust_city,

    // Paramètre facultatif. Code pays du client à la norme ISO 3166.
    // http://www.iso.org/iso/english_country_names_and_code_elements
    // Pour la France, le code est FR.
    //vads_cust_country,

    // Paramètre facultatif. Adresse e-mail du client, nécessaire pour lui envoyer un mail
    // récapitulatif de la transaction
    // vads_cust_email

    // Paramètre facultatif. Identifiant du client chez le marchand.
    //vads_cust_id

    // Paramètre facultatif. Nom du client
    //vads_cust_name

    // Paramètre facultatif. Numéro de téléphone du client (longueur 32caractères /
    //type : alpha numérique)
    //vads_cust_phone

    // Paramètre facultatif. Civilité du client
    //vads_cust_title

    // Paramètre facultatif. Code postal du client
    //vads_cust_zip

    // Paramètre facultatif. Langue dans laquelle doit être affichée la page de
    // paiement (norme ISO 639-1).
    // Possible values: de, en, zh, es, fr, it, jp, pt
    'vads_language' => 'fr',

    // Paramètre facultatif. Numéro de commande qui pourra être rappelé dans l'e-
    // mail de confirmation de paiement adressé au client. Champ au format
    // alphanumérique. Seul le caractère spécial « - » est autorisé.
    vads_order_id,

    // Champs libres facultatifs pouvant par exemple servir à stocker un résumé de la
    // commande.
    //vads_order_info2, vads_order_info3
    vads_order_info => '', // TODO: Mettre les produits acheté ici

    // Ce paramètre est obligatoire et doit être valorisé à PAYMENT.
    'vads_page_action' => 'PAYMENT',

    // Ce paramètre facultatif contient la liste des types de cartes à proposer à
    // l’internaute, séparés par des " ;".
    // Si la liste ne contient qu'un type de carte, la page de saisie des données du
    // paiement sera directement présentée. Sinon la page de sélection du moyen de
    // paiement sera présentée.
    // Si ce paramètre est vide alors l’ensemble des moyens de paiement défini dans
    // l’outil de gestion de caisse sera présenté en sélection. Par défaut la valeur VIDE
    // est conseillée.
    //vads_payment_cards

    // Ce paramètre obligatoire indique le type du paiement :
    // - SINGLE indique un paiement unitaire.
    // - MULTI indique un paiement en plusieurs fois. Dans ce cas, le paramètre est
    // constitué de la chaîne « MULTI: », suivi par des paires clés/valeurs séparées par
    // des « ; ». Les paramètres sont les suivants :
    // - « first » indique le montant du premier paiement.
    // - « count » indique le nombre de paiements total.
    // - « period » indique l’intervalle en nombre de jours entre 2 paiements.
    'vads_payment_config' => 'SINGLE',

    // Paramètre facultatif permettant de spécifier le message en cas de
    // paiement refusé dans le cas d’une redirection automatique vers le site
    // marchand.
    //vads_redirect_error_message

    // Paramètre facultatif permettant de spécifier le délai avant redirection
    // vers le site marchand à la fin d’un paiement refusé.
    //vads_redirect_error_timeout

    // Paramètre facultatif permettant de spécifier le message à la fin d’un
    // paiement accepté dans le cas d’une redirection automatique vers le site
    // marchand.
    //ads_redirect_success_message

    // Paramètre facultatif permettant de spécifier le délai avant redirection
    // vers le site marchand à la fin d’un paiement accepté.
    //vads_redirect_success_timeout

    // Paramètre facultatif permettant de conditionner le passage des
    // paramètres aux URL de retour vers le site marchand.
    // - Non défini, vide ou NONE : aucun paramètre ne sera passé à l’URL de
    // retour.
    // - POST : les paramètres de retour seront transmis à l’URL de retour
    // sous la forme d’un formulaire HTTP POST.
    // - GET : les paramètres de retour seront transmis à l’URL de retour
    // sous la forme d’un formulaire HTTP GET (dans la « query string »).
    'vads_return_mode' => 'POST',

    // Paramètre facultatif. Adresse de livraison : Ville du client
    //vads_ship_to_city,

    // Paramètre facultatif. Adresse de livraison : Code pays du client à la
    // norme ISO 3166.
    // http://www.iso.org/iso/english_country_names_and_code_elements
    // Pour la France, le code est FR.
    //vads_ship_to_country,

    // Paramètre facultatif. Adresse de livraison : Nom du client
    //vads_ship_to_name,

    // Paramètre facultatif. Adresse de livraison : Numéro de téléphone du client
    //vads_ship_to_phone_num,

    // Paramètre facultatif. Adresse de livraison : Etat du client
    //vads_ship_to_state,

    // Paramètre facultatif. Adresse de livraison : Adresse du client
    //vads_ship_to_street,

    // Paramètre facultatif. Adresse de livraison : Deuxième ligne d’adresse du client
    //vads_ship_to_street2

    // Paramètre facultatif. Adresse de livraison : Code postal du client
    //vads_ship_to_zip,

    // Paramètre facultatif. Nom de boutique qui apparait dans les mails de
    // confirmation de paiement
    //'vads_shop_name' => 'PEVI',

    // Paramètre facultatif. URL de la boutique qui apparait sur la page de
    // paiement et les mails de confirmation de paiement.
    //vads_shop_url,

    // Paramètre obligatoire attribué lors de l'inscription à la plateforme de paiement.
    // Sa valeur est consultable sur l’interface de l’outil de gestion de caisse dans
    // l’onglet « Paramétrages » / « Boutique » par toutes les personnes habilitées.
    vads_site_id,

    // Paramètre facultatif permettant de personnaliser certains paramètres de la page
    // de paiement standard de la plateforme, comme les logos, bandeaux ainsi que
    // certains messages.
    //vads_theme_config

    // Ce paramètre est obligatoire. Correspond à l’horodatage au format
    // AAAAMMJJHHMMSS.
    // L’horodatage doit nécessairement correspondre à la date et heure courants,
    // dans le fuseau GMT (ou UTC) au format horaire 24h.
    vads_trans_date,

    // Ce paramètre est obligatoire. Il est constitué de 6 caractères numériques et doit
    // être unique pour chaque transaction pour une boutique donnée sur la journée.
    // En effet l'identifiant unique de transaction au niveau de la plateforme de
    // paiement est constitué du vads_site_id, de vads_trans_date restreint à la valeur
    // de la journée (partie correspondant à AAAAMMJJ) et de vads_trans_id. Il est à la
    // charge du site marchand de garantir cette unicité sur la journée. Il doit être
    // impérativement compris entre 000000 et 899999. La tranche 900000 et 999999 est
    // interdite.
    vads_trans_id,

    // Paramètre facultatif précisant le mode de validation de la transaction
    // (manuellement par le commerçant, ou automatiquement par la plateforme).
    // Valeurs:
    // - Absent ou vide: Configuration par défaut de la boutique retenue
    //   (paramétrable dans l’outil de gestion de caisse)
    // - 0: Validation automatique
    // - 1: Validation manuelle
    // vads_validation_mode

    // Paramètre obligatoire et devant être valorisé à V2.
    'vads_version' => 'V2',

    // Paramètre facultatif. URL où sera redirigé le client si celui-ci appuie sur " annuler
    // et retourner à la boutique " avant d'avoir procédé au paiement.
    //vads_url_cancel

    // Paramètre facultatif. URL où sera redirigé le client en cas d'erreur de traitement
    // interne.
    //vads_url_error

    // Paramètre facultatif. URL où sera redirigé le client en cas de refus d’autorisation
    // avec le code 02 « contacter l’émetteur de la carte », après appui du bouton "
    // retourner à la boutique ".
    //vads_url_referral

    // Paramètre facultatif. URL où sera redirigé le client en cas de refus pour toute
    // autre cause que le refus d’autorisation de motif 02 (contacter l’émetteur de la
    // carte), après appui du bouton " retourner à la boutique ".
    //vads_url_refused

    // Paramètre facultatif. URL où sera redirigé le client en cas de succès du paiement,
    // après appui du bouton " retourner à la boutique ".
    //vads_url_success

    // Paramètre facultatif. URL où sera redirigé par défaut le client après un appui sur
    // le bouton " retourner à la boutique ", si les URL correspondantes aux cas de figure
    // vus précédemment ne sont pas renseignées.
    // Si cette URL n’est pas présente dans la requête, alors c’est la configuration dans
    // l’outil de gestion de caisse qui sera prise en compte.
    // En effet il est possible de configurer des URL de retour, en mode TEST et en mode
    // PRODUCTION. Ces paramètres sont nommés « URL de retour de la boutique » et
    // « URL de retour de la boutique en mode test » respectivement, et sont accessibles
    // dans l’onglet « Configuration » lors du paramétrage d’une boutique.
    // Si toutefois aucune URL n’est présente, que ce soit dans la requête ou dans le
    // paramétrage de la boutique, alors le bouton « retourner à la boutique »
    // redirigera vers l’URL générique de la boutique (paramètre nommé « URL » dans la
    // configuration de la boutique).
    //vads_url_return
];

$systempay_order['signature'] = SystemPay::sign($systempay_order, SYSTEMPAY_SIGN_KEY);

// Reply

header('Content-Type: application/json; charset=utf-8');
echo json_encode($systempay_order);

?>
