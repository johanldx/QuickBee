# QuickBee
QuickBee est un service de gestion de facturation tout-en-un. Il permet de générer des factures et des devis, en plus de gérer les clients et les produits. Il est également accessible via une API pour une intégration complète avec les applications des utilisateurs.

QuickBee est un projet étudiant en groupe de 3 validant la première année à l'[ESGI](https://www.esgi.fr/). Il doit être développé en PHP sans framework et en suivant le cahier des charges de validation de projet.

## Table des Matières

- Installation
- Configuration
- Utilisation
- Auteurs et Remerciements

## Installation

1. Clonez le dépôt :
   
    ```sh
    git clone https://github.com/johanldx/QuickBee.git
    cd Quickbee-main
    ```

2. Installez les dépendances avec Composer :

    ```sh
    composer install
    ```

2. Créez le fichier `.env` et configurez vos variables d'environnement selon vos besoins :

    ```dotenv
    # DATABASE
    DB_HOST=
    DB_PORT=
    DB_NAME=
    DB_USER=
    DB_PASS=
    
    # PATH
    URL_PATH=
    
    # SMTP
    HOST=
    SMTP_AUTH=
    USERNAME=
    PASSWORD=
    SMTP_SECURE=
    PORT=
    
    # STRIPE
    STRIPE_API_KEY=
    STRIPE_WEBHOOK_SECRET=
    
    # PDF encryption key
    ENCRYPTION_KEY=
    ```

## Configuration

Pour lancer l'environnement de développement, il vous faudra installer MAMP et configurer les fichiers .env et .gitignore en dehors du répertoire **www**.

## Auteurs et Remerciements
- Johan Ledoux - Chef de projet & Développeur full-stack
- Killian Bidaux - Développeur full-stack
- Vincent Juillet - Développeur full-stack

- Artus de Salaberry, pour son aide pour le graphisme.
