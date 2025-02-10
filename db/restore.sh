#!/usr/bin/sh

mariadb video_games -uroot -psuperAdmin < /root/init.sql
echo "Restauration terminée"
