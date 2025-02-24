# Employee Web Application

## Crear la Red Externa

Antes de ejecutar, se debe crear una red Docker. Ejecutar el siguiente comando:

```bash
docker network create employee-network
```

## Actualizar `MAILER_DSN` en `notification-service/.env`

Para verificar que los correos lleguen correctamente, actualiza la variable `MAILER_DSN` en el archivo `notification-service/.env`.

> NOTA: El valor de `MAILER_DSN` proporcionado por defecto es un SMTP falso y sólo funciona con un dominio local, es decir no funcionará con otros dominios, por lo que es necesario que lo actualices con los datos de tu propio servidor SMTP.

**Actualizar con los datos de tu servidor de correo**, por ejemplo:

```env
MAILER_DSN=smtp://user:password@smtp.server.com:587
```

## Despliegue

Ejemplo:

```bash
cd notification-service && docker-compose down --rmi local --volumes --remove-orphans && docker compose up -d --wait && cd .. && cd backend && docker-compose down --rmi local --volumes --remove-orphans && docker compose up --pull always -d --wait && cd ..
```

## Ejecución de pruebas con PHPUnit

Ejemplo:

```bash
cd backend && php bin/phpunit
```
