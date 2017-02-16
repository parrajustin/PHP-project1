#!/bin/bash


echo "Do you wish to install this program?"
select yn in "setup" "start" "stop" "delete"; do
    case $yn in
        setup ) rm -rf /home/jparra/web/*; cp -a /home/jparra/git/PHP-project1/. /home/jparra/web/; chown -R www-data /home/jparra/web/; chmod -R 775 /home/jparra/web/;  exit;;
        start ) sudo service php7.0-fpm stop; sudo service php7.0-fpm start; sudo service nginx restart; exit;;
        stop ) sudo service php7.0-fpm stop; sudo service nginx stop; exit;;
        delete ) rm -rf /home/jparra/web/*; exit;;
    esac
done
