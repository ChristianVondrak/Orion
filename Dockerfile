FROM webdevops/php-nginx:8.2 as ORION

# # Instala Node.js y npm
# RUN apt-get update && apt-get install -y \
#     curl \
#     gnupg \
#     && curl -sL https://deb.nodesource.com/setup_14.x | bash - \
#     && apt-get install -y nodejs \
#     && apt-get install -y npm

RUN apt-get update && apt-get install -y
# Instala Node.js y npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

EXPOSE 80
EXPOSE 5173
EXPOSE 8000
