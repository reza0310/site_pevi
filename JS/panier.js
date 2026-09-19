function up_addresse() {
    let af = document.getElementById("af");
    if (document.getElementById("same").checked) af.style.display = "none"
    else af.style.display = "block";
}

(async () => {
    try {
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
    } catch (e) {
        console.log("I fucked up");
        console.log(e);
    }
})();
