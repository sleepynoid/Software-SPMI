# Use an official Node runtime as a parent image
FROM php:8

# Set the working directory
WORKDIR /usr/src/app

# Copy the package.json and package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the application
COPY . .

# Build the Nuxt.js application
RUN npm run build

# Expose the port the app runs on
EXPOSE 3000

# Start the Nuxt.js application
CMD [ "npm", "start" ]
