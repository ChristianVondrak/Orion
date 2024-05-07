FROM webdevops/php-nginx:8.3 as ORION

# Instala Node.js y npm
RUN apt-get update && apt-get install -y \
    curl \
    gnupg \
    && curl -sL https://deb.nodesource.com/setup_14.x | bash - \
    && apt-get install -y nodejs \
    && apt-get install -y npm

EXPOSE 80
EXPOSE 5173
EXPOSE 8000
