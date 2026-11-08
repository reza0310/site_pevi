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
            port = 1;
        } else if (soustotal < 50) {
            port = 1;
        } else if (soustotal < 100) {
            port = 1;
        } else if (soustotal < 150) {
            port = 1;
        } else if (soustotal < 200) {
            port = 1;
        } else if (soustotal < 250) {
            port = 1;
        } else {
            port = 9999;
        }
    } else if (sel.value == "GP" || sel.value == "GF" || sel.value == "BL" || sel.value == "MF" || sel.value == "PM" || sel.value == "MQ" || sel.value == "YT" || sel.value == "RE" || sel.value == "TF") {
        if (soustotal < 20) {
            port = 2;
        } else if (soustotal < 50) {
            port = 2;
        } else if (soustotal < 100) {
            port = 2;
        } else if (soustotal < 150) {
            port = 2;
        } else if (soustotal < 200) {
            port = 2;
        } else if (soustotal < 250) {
            port = 2;
        } else {
            port = 9999;
        }
    } else if (sel.value == "GB") {
        if (soustotal < 20) {
            port = 3;
        } else if (soustotal < 50) {
            port = 3;
        } else if (soustotal < 100) {
            port = 3;
        } else if (soustotal < 150) {
            port = 3;
        } else if (soustotal < 200) {
            port = 3;
        } else if (soustotal < 250) {
            port = 3;
        } else {
            port = 9999;
        }
    } else {
        if (soustotal < 20) {
            port = 4;
        } else if (soustotal < 50) {
            port = 4;
        } else if (soustotal < 100) {
            port = 4;
        } else if (soustotal < 150) {
            port = 4;
        } else if (soustotal < 200) {
            port = 4;
        } else if (soustotal < 250) {
            port = 4;
        } else {
            port = 9999;
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
