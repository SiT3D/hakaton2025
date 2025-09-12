FROM node:22-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install
RUN npm install vue-router@4

COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]
