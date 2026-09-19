function addToCart(item) {
    let panier = JSON.parse(sessionStorage.getItem("panier"));
    console.log(panier);
    if (panier != null && panier[item] != null) {
	panier[item] = panier[item] + 1;
    } else {
	if (panier == null) panier = {};
	panier[item] = 1;
    };
    sessionStorage.setItem("panier", JSON.stringify(panier));
    const cartElement = document.getElementById('cart-count');
    let taille = 0;
    for (const [key, value] of Object.entries(panier)) {
        taille += value;
    }
    if (cartElement) cartElement.innerText = taille;
}
    

let panier = JSON.parse(sessionStorage.getItem("panier"));
const cartElement = document.getElementById('cart-count');
let taille = 0;
for (const [key, value] of Object.entries(panier)) {
    taille += value;
}
if (cartElement) cartElement.innerText = taille;
