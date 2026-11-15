class Basket {
    constructor() {
        this.checkout_form_elem = document.getElementById('checkout-form');

        this.empty_basket_btn = document.getElementById('empty-basket-btn');

        this.af = document.getElementById('address-facturation');
        this.af_same = document.getElementById('shipping-billing-same');
        this.af_required_inputs = [
            document.getElementById('billing-postal-code'),
            document.getElementById('billing-city'),
            document.getElementById('billing-country'),
            document.getElementById('billing-family-name'),
            document.getElementById('billing-given-name'),
        ];

        this.shipping_country_elem = document.getElementById('shipping-country');

        this.subtotal_price_elem = document.getElementById('sous-total');
        this.shipping_price_elem = document.getElementById('port');
        this.total_price_elem = document.getElementById('total')

        this.products_list_elem = document.getElementById('products-list');

        this.shipping_billing_same_elem = document.getElementById('shipping-billing-same');

        this.summary_shipping_addr_elem = document.getElementById('summary-shipping-addr');
        this.summary_billing_addr_elem = document.getElementById('summary-billing-addr');
        this.summary_products_list_elem = document.getElementById('summary-products-list');
        this.summary_subtotal_price_elem = document.getElementById('summary-subtotal-price');
        this.summary_shipping_price_elem = document.getElementById('summary-shipping-price');
        this.summary_total_price_elem = document.getElementById('summary-total-price');

        this.confirm_popup_elem = document.getElementById('confirm-popup');
        this.confirm_popup_close_btn = document.getElementById('confirm-popup-close-btn');

        this.pay_by_transfer_btn = document.getElementById('pay-by-transfer-btn');
        this.pay_by_card_btn = document.getElementById('pay-by-card-btn');

        this.confirm_popup = new Popup(this.confirm_popup_elem);
    }


    up_addresse() {
        if (this.af_same.checked) {
            this.af.style.display = 'none';
            for (const e of this.af_required_inputs) {
                e.required = false;
            }
        } else {
            this.af.style.display = 'block';
            for (const e of this.af_required_inputs) {
                e.required = true;
            }
        }
    }


    update_port() {
        let soustotal = parseFloat(document.getElementById('sous-total').innerHTML);
        let port = 0;
        const country = this.shipping_country_elem.value;
        if (country == 'FR') {
            if (soustotal < 20) {
                // 1 jeu
                // 122g
                port = 549+61+10; // Port + carton + papier bulle / mise en boîte
            } else if (soustotal < 50) {
                // 3 jeux
                // 455g
                port = 759+229+3*10; // Port + carton + papier bulle / mise en boîte
            } else if (soustotal < 100) {
                // 6 jeux
                // 740g
                port = 929+229+6*10; // Port + carton + papier bulle / mise en boîte
            } else if (soustotal < 150) {
                // 9 jeux
                // 1025g
                port = 1119+229+9*10; // Port + carton + papier bulle / mise en boîte
            } else if (soustotal < 200) {
                // 12 jeux
                // 1310g
                port = 1119+229+12*10; // Port + carton + papier bulle / mise en boîte
            } else if (soustotal < 250) {
                // 16 jeux
                // 1690g
                port = 1119+229+16*10; // Port + carton + papier bulle / mise en boîte
            } else {
                port = 0;
            }
        } else if (country == 'GP' || country == 'GF' || country == 'BL' || country == 'MF' || country == 'PM' || country == 'MQ' || country == 'YT' || country == 'RE' || country == 'TF') {
            if (soustotal < 20) {
                port = 1202+61+10;
            } else if (soustotal < 50) {
                port = 1202+229+3*10;
            } else if (soustotal < 100) {
                port = 19+229+6*10;
            } else if (soustotal < 150) {
                port = 2589+229+9*10;
            } else if (soustotal < 200) {
                port = 2589+229+12*10;
            } else if (soustotal < 250) {
                port = 2589+229+16*10;
            } else {
                port = 0;
            }
        } else if (country == 'GB') {
            if (soustotal < 20) {
                port = 1899+61+10;
            } else if (soustotal < 50) {
                port = 1899+229+3*10;
            } else if (soustotal < 100) {
                port = 2339+229+6*10;
            } else if (soustotal < 150) {
                port = 2619+229+9*10;
            } else if (soustotal < 200) {
                port = 2619+229+12*10;
            } else if (soustotal < 250) {
                port = 2619+229+16*10;
            } else {
                port = 0;
            }
        } else {
            if (soustotal < 20) {
                port = 1499+61+10;
            } else if (soustotal < 50) {
                port = 1499+229+3*10;
            } else if (soustotal < 100) {
                port = 1939+229+6*10;
            } else if (soustotal < 150) {
                port = 2219+229+9*10;
            } else if (soustotal < 200) {
                port = 2219+229+12*10;
            } else if (soustotal < 250) {
                port = 2219+229+16*10;
            } else {
                port = 0;
            }
        }
        this.shipping_price_elem.innerHTML = port / 100;
        this.total_price_elem.innerHTML = port / 100 + soustotal;
    }


    async update_all() {
        let panier = JSON.parse(sessionStorage.getItem('panier'));
        let soustotal = 0;
        let liste_texte = '';
        if (panier != null) {
            for (const [key, value] of Object.entries(panier)) {
                const response = await fetch('/DATA/'+key.toString()+'.json');
                if (!response.ok) {
                    throw new Error(`Response status: ${response.status}`);
                }
                const result = await response.json();
                liste_texte += '<tr><td>' + result.name + '</td><td>' + result.price / 100 + '€</td><td>' + value + '</td><td>' + result.price * value / 100 + '€</td></tr>';
                soustotal += result.price * value;
                console.log(result);
            }
        }
        this.products_list_elem.innerHTML = liste_texte;
        this.subtotal_price_elem.innerHTML = soustotal / 100;
        this.up_addresse();
        this.update_port();
        this.shipping_billing_same_elem.addEventListener('change', () => this.up_addresse());
        this.shipping_country_elem.addEventListener('change', () => this.update_port());
    }


    async empty_basket() {
        sessionStorage.removeItem('panier');
        await this.update_all();
    }


    checkout() {
        const form_data = new FormData(this.checkout_form_elem);

        //console.log(form_data);

        // 1. Récupération de l'adresse de livraison
        const civLivraison = form_data.get('shipping-civil-status') === 'FEMALE' ? 'Mme' : 'M.';
        const paysLivraison = form_data.get('shipping-country');
        const voieLivraison = form_data.get('shipping-street') || form_data.get('shipping-additional-street');

        this.summary_shipping_addr_elem.innerHTML = `
            <strong>${civLivraison} ${form_data.get('shipping-given-name')} ${form_data.get('shipping-family-name')}</strong><br>
            ${voieLivraison}<br>
            ${form_data.get('shipping-postal-code')} ${form_data.get('shipping-city')}<br>
            ${paysLivraison}
        `;

        // 2. Récupération de l'adresse de facturation
        const is_same_addr = this.shipping_billing_same_elem.checked;
        if (is_same_addr) {
            this.summary_billing_addr_elem.innerHTML = this.summary_shipping_addr_elem.innerHTML;
        } else {
            const civFacturation = form_data.get('billing-civil-status') === 'FEMALE' ? 'Mme' : 'M.';
            const paysFacturation = form_data.get('billing-country');
            const voieFacturation = form_data.get('billing-street') || form_data.get('billing-additional-street');

            this.summary_billing_addr_elem.innerHTML = `
                <strong>${civFacturation} ${form_data.get('billing-given-name')} ${form_data.get('billing-family-name')}</strong><br>
                ${voieFacturation}<br>
                ${form_data.get('billing-postal-code')} ${form_data.get('billing-city')}<br>
                ${paysFacturation}
            `;
        }

        // Copie des produits du panier et des totaux
        this.summary_products_list_elem = this.products_list_elem.innerHTML;
        this.summary_subtotal_price_elem.textContent = this.subtotal_price_elem.textContent;
        this.summary_shipping_price_elem.textContent = this.shipping_price_elem.textContent;
        this.summary_total_price_elem.textContent = this.total_price_elem.textContent;

        // Affichage de la popup
        this.confirm_popup.show();
    }


    close_confirm_popup() {
        this.confirm_popup.hide();
    }


    async confirm_order(payment_method) {
        this.confirm_popup.hide();
        await this.empty_basket(); // Vide le panier une fois la commande validée
        // TODO
    }


    async init() {
        this.checkout_form_elem.addEventListener('submit', (ev) => {
            ev.preventDefault();
            this.checkout();
            return false;
        });

        this.confirm_popup_close_btn.addEventListener('click', (ev) => {
            this.close_confirm_popup();
        });

        this.pay_by_transfer_btn.addEventListener('click', (ev) => {
            this.confirm_order("transfer");
        });

        this.pay_by_card_btn.addEventListener('click', (ev) => {
            this.confirm_order("card");
        });

        this.empty_basket_btn.addEventListener('click', (ev) => {
            this.empty_basket();
        });

        await this.update_all();
    }


    systempay_result_code_to_string(code) {
        return {
            0: 'Paiement réalisé avec succès.',
            2: 'Le commerçant doit contacter la banque du porteur.',
            5: 'Paiement refusé.',
            17: 'Annulation client.',
            30: 'Erreur de format de la requête. A mettre en rapport avec la valorisation du champ vads_extra_result.',
            96: 'Erreur technique lors du paiement.',
        }[code];
    }


    systempay_bank_result_code_to_string(code) {
        return {
            0: 'Transaction approuvée ou traitée avec succès',
            2: 'Contacter l’émetteur de carte',
            3: 'Accepteur invalide',
            4: 'Conserver la carte',
            5: 'Ne pas honorer',
            7: 'Conserver la carte, conditions spéciales',
            8: 'Approuver après identification',
            12: 'Transaction invalide',
            13: 'Montant invalide',
            14: 'Numéro de porteur invalide',
            30: 'Erreur de format',
            31: 'Identifiant de l’organisme acquéreur inconnu',
            33: 'Date de validité de la carte dépassée',
            34: 'Suspicion de fraude',
            41: 'Carte perdue',
            43: 'Carte volée',
            51: 'Provision insuffisante ou crédit dépassé',
            54: 'Date de validité de la carte dépassée',
            56: 'Carte absente du fichier',
            57: 'Transaction non permise à ce porteur',
            58: 'Transaction interdite au terminal',
            59: 'Suspicion de fraude',
            60: 'L’accepteur de carte doit contacter l’acquéreur',
            61: 'Montant de retrait hors limite',
            63: 'Règles de sécurité non respectées',
            68: 'Réponse non parvenue ou reçue trop tard',
            90: 'Arrêt momentané du système',
            91: 'Émetteur de cartes inaccessible',
            96: 'Mauvais fonctionnement du système',
            94: 'Transaction dupliquée',
            97: 'Échéance de la temporisation de surveillance globale',
            98: 'Serveur indisponible routage réseau demandé à nouveau',
            99: 'Incident domaine initiateur',
        }[code];
    }


    // async checkout() {
    //     let a = 'https://systempay.cyberpluspaiement.com/vads-payment/';
    // }
}


// Only run init function if DOM is fully loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        (new Basket()).init();
    });
} else {
    (new Basket()).init();
}
