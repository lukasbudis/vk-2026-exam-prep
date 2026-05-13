# === Development Stage ===
FROM node:22-alpine AS development

USER node

WORKDIR /app

# ARG pre nastavenie prostredia
ARG NODE_ENV=development
ENV NODE_ENV=$NODE_ENV

COPY --chown=node:node ./package*.json ./
RUN npm install

COPY --chown=node:node . .

EXPOSE 8080

CMD ["npm", "run", "dev"]

# === Build Stage ===
FROM node:22-alpine AS build

USER node

WORKDIR /app

COPY --from=development /app ./

RUN npm run build

# === Production Stage ===
FROM nginx:alpine AS production

WORKDIR /usr/share/nginx/html

COPY --from=build /app/dist .

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
