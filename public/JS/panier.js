function up_addresse() {
    let af = document.getElementById("af");
    if (document.getElementById("same").checked) af.style.display = "none"
    else af.style.display = "block";
}


async function init() {
    let panier = JSON.parse(sessionStorage.getItem("panier"));
    let total = 0;
    let liste_texte = "";
    for (const [key, value] of Object.entries(panier)) {
        const response = await fetch("/DATA/"+key.toString()+".json");
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const result = await response.json();
        liste_texte += "<tr><td>" + result.name + "</td><td>" + result.price + "€</td><td>" + value + "</td><td>" + result.price * value + "€</td></tr>"
        console.log(result);
    }
    document.getElementById("liste").innerHTML = liste_texte;
    up_addresse();
    document.getElementById("same").addEventListener("change", up_addresse);
}


function systempay_result_code_to_string(code) {
    return {
        0: "Paiement réalisé avec succès.",
        2: "Le commerçant doit contacter la banque du porteur.",
        5: "Paiement refusé.",
        17: "Annulation client.",
        30: "Erreur de format de la requête. A mettre en rapport avec la valorisation du champ vads_extra_result.",
        96: "Erreur technique lors du paiement.",
    }[code];
}


function systempay_bank_result_code_to_string(code) {
    return {
         0: "Transaction approuvée ou traitée avec succès",
         2: "Contacter l’émetteur de carte",
         3: "Accepteur invalide",
         4: "Conserver la carte",
         5: "Ne pas honorer",
         7: "Conserver la carte, conditions spéciales",
         8: "Approuver après identification",
        12: "Transaction invalide",
        13: "Montant invalide",
        14: "Numéro de porteur invalide",
        30: "Erreur de format",
        31: "Identifiant de l’organisme acquéreur inconnu",
        33: "Date de validité de la carte dépassée",
        34: "Suspicion de fraude",
        41: "Carte perdue",
        43: "Carte volée",
        51: "Provision insuffisante ou crédit dépassé",
        54: "Date de validité de la carte dépassée",
        56: "Carte absente du fichier",
        57: "Transaction non permise à ce porteur",
        58: "Transaction interdite au terminal",
        59: "Suspicion de fraude",
        60: "L’accepteur de carte doit contacter l’acquéreur",
        61: "Montant de retrait hors limite",
        63: "Règles de sécurité non respectées",
        68: "Réponse non parvenue ou reçue trop tard",
        90: "Arrêt momentané du système",
        91: "Émetteur de cartes inaccessible",
        96: "Mauvais fonctionnement du système",
        94: "Transaction dupliquée",
        97: "Échéance de la temporisation de surveillance globale",
        98: "Serveur indisponible routage réseau demandé à nouveau",
        99: "Incident domaine initiateur",
    }[code];
}


async function checkout() {
    let a = 'https://systempay.cyberpluspaiement.com/vads-payment/';
}


// Only run init function if DOM is fully loaded
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
