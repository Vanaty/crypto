#!/bin/sh

# Se rendre dans le dossier du projet
cd /var/www/html

# Installer les dépendances Composer
composer install --no-interaction --optimize-autoloader

# Effectuer la migration de la base de données
#php bin/console doctrine:migrations:migrate --no-interaction

# Vider le cache Symfony
php bin/console cache:clear

# Démarrer le serveur PHP interne
php -S 0.0.0.0:8082 -t public
