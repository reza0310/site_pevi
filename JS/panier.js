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
