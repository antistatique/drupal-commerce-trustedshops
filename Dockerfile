ARG BASE_IMAGE_TAG=8.9
FROM wengerk/drupal-for-contrib:${BASE_IMAGE_TAG}

# Drupal Commerce 2.20+ requires bcmath extensions.
RUN docker-php-ext-install bcmath

ENV COMPOSER_MEMORY_LIMIT=-1

# Install Drupal Commerce as required by the module
RUN composer require drupal/commerce:^2.20

# Install the TrustedShops PHP SDK as required by the module
RUN composer require antistatique/trustedshops-php-sdk:^1.0.0

# Override the default template for PHPUnit testing.
COPY phpunit.xml /opt/drupal/web/phpunit.xml
