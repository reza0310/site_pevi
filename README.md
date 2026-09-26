# Site web de PEVI

## TODO

- Ajouter une section "A propos" qui explique ce qu'est PEVI.
- Générer un sitemap.
- Créer un `robot.txt`.
- Faire le système de payement.
- Réduire la taille la la policé d'écriture "Font Awesome" en retirant les symbols inutilisé (possible via la bibliothèque Python pyftsubset par example).
- Générer plusieurs taille pour chaque image et utilisé des images responsibe dans le HTML pour accéléré le chargement des images quand le client n'a pas besoin de l'image en plein dimension.
- Ajouter des balises meta pour une meilleur intégration avec les logiciels et les moteurs de recherche (tags OpenGraph, Apple, Twitter, Discord, ...).
Ressources: https://www.geeksforgeeks.org/websites-apps/10-most-important-meta-tags-for-seo/, https://gist.github.com/whitingx/3840905
- Minifier le JS, CSS et HTML ?
- Désactiver l'autoindex (le faite de lister les fichier quand l'url est un dossier) sur l'hebergement web.

## Système de payement

### Checklist de sécurité

1. Le backend doit validé la disponibilité des produits ainsi que la validité des informations de livraison ou autre informations de payement, ensuite seulement le serveur peux signé ces informations pour garentire leur authenticité et intégrité au près de la platforme de payement qui doit vérifier la signature. Le payement ne doit donc pas être possible sans l'accord du backend.

2. Les informations de payement sont privé de ne doivent donc pas fuité.

3. Les payement sont nominative, il ne devrais pas être possible pour quelqu'un de payer au nom de pour quelqu'un d'autre sans sont accord -> assuré par la platform de payement.

4. Une commande peut être validé si et seulement si la transaction à réussi. Il ne doit donc pas être possible de faire "croire" au serveur que la réaction à réussi alors que ce n'est pas le cas (par example via une signature de la transaction coté platforme de payement qui serat vérifié coter backend).

5. S'assuré que le backend soit forcément mit au courent d'une transaction réussi sur platforme de payement (même en cas d'indisponibilité temporaire du backend, donc la platforme de payement doit périodiquement tenté de contacter le backend en cas de probleme).

6. S'assuré que chaque commande est unique et donc qu'une transaction mène à seulement une et unique commande (implique de pouvoir identifié de manière unique chaque commandes).

7. Une commande ne doit pas pouvoir être altéré sans l'autorisation de la perssone ayant passer la commande (que ce soit avant ou après le payement).

### User stories

**En tend que client:**
- Je veux recevoir ma commande intacte, à la bonne addresse et dans le delais indiqué si j'ai payer.
- Je veux payer le montant affiché.
- Je ne veux pas que mes informations perssonel et banquaire fuite.
- Je veux recevoir un email de confirmation ou d'echec de commande qui me permette de suivre la commande.
- Je doit pouvoir obtenir des information sur l'état de ma commande au près de PEVI

**En tend que marchant:**
- Je ne veux pas qu'une commande soit validé sans qu'un virement du bon motant de la part du client ai été éffectué avec succès.
- Je ne veux qu'une commande soit dupliqué (un virement égale une commande).
- Je ne veux pas qu'une commande soit passé si les stoque ne le permette pas.
- Je ne veux pas qu'une commande soit passé avec une addresse de livraison invalide.
- Je veux pouvoir identifié chaque commande par un identifiant unique.

**En tend que perssone lambda:**
- Je ne veux pas que mon argent ai été utilisé sans mon accord pour passé une commande chez PEVI.
