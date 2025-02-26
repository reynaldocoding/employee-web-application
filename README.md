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

Finalmente para probar ejecuta:

```bash
cd notification-service
```

```bash
php bin/console mailer:test someone@example.com
```

## Despliegue

Realizar la ejecución de lo siguiente en el mismo orden dentro la terminal:

```bash
git clone https://github.com/reynaldocoding/employee-web-application.git
```

```bash
cd employee-web-application
```

```bash
cd rabbit && docker-compose down --rmi local --volumes --remove-orphans && docker compose up --pull always -d --wait
```

```bash
cd .. && cd notification-service && docker compose build --no-cache
```

```bash
docker-compose down --rmi local --volumes --remove-orphans && docker compose up --pull always -d --wait
```

```bash
cd .. && cd backend && docker compose build --no-cache
```

```bash
docker-compose down --rmi local --volumes --remove-orphans && docker compose up --pull always -d --wait
```

```bash
cd .. && cd frontend-service && docker-compose down --rmi local --volumes --remove-orphans && docker-compose up -d --build
```

## Acceso al Frontend (React)

Finalmente, para interactuar con el Frontend React acceder a: <http://localhost:3000/login>.

## Ejecución de pruebas con PHPUnit

Para ejecutar las pruebas realizar lo siguiente:

```bash
cd backend && php bin/phpunit
```
