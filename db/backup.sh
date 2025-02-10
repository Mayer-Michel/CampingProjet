#!/usr/bin/sh
mariadb-dump video_games -uroot -psuperAdmin > /root/init.sql
echo "Sauvegarde terminée"