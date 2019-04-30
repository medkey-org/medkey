FROM php:7.3-apache

RUN apt-get update

RUN apt-get install -y libzip-dev libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpq-dev libc-client-dev libkrb5-dev libicu-dev zlib1g-dev git nano

RUN docker-php-ext-install pgsql pdo_pgsql zip bcmath intl

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
	&& docker-php-ext-install imap

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/  &&  \
    docker-php-ext-install gd

# конфигурация xdebug
#RUN if [ -z ${http_proxy} ]; then echo "http_proxy is unset"; else pear config-set http_proxy $http_proxy; fi
#RUN pecl install xdebug
#RUN docker-php-ext-enable xdebug
#RUN sed -i '1 a xdebug.remote_autostart=true' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN sed -i '1 a xdebug.remote_mode=req' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN sed -i '1 a xdebug.remote_handler=dbgp' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN sed -i '1 a xdebug.remote_connect_back=Off' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN sed -i '1 a xdebug.remote_port=9100' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN sed -i '1 a xdebug.remote_enable=on' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#RUN sed -i '1 a xdebug.idekey=PHPSTORM' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN mkdir -p ~/.ssh
#COPY .ssh/yii2queue /root/.ssh/id_rsa
#COPY .ssh/yii2queue.pub /root/.ssh/id_rsa.pub
#RUN ls /root/.ssh/ -la
#RUN chmod 600 /root/.ssh/id_rsa

RUN ssh-keyscan -t rsa 127.0.0.1 >> /root/.ssh/known_hosts
RUN ssh-keyscan github.com >> ~/.ssh/known_hosts
RUN rm -rf /etc/apache2/sites-enabled/000-default.conf
COPY docker/backend/medkey.conf /etc/apache2/sites-available/
#COPY docker/app/php.ini /usr/local/etc/php/

RUN a2enmod rewrite && service apache2 restart && a2ensite medkey.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -LsS http://codeception.com/codecept.phar -o /usr/local/bin/codecept
RUN chmod a+x /usr/local/bin/codecept

# For Symfony Workflow
RUN apt-get install graphviz -y

## github-token user
RUN composer config -g github-oauth.github.com b3d3fc73aa36d5b4ce65b6d5948c4cce0d8b47f5

WORKDIR /var/www/medkey
#COPY . /var/www/medkey
#COPY .env.prod .env
#RUN composer install

RUN curl -sL https://deb.nodesource.com/setup_11.x -o nodesource_setup.sh
RUN bash nodesource_setup.sh
RUN apt-get install nodejs -y
RUN npm install npm@6.9.0 -g
#WORKDIR /var/www/medkey/frontend
#RUN npm install
#RUN npm run build-prod
#RUN php bin migrate --interactive = 0
#RUN php bin seed Package