# hermione_choix

C'est le code source du site : [https://hermione.jeremielibeau.fr/](https://hermione.jeremielibeau.fr/)

Le projet est basé sur Symfony4 beta.

## Installation

```bash
git clone https://github.com/loconox/hermione_choix
composer install
```

## Configuration

Éditer le ficher .env et renseigner les variables suivantes :

```
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<une chaine très compliquée>

APP_QUESTIONNAIRE=<l'id du questionnaire askabox>
APP_RESULTAT=<id du resultat askabox>
APP_PASSWOR=<un mot de passe pour le compte 'hermione'>
DATABASE_URL="mysql://root@127.0.0.1:3306/hermione?charset=utf8mb4&serverVersion=5.6"
```

## Creation de la base de données

```bash
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
```

## Chargement des données

Pour charger les données dans la base de données il faut executer la commande suivante :

```bash
bin/console app:load -f
```