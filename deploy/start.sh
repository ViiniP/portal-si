#!/bin/sh
set -eu
# O Render monta arquivos secretos para root; o Apache atende como www-data.
# Copiamos somente o certificado CA público, sem mover senhas ou chaves privadas.
if [ -n "${DB_SSLROOTCERT:-}" ]; then
    install -d -m 755 /var/run/portal-si
    install -m 644 "$DB_SSLROOTCERT" /var/run/portal-si/database-ca.pem
    export DB_SSLROOTCERT=/var/run/portal-si/database-ca.pem
fi
php /var/www/html/database/migrate.php
exec apache2-foreground
