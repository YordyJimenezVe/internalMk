#!/bin/sh
set -e

if [ ! -f /var/www/public/index.php ]; then
    echo '[Nginx-Entrypoint] ERROR: /var/www/public/index.php no encontrado (volumen WSL aun no montado).' >&2
    echo '[Nginx-Entrypoint] Saliendo con codigo 1 para que Docker reinicie el contenedor...' >&2
    sleep 3
    exit 1
fi

echo '[Nginx-Entrypoint] Volumen /var/www listo y verificado.'
