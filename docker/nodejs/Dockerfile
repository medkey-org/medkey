FROM node:11.14.0-alpine
COPY . /var/www/medkey
WORKDIR /var/www/medkey/frontend
RUN npm install
RUN npm run build-dev