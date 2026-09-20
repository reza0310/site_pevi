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

function init_basket() {
    let basket_json = sessionStorage.getItem("panier");
    if (basket_json === null) return;
    let basket_data = JSON.parse(basket_json);
    const cartElement = document.getElementById('cart-count');
    let taille = 0;
    for (const [key, value] of Object.entries(basket_data)) {
        taille += value;
    }
    if (cartElement) cartElement.innerText = taille;
}

function close_banner() {
    Array.from(document.getElementsByClassName("banner")).forEach(element => {
        element.remove();
    });;
}

function init() {
    Array.from(document.getElementsByClassName("banner-close")).forEach(element => {
        element.addEventListener("click", close_banner);
    });

    init_basket();
}

// Only run init function if DOM is fully loaded
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
