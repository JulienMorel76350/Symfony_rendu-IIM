# Exercice E-commerce

__Vous devez réaliser une boutique e-commerce en groupes de 3 maximum sur le thème de votre choix.__

Le paiement des paniers peut être fictif.

## Conseils

- Utilisez GitHub,
- Mettez à jour vos versions *PHP* pour avoir la même pour tous les membres de votre groupe.

## Création du projet
Pour travailler à plusieurs sur le projet, il faut qu’un des membres crée le projet et le partage sur GitHub, puis que le second membre récupère ce projet.

A partir de là vous pouvez commencer à coder.

### Procédure de récupération d'un projet existant

1. `git clone URL`
2. `cd dossier_projet`
3. `composer install`
4. Dupliquer le fichier `.env` en le renommant en `.env.local` et y mettre à jour les accès à la base de données
5. `php bin/console d:d:c`
6. S'il y a eu des migrations de faites, les executer : `php bin/console d:m:m`

## Entités
Vous aurez besoin des entités suivantes :

### Produit
Vous devez avoir une entité produit qui contient les informations suivantes :

- Nom
- Description
- Prix
- Stock
- Photo

### Utilisateur
Vous devez stocker :

- Nom
- Prénom
- Email
- Mot de passe
- Roles

### Panier
Cette entité stock les achats des utilisateurs.

Vous devez y stocker :

- Utilisateur (relation vers la table Utilisateur)
- Date d’achat (la date de l’achat)
- Etat (false par défaut, true dès que c’est payé)

### ContenuPanier
Cette entité contient le contenu de chaque panier.

Vous devez y stocker :

- Produit (relation vers la table Produit)
- Panier (relation vers la table Panier)
- Quantité (quantité commandée)
- Date (date de l’ajout au panier)

## Pages
Vous devez réaliser les pages suivantes :

### Accueil
Liste des produits.

Au clic d’un produit on arrive sur la fiche du produit.

Si vous êtes connecté avec un rôle `ROLE_ADMIN`, vous devez pouvoir ajouter un produit sur cette page.

### Fiche produit
Cette page affiche le contenu d’un produit.

Il doit être possible d’ajouter le produit au panier à partir de cette page à condition d’être connecté. L’utilisateur connecté doit automatiquement être attribué à la commande.

Si vous êtes connecté avec un rôle `ROLE_ADMIN`, vous devez pouvoir modifier ou supprimer le produit.

### Panier
Cette page liste les produits du panier de l’utilisateur.

Attention à afficher uniquement les produits du panier en cours (non payé) de l’internaute connecté.

Chaque ligne du panier doit posséder les informations suivantes :

- Nom du produit
- Quantité commandée
- Prix du produit
- Montant de la ligne,
- Un bouton de suppression du produit du panier.

La page doit afficher le montant total du panier et un bouton permettant d’acheter le panier.

### Mon compte
Cette page doit permettre à l’internaute de modifier son profil et doit afficher l’historique de ses commandes.

L’historique des commandes doit afficher pour chaque ligne :

- L’identifiant de la commande,
- Le montant de la commande,
- La date de la commande (stockée automatiquement en base, ça ne doit apparaitre dans aucun formulaire).

Au clic d’une commande, on doit arriver sur une page qui affiche le contenu de la commande.

### Contenu de la commande
Cette page affiche le contenu de la commande.

Vous pouvez utiliser le même tableau que la page Panier, en affichant en plus, la date de la commande (et sans l’option d’achat).

## Le SUPER_ADMIN
Vous devez mettre en place un rôle `ROLE_SUPER_ADMIN` qui doit posséder les même droits que le `ROLE_ADMIN` mais il doit pouvoir en plus :

1. lister les paniers non achetés, avec pour chacun :
	- l'utilisateur à qui appartient le panier,
	- le numéro du panier,
	- le contenu du panier
2. afficher les utilisateurs inscrits aujourd'hui du plus récent au plus ancien

## Contraintes

- Les menus et pages doivent être adaptés au rôle de l'internaute connecté.
- Les pages d’erreur doivent être personnalisées.
- Les formulaires doivent faire l’objet de validation des données.
- Vous devez utiliser des messages Flash indiquant le succès ou l’échec des actions.
- Tous les textes, en dehors de ceux stockés dans la base de données, doivent être traduits dans 2 langues de votre choix.
- L'utilisation du `bundle` _EasyAdmin_ n'est pas autorisé.

## Deadline
Le devoir toit être rendu complet via Github (identifiant : `yoannncoualan`) au plus tard dimanche 31 mars 2024 à 23h59.

N’oubliez pas de me préciser les membres de votre groupe dans le `README.md` au moment du rendu.;


GROUPE : JULIEN MOREL 