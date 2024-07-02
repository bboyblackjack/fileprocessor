FROM php:7.1-apache

RUN apt-get update \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get install -y git

# DEPENDENCIES
RUN apt-get update --fix-missing \
    && apt-get install -y python3 \
    && rm -rf /var/lib/apt/lists/* \
    && ln -s /usr/bin/python3 /usr/bin/python

RUN git clone --depth 1 --branch v0.11.1 https://github.com/edenhill/librdkafka.git \
    && ( \
        cd librdkafka \
        && ./configure \
        && make \
        && make install \
    ) \
    && pecl install rdkafka-5.0.2 \
    && echo "extension=rdkafka.so" > /usr/local/etc/php/conf.d/rdkafka.ini

COPY ./ /var/www/html

WORKDIR .

RUN useradd -ms /bin/bash yii

USER yii