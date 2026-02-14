FROM dockerhub.timeweb.cloud/library/node:25-alpine

ENV NODE_ENV=development
WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm install

COPY . .

EXPOSE 5173
CMD [ "npm", "run", "dev", "--", "--host" ]
