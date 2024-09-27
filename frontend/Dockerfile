FROM node:18-alpine

WORKDIR /app

COPY package*.json ./

RUN npm install -g @vitejs/plugin-vue@^5.0.5
RUN npm install -g axios@^1.7.2
RUN npm install -g laravel-vite-plugin@^1.0
RUN npm install -g vite@^5.0
RUN npm install -g sass@^1.77.8

RUN npm install

COPY . .

RUN npm run build

CMD ["npm", "run", "build"]
