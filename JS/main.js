function addToCart(item) {
    let panier = JSON.parse(sessionStorage.getItem("panier"));
    console.log(panier);
    if (panier == null) {
	console.log(1);
	panier = [ item ];
    } else {
	console.log(2);
	panier.push(item);
    };
    sessionStorage.setItem("panier", JSON.stringify(panier));
    const cartElement = document.getElementById('cart-count');
    if (cartElement) cartElement.innerText = panier.length;
}
    

let panier = JSON.parse(sessionStorage.getItem("panier"));
const cartElement = document.getElementById('cart-count');
if (cartElement) cartElement.innerText = panier.length;
