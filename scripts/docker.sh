#!/bin/bash
docker compose exec php bash -c "cd /var/www/lumen-app && exec bash"
