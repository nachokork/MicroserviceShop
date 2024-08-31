# Dockerfile
FROM node:14

# Configuración del directorio de trabajo
WORKDIR /usr/src/app

# Copiar y instalar las dependencias de la aplicación
COPY package*.json ./
COPY . .

# Exponer el puerto 3000
EXPOSE 3000

# Comando para iniciar la aplicación React
CMD ["npm", "start"]