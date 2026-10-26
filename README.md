# Prueba Técnica - API de Gestión de Productos

API REST desarrollada con Laravel para gestión de productos con soporte multi-moneda.

## Tabla de Contenidos

- [Descripción](#descripción)
- [Stack Tecnológico](#stack-tecnológico)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Credenciales por Defecto](#credenciales-por-defecto)
- [Endpoints de la API](#endpoints-de-la-api)
- [Parámetros de Consulta](#parámetros-de-consulta)
- [Formato de Respuestas](#formato-de-respuestas)
- [Códigos de Estado](#códigos-de-estado)
- [Validaciones](#validaciones)
- [Comandos Útiles](#comandos-útiles)
- [Tests](#tests)
- [Documentación Interactiva](#documentación-interactiva)
- [Documentación Adicional](#documentación-adicional)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Convenciones de Código](#convenciones-de-código)
- [Seguridad](#seguridad)
- [Características Técnicas](#características-técnicas)
- [Variables de Entorno Principales](#variables-de-entorno-principales)
- [Troubleshooting](#troubleshooting)
- [Entrega](#entrega)
- [Desarrollo](#desarrollo)
- [Licencia](#licencia)

## Descripción

Sistema que permite:
- Crear, leer, actualizar y eliminar productos
- Registrar precios de productos en diferentes divisas
- Mantener historial de precios por producto
- Autenticación basada en tokens

## Stack Tecnológico

- **Backend**: Laravel 13 (PHP 8.3+)
- **Base de datos**: PostgreSQL 18
- **Autenticación**: Laravel Sanctum
- **Servidor web**: Nginx + PHP-FPM
- **Contenedores**: Docker & Docker Compose

## Requisitos

### Con Docker

- Docker Engine 20.10+ - [Guía de instalación oficial](https://docs.docker.com/engine/install/)
- Docker Compose 2.0+
- Make (opcional, para usar comandos del Makefile)

### Sin Docker

- PHP 8.3 o superior
- Composer 2.0+
- PostgreSQL 18
- Extensiones PHP: pdo_pgsql, bcmath, sockets

## Instalación

### Con Docker (Recomendado)

#### Método rápido (usando Make)

```bash
# Clonar el repositorio
git clone <url-del-repositorio>
cd prueba_tecnica

# Ejecutar setup completo
make setup
```

Este comando:
1. Crea el archivo `.env` desde `.env.example`
2. Detecta automáticamente tu UID/GID (compatible con macOS y Linux)
3. Configura interactivamente las credenciales de la base de datos
4. Construye las imágenes Docker
5. Inicia los contenedores
6. Instala dependencias PHP
7. Genera la clave de la aplicación
8. Ejecuta migraciones y seeders

#### Método manual

```bash
# 1. Copiar archivo de configuración
cp .env.example .env

# 2. Crear volumen para PostgreSQL
docker volume create prueba_tecnica_postgres_data

# 3. Configurar credenciales en .env
# DB_PASSWORD es obligatorio

# 4. Actualizar HOST_UID y HOST_GID en .env con tu UID/GID
id -u  # Tu UID
id -g  # Tu GID

# 5. Construir y levantar contenedores
docker compose build
docker compose up -d

# 6. Instalar dependencias
docker compose run --rm php composer install

# 7. Generar clave de aplicación
docker compose exec php php artisan key:generate

# 8. Ejecutar migraciones
docker compose exec php php artisan migrate

# 9. Cargar datos de prueba
docker compose exec php php artisan db:seed
```

### Sin Docker

```bash
# 1. Clonar el repositorio
git clone <url-del-repositorio>
cd prueba_tecnica

# 2. Instalar dependencias PHP
composer install

# 3. Configurar variables de entorno
cp .env.example .env
# Editar .env con tus credenciales de PostgreSQL

# 4. Generar clave de aplicación
php artisan key:generate

# 5. Crear base de datos
psql -U tu_usuario -c "CREATE DATABASE prueba_tecnica;"

# 6. Ejecutar migraciones
php artisan migrate

# 7. Cargar datos de prueba
php artisan db:seed

# 8. Iniciar servidor de desarrollo
php artisan serve
```

## Credenciales por Defecto

El seeder crea un usuario de prueba:
- **Email**: `admin@example.com`
- **Password**: `password`

⚠️ **Importante**: Cambia estas credenciales en producción.

## Endpoints de la API

### Autenticación

**POST /api/login**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

Respuesta:
```json
{
  "error": [],
  "body": {
    "access_token": "1|abc123...",
    "token_type": "bearer",
    "expires_at": "2026-10-26T01:00:00+00:00"
  }
}
```

### Productos (Requiere Autenticación)

**GET /api/products** - Listar productos
```bash
curl -X GET "http://localhost:8000/api/products?per_page=10&page=1" \
  -H "Authorization: Bearer {tu_token}"
```

**POST /api/products** - Crear producto
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {tu_token}" \
  -d '{
    "name": "Producto de ejemplo",
    "description": "Descripción detallada",
    "price": 100.00,
    "currency_id": 1,
    "tax_cost": 16.00,
    "manufacturing_cost": 50.00
  }'
```

**GET /api/products/{id}** - Obtener producto
```bash
curl -X GET http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer {tu_token}"
```

**PUT /api/products/{id}** - Actualizar producto
```bash
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {tu_token}" \
  -d '{
    "name": "Producto actualizado",
    "description": "Descripción actualizada",
    "price": 150.00,
    "currency_id": 1,
    "tax_cost": 20.00,
    "manufacturing_cost": 60.00
  }'
```

**DELETE /api/products/{id}** - Eliminar producto (soft delete)
```bash
curl -X DELETE http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer {tu_token}"
```

### Precios (Requiere Autenticación)

**GET /api/products/{id}/prices** - Listar precios de un producto
```bash
curl -X GET http://localhost:8000/api/products/1/prices \
  -H "Authorization: Bearer {tu_token}"
```

**POST /api/products/{id}/prices** - Crear precio
```bash
curl -X POST http://localhost:8000/api/products/1/prices \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {tu_token}" \
  -d '{
    "price": 120.00,
    "currency_id": 1
  }'
```

### Divisas (Requiere Autenticación)

**GET /api/divisas** - Listar divisas
```bash
curl -X GET "http://localhost:8000/api/divisas?per_page=10&page=1" \
  -H "Authorization: Bearer {tu_token}"
```

**POST /api/divisas** - Crear divisa
```bash
curl -X POST http://localhost:8000/api/divisas \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {tu_token}" \
  -d '{
    "name": "Euro",
    "symbol": "€",
    "exchange_rate": 0.92000000
  }'
```

## Parámetros de Consulta

### Listar productos y precios

- `per_page` (integer): Número de items por página (1-100, default: 10)
- `page` (integer): Número de página (default: 1)
- `sort_by` (string): Campo para ordenar
- `sort_direction` (string): Dirección del ordenamiento (asc, desc)

### Campos disponibles para ordenar productos

- `id`, `name`, `description`, `price`, `currency_id`, `tax_cost`, `manufacturing_cost`, `created_at`, `updated_at`

### Campos disponibles para ordenar precios

- `id`, `product_id`, `currency_id`, `price`, `created_at`, `updated_at`

### Campos disponibles para ordenar divisas

- `id`, `name`, `symbol`, `exchange_rate`, `created_at`, `updated_at`

## Formato de Respuestas

**Éxito**:
```json
{
  "error": [],
  "body": {
    "data": "..."
  }
}
```

**Error de validación**:
```json
{
  "error": {
    "campo": ["Mensaje de error"]
  },
  "body": []
}
```

**Error de autenticación**:
```json
{
  "error": ["Unauthenticated"],
  "body": []
}
```

## Códigos de Estado

- `200 OK`: Operación exitosa
- `201 Created`: Recurso creado exitosamente
- `400 Bad Request`: Datos de validación inválidos
- `401 Unauthorized`: Token inválido o ausente
- `404 Not Found`: Recurso no encontrado
- `500 Internal Server Error`: Error interno del servidor

## Validaciones

### Productos

- `name`: requerido, string, máximo 255 caracteres
- `description`: requerido, string
- `price`: requerido, decimal (0 a 9999999999999.99), máximo 2 decimales
- `currency_id`: requerido, integer, debe existir en la tabla divisas
- `tax_cost`: requerido, decimal (0 a 9999999999999.99), máximo 2 decimales
- `manufacturing_cost`: requerido, decimal (0 a 9999999999999.99), máximo 2 decimales

### Precios

- `price`: requerido, decimal (0 a 9999999999999.99), máximo 2 decimales
- `currency_id`: requerido, integer, debe existir en la tabla divisas

### Divisas

- `name`: requerido, string, máximo 255 caracteres
- `symbol`: requerido, string, máximo 255 caracteres
- `exchange_rate`: requerido, decimal (0 a 99999999.99999999), máximo 8 decimales

## Comandos Útiles

```bash
make up          # Levantar contenedores
make down        # Detener contenedores
make test        # Ejecutar tests
make migrate     # Ejecutar migraciones
make rollback    # Revertir última migración
make seed        # Cargar datos de prueba
make install     # Instalar dependencias Composer
```

## Tests

El proyecto incluye tests automatizados que validan:
- Autenticación y autorización
- CRUD de productos
- Gestión de precios
- Validaciones de entrada
- Paginación y ordenamiento
- Manejo de errores

### Ejecutar tests

```bash
# Con Docker
make test

# Sin Docker
./vendor/bin/phpunit
```

**Estadísticas**: 36 tests, 268 aserciones, 100% passing

## Documentación Interactiva

La API incluye documentación interactiva con Swagger UI:

1. Inicia la aplicación: `make up`
2. Abre en tu navegador: `http://localhost:8000/docs/swagger-ui.html`

## Documentación Adicional

- **OpenAPI Specification**: `public/docs/openapi.yaml`
- **Postman Collection**: `docs/postman_collection.json`
- **Insomnia Collection**: `docs/insomnia.json`
- **Documentación completa**: `docs/API.md`

## Estructura del Proyecto

```
.
├── app/
│   ├── Actions/                        # Lógica de negocio reutilizable
│   │   ├── Divisa/
│   │   │   ├── CrearDivisa.php
│   │   │   └── ObtenerDivisas.php
│   │   ├── PrecioProducto/
│   │   │   ├── CrearPrecio.php
│   │   │   └── ObtenerPrecios.php
│   │   ├── Producto/
│   │   │   ├── ActualizarProducto.php
│   │   │   ├── CrearProducto.php
│   │   │   ├── EliminarProducto.php
│   │   │   ├── ObtenerProducto.php
│   │   │   └── ObtenerProductos.php
│   │   └── Usuario/
│   │       └── LoginUsuario.php
│   ├── Exceptions/                     # Excepciones de dominio
│   │   ├── CredencialesInvalidasException.php
│   │   └── ProductoNoEncontradoException.php
│   ├── Http/
│   │   └── Controllers/                # Controladores invokables
│   │       ├── Autenticacion/
│   │       │   └── LoginPostController.php
│   │       ├── Producto/
│   │       │   ├── ProductosGetController.php
│   │       │   ├── ProductoGetController.php
│   │       │   ├── ProductoPostController.php
│   │       │   ├── ProductoPutController.php
│   │       │   └── ProductoDeleteController.php
│   │       ├── PrecioProducto/
│   │       │   ├── PrecioProductosGetController.php
│   │       │   └── PrecioProductoPostController.php
│   │       └── Divisa/
│   │           ├── DivisasGetController.php
│   │           └── DivisaPostController.php
│   ├── Models/
│   │   ├── Usuario.php                 # Modelo de usuario
│   │   ├── Producto.php                # Modelo de producto
│   │   ├── PrecioProducto.php          # Modelo de precio
│   │   └── Divisa.php                  # Modelo de moneda
│   └── Services/                       # Servicios de dominio
│       └── Producto/
│           └── BuscarProducto.php
├── database/
│   ├── migrations/                     # Migraciones de base de datos
│   ├── seeders/                        # Datos de prueba
│   └── factories/                      # Factories para testing
├── docker/
│   ├── php/Dockerfile                  # Configuración PHP-FPM
│   ├── nginx/default.conf              # Configuración Nginx
│   └── postgres/initdb/                # Scripts de inicialización
├── docs/
│   ├── postman_collection.json         # Colección Postman
│   ├── insomnia.json                   # Colección Insomnia
│   └── API.md                          # Documentación completa
├── public/
│   └── docs/
│       ├── openapi.yaml                # Especificación OpenAPI
│       ├── swagger-ui.html             # UI de Swagger
│       ├── swagger-ui.css              # Estilos Swagger UI
│       └── swagger-ui-bundle.js        # JavaScript Swagger UI
├── routes/
│   └── api.php                         # Rutas de la API
└── tests/                              # Tests automatizados
```

## Convenciones de Código

### Idioma

- **Modelos, tablas, variables, funciones, clases, comentarios**: Español
- **Respuestas de API**: Inglés (estándar REST)
- **Nombres de controladores**: Inglés con patrón `RecursoMetodoController` (convención de controladores invokables)

### Arquitectura

Los controladores son **invokables** (usando `__invoke`) y siguen el patrón:
- **Un controlador = Un endpoint**: Cada acción tiene su propio controlador
- **Nombres descriptivos**: `[Recurso][MetodoHTTP]Controller` (ejemplo: `ProductoGetController`, `ProductoPostController`)
- **Validación en el controlador**: Los controladores validan directamente con `$request->validate()` y manejan todas las respuestas posibles (200, 201, 400, 401, 404)
- **Acciones de negocio**: La lógica está en `app/Actions/` y es reutilizable desde cualquier contexto (controladores, comandos, jobs)

### Ejemplos

```php
// Controlador invokable con inyección por constructor
class ProductoPostController
{
    public function __construct(private CrearProducto $action) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'price' => ['required', 'decimal:0,2', 'min:0'],
                'currency_id' => ['required', 'exists:divisas,id'],
                'tax_cost' => ['required', 'decimal:0,2', 'min:0'],
                'manufacturing_cost' => ['required', 'decimal:0,2', 'min:0'],
            ]);

            $producto = ($this->action)($validated);

            return response()->json([
                'error' => [],
                'body' => ['product' => $producto],
            ], JsonResponse::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->errors(),
                'body' => [],
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}

// Acción de negocio (reutilizable)
class CrearProducto
{
    public function __invoke(array $datos): Producto
    {
        return Producto::create($datos);
    }
}

// Servicio para buscar productos (lanza excepción si no existe)
class BuscarProducto
{
    public function __invoke(int $id): Producto
    {
        $producto = Producto::find($id);
        if (!$producto) {
            throw new ProductoNoEncontradoException();
        }
        return $producto;
    }
}
```

### Modelo en español

```php
class PrecioProducto extends Model { }
```

### Respuesta API en inglés

```json
{
  "data": {
    "price": 100.00,
    "currency_id": 1
  }
}
```

## Seguridad

- Tokens Sanctum con expiración configurable (default: 60 minutos)
- Validación estricta de todas las entradas
- Sin campos nullable en la API
- Límites en tamaños de datos
- Validación de decimales (máximo 2 decimales)
- Límite máximo en valores numéricos
- Puertos Docker restringidos a localhost
- Contraseñas hasheadas con bcrypt
- Rutas protegidas con middleware de autenticación
- Manejo global de excepciones
- Respuestas JSON consistentes para errores

## Características Técnicas

- **Soft Deletes**: Los productos se eliminan de forma lógica (recuperables)
- **Paginación**: Soporte para paginación en endpoints de listado
- **Ordenamiento**: Ordenamiento personalizado por múltiples campos
- **Validación robusta**: Rechazo de valores inválidos con mensajes claros
- **Historial de precios**: Múltiples precios por producto en diferentes divisas
- **Respuestas consistentes**: Formato uniforme `{error, body}` en todas las respuestas

## Variables de Entorno Principales

| Variable | Descripción | Valor por defecto |
|----------|-------------|-------------------|
| `APP_KEY` | Clave de aplicación Laravel | Generada automáticamente |
| `DB_CONNECTION` | Driver de base de datos | `pgsql` |
| `DB_HOST` | Host de PostgreSQL | `postgres` (Docker) / `127.0.0.1` (local) |
| `DB_PORT` | Puerto de PostgreSQL | `5432` |
| `DB_DATABASE` | Nombre de la base de datos | `prueba_tecnica` |
| `DB_USERNAME` | Usuario de PostgreSQL | `prueba_tecnica_user` |
| `DB_PASSWORD` | Contraseña de PostgreSQL | (obligatorio) |
| `SANCTUM_TOKEN_EXPIRATION` | Expiración de tokens (minutos) | `60` |

## Troubleshooting

### Error: "no such table: productos.deleted_at"

Si encuentras este error, ejecuta las migraciones pendientes:

```bash
make migrate
```

### Error: "Permission denied" en Docker

Asegúrate de que los valores de `HOST_UID` y `HOST_GID` en `.env` coincidan con tu usuario:

```bash
id -u  # Obtén tu UID
id -g  # Obtén tu GID
```

### Error: "DB_PASSWORD no puede estar vacío"

El setup requiere una contraseña para la base de datos. Proporciona una contraseña válida durante el setup interactivo.

## Entrega

La prueba incluye los siguientes entregables requeridos:

✅ **Swagger/OpenAPI**: `public/docs/openapi.yaml` con UI interactiva en `http://localhost:8000/docs/swagger-ui.html`
✅ **Postman**: `docs/postman_collection.json`
✅ **Insomnia**: `docs/insomnia.json`
✅ **Documento**: `docs/API.md` con documentación completa

## Desarrollo

Esta API fue desarrollada siguiendo las mejores prácticas de Laravel:

- Uso de Eloquent ORM para interacción con base de datos
- Migraciones versionadas para esquema de base de datos
- Factories y seeders para datos de prueba
- Tests automatizados con PHPUnit
- Controladores RESTful con validación robusta
- Autenticación stateless con tokens
- Manejo centralizado de excepciones
- Validación de entrada estricta
- Respuestas JSON consistentes

## Licencia

Este proyecto fue desarrollado como prueba técnica.
