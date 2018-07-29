FROM webdevops/php-apache:7.2

ENV WEB_DOCUMENT_ROOT=/var/www/public
ENV WEB_DOCUMENT_INDEX=index.php

# xdebug
RUN apt-get update
RUN pecl install xdebug
ADD ./docker-compose/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# apt cleanup
RUN apt-get clean && \
    rm -rf /tmp/* /var/tmp/*

EXPOSE 80

CMD ["supervisord"]