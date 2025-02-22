# Employee Web Application

## Crear la Red Externa

Antes de ejecutar, se debe crear una red Docker. Ejecutar el siguiente comando:

```bash
docker network create employee-network
```

## Despliegue

Ejemplo:

```bash
cd backend && docker-compose down --rmi local --volumes --remove-orphans && docker compose up --pull always -d --wait && cd ..
```
