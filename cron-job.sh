#!/bin/bash
while true; do
    # Commande à exécuter
    echo "Exécution à $(date)"
    php /var/www/html/bin/console app:sync-firestore
    php /var/www/html/bin/console app:random-crypto

    sleep 10
done
