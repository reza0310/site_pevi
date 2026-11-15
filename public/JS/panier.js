function up_addresse() {
    let af = document.getElementById("af");
    if (document.getElementById("same").checked) af.style.display = "none"
    else af.style.display = "block";
}

async function empty_panier() {
    sessionStorage.removeItem("panier");
    await start();
}

function update_port() {
    let sel = document.getElementById("select");
    let soustotal = parseFloat(document.getElementById("sous-total").innerHTML);
    let port = 0;
    if (sel.value == "FR") {
        if (soustotal < 20) {
            // 1 jeu
            // 122g
            port = 5.49+0.61+0.1; // Port + carton + papier bulle / mise en boîte
        } else if (soustotal < 50) {
            // 3 jeux
            // 455g
            port = 7.59+2.29+3*0.1; // Port + carton + papier bulle / mise en boîte
        } else if (soustotal < 100) {
            // 6 jeux
            // 740g
            port = 9.29+2.29+6*0.1; // Port + carton + papier bulle / mise en boîte
        } else if (soustotal < 150) {
            // 9 jeux
            // 1025g
            port = 11.19+2.29+9*0.1; // Port + carton + papier bulle / mise en boîte
        } else if (soustotal < 200) {
            // 12 jeux
            // 1310g
            port = 11.19+2.29+12*0.1; // Port + carton + papier bulle / mise en boîte
        } else if (soustotal < 250) {
            // 16 jeux
            // 1690g
            port = 11.19+2.29+16*0.1; // Port + carton + papier bulle / mise en boîte
        } else {
            port = 0;
        }
    } else if (sel.value == "GP" || sel.value == "GF" || sel.value == "BL" || sel.value == "MF" || sel.value == "PM" || sel.value == "MQ" || sel.value == "YT" || sel.value == "RE" || sel.value == "TF") {
        if (soustotal < 20) {
            port = 12.02+0.61+0.1;
        } else if (soustotal < 50) {
            port = 12.02+2.29+3*0.1;
        } else if (soustotal < 100) {
            port = 19+2.29+6*0.1;
        } else if (soustotal < 150) {
            port = 25.89+2.29+9*0.1;
        } else if (soustotal < 200) {
            port = 25.89+2.29+12*0.1;
        } else if (soustotal < 250) {
            port = 25.89+2.29+16*0.1;
        } else {
            port = 0;
        }
    } else if (sel.value == "GB") {
        if (soustotal < 20) {
            port = 18.99+0.61+0.1;
        } else if (soustotal < 50) {
            port = 18.99+2.29+3*0.1;
        } else if (soustotal < 100) {
            port = 23.39+2.29+6*0.1;
        } else if (soustotal < 150) {
            port = 26.19+2.29+9*0.1;
        } else if (soustotal < 200) {
            port = 26.19+2.29+12*0.1;
        } else if (soustotal < 250) {
            port = 26.19+2.29+16*0.1;
        } else {
            port = 0;
        }
    } else {
        if (soustotal < 20) {
            port = 14.99+0.61+0.1;
        } else if (soustotal < 50) {
            port = 14.99+2.29+3*0.1;
        } else if (soustotal < 100) {
            port = 19.39+2.29+6*0.1;
        } else if (soustotal < 150) {
            port = 22.19+2.29+9*0.1;
        } else if (soustotal < 200) {
            port = 22.19+2.29+12*0.1;
        } else if (soustotal < 250) {
            port = 22.19+2.29+16*0.1;
        } else {
            port = 0;
        }
    }
    document.getElementById("port").innerHTML = port;
    document.getElementById("total").innerHTML = port + soustotal;
}

async function start() {
    try {
        let panier = JSON.parse(sessionStorage.getItem("panier"));
        let soustotal = 0;
        let liste_texte = "";
        if (panier != null) {
            for (const [key, value] of Object.entries(panier)) {
                const response = await fetch("/DATA/"+key.toString()+".json");
                if (!response.ok) {
                    throw new Error(`Response status: ${response.status}`);
                }
                const result = await response.json();
                liste_texte += "<tr><td>" + result.name + "</td><td>" + result.price + "€</td><td>" + value + "</td><td>" + result.price * value + "€</td></tr>";
                soustotal += result.price * value;
                console.log(result);
            }
        }
        document.getElementById("liste").innerHTML = liste_texte;
        document.getElementById("sous-total").innerHTML = soustotal;
        up_addresse();
        update_port();
        document.getElementById("same").addEventListener("change", up_addresse);
        document.getElementById("select").addEventListener("change", update_port);
    } catch (e) {
        console.log("I fucked up");
        console.log(e);
    }
}

(async () => {
    await start();
})();

async function init() {
    let panier = JSON.parse(sessionStorage.getItem("panier"));
    let total = 0;
    let liste_texte = "";
    if (panier !== null) {
        for (const [key, value] of Object.entries(panier)) {
            const response = await fetch("/DATA/"+key.toString()+".json");
            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }
            const result = await response.json();
            liste_texte += "<tr><td>" + result.name + "</td><td>" + result.price + "€</td><td>" + value + "</td><td>" + result.price * value + "€</td></tr>"
            console.log(result);
        }
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


// async function checkout() {
//     let a = 'https://systempay.cyberpluspaiement.com/vads-payment/';
// }


// Only run init function if DOM is fully loaded
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
