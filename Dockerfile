FROM php:8.2-cli

# Installer les dépendances pour PostgreSQL, cron et autres utilitaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    cron \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
COPY --from=composer:2.8.3 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . /var/www/html

# Installer les dépendances PHP avec Composer
RUN composer install --no-interaction --optimize-autoloader

# Ajouter un script pour exécuter la commande toutes les 10 secondes
RUN chmod +x /var/www/html/cron-job.sh

# Commande de démarrage : lancer le cron en arrière-plan et le serveur PHP
CMD sh -c "/var/www/html/cron-job.sh & php -S 0.0.0.0:8082 -t public"
