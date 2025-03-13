# Use an official Node.js image from Docker hub

FROM node:22

# Set up the working directory in the container
WORKDIR /var/www/html

# Copy package.json from src folder in host to working directory in container
COPY src/package*.json ./

# Install dependencies
RUN npm install

# Install dependencies using yarn
RUN yarn install

# Copy application source code from src folder
COPY src/ .

# Expose the port (if needed)
EXPOSE 3003

# Run the application
CMD ["yarn", "dev"]

