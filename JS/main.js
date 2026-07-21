// --- Gestion du Panier ---
let cartCount = 0;

function addToCart() {
    cartCount++;
    const cartElement = document.getElementById('cart-count');
    if (cartElement) cartElement.innerText = cartCount;
    alert("Produit ajouté avec succès au panier !");
}

// --- Gestion des onglets du Profil ---
function openTab(evt, tabName) {
    let tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
    }
    
    let tabLinks = document.getElementsByClassName("tab-link");
    for (let i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
    }
    
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.classList.add("active");
}
