# Documentación de la API

## Descripción General

API REST desarrollada con Laravel para gestión de productos con soporte multi-moneda. Permite crear, leer, actualizar y eliminar productos, así como registrar precios en diferentes divisas.

## Características

- **Autenticación**: Basada en tokens con Laravel Sanctum
- **Formato de respuesta**: JSON
- **Paginación**: Soporte para paginación en endpoints de listado
- **Ordenamiento**: Soporte para ordenamiento personalizado
- **Validación**: Validación robusta de datos de entrada
- **Soft Deletes**: Los productos se eliminan de forma lógica (recuperables)

## Base URL

```
http://localhost:8000/api
```

## Autenticación

Todos los endpoints de productos y precios requieren autenticación mediante token Bearer.

### Obtener Token

**Endpoint**: `POST /login`

**Request**:
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response** (200 OK):
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

**Uso del token**:
```
Authorization: Bearer {tu_token}
```

**Credenciales de prueba**:
- Email: `admin@example.com`
- Password: `password`

## Endpoints

### 1. Listar Productos

**Endpoint**: `GET /products`

**Autenticación**: Requerida

**Parámetros de consulta**:
- `per_page` (integer, opcional): Número de productos por página (1-100, default: 10)
- `page` (integer, opcional): Número de página (default: 1)
- `sort_by` (string, opcional): Campo para ordenar (id, name, description, price, currency_id, tax_cost, manufacturing_cost, created_at, updated_at)
- `sort_direction` (string, opcional): Dirección del ordenamiento (asc, desc)

**Response** (200 OK):
```json
{
  "error": [],
  "body": {
    "products": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "name": "Producto de ejemplo",
          "description": "Descripción detallada",
          "price": "100.00",
          "currency_id": 1,
          "tax_cost": "16.00",
          "manufacturing_cost": "50.00",
          "created_at": "2026-10-26T00:00:00.000000Z",
          "updated_at": "2026-10-26T00:00:00.000000Z",
          "deleted_at": null
        }
      ],
      "per_page": 10,
      "total": 1,
      "last_page": 1
    }
  }
}
```

### 2. Crear Producto

**Endpoint**: `POST /products`

**Autenticación**: Requerida

**Request**:
```json
{
  "name": "Producto de ejemplo",
  "description": "Descripción detallada del producto",
  "price": 100.00,
  "currency_id": 1,
  "tax_cost": 16.00,
  "manufacturing_cost": 50.00
}
```

**Validaciones**:
- `name`: requerido, string, máximo 255 caracteres
- `description`: requerido, string
- `price`: requerido, decimal (0 a 9999999999999.99), máximo 2 decimales
- `currency_id`: requerido, integer, debe existir en la tabla divisas
- `tax_cost`: requerido, decimal (0 a 9999999999999.99), máximo 2 decimales
- `manufacturing_cost`: requerido, decimal (0 a 9999999999999.99), máximo 2 decimales

**Response** (201 Created):
```json
{
  "error": [],
  "body": {
    "product": {
      "id": 1,
      "name": "Producto de ejemplo",
      "description": "Descripción detallada del producto",
      "price": "100.00",
      "currency_id": 1,
      "tax_cost": "16.00",
      "manufacturing_cost": "50.00",
      "created_at": "2026-10-26T00:00:00.000000Z",
      "updated_at": "2026-10-26T00:00:00.000000Z",
      "deleted_at": null
    }
  }
}
```

### 3. Obtener Producto

**Endpoint**: `GET /products/{id}`

**Autenticación**: Requerida

**Parámetros**:
- `id` (integer, requerido): ID del producto

**Response** (200 OK):
```json
{
  "error": [],
  "body": {
    "product": {
      "id": 1,
      "name": "Producto de ejemplo",
      "description": "Descripción detallada",
      "price": "100.00",
      "currency_id": 1,
      "tax_cost": "16.00",
      "manufacturing_cost": "50.00",
      "created_at": "2026-10-26T00:00:00.000000Z",
      "updated_at": "2026-10-26T00:00:00.000000Z",
      "deleted_at": null
    }
  }
}
```

**Response** (404 Not Found):
```json
{
  "error": ["Product not found"],
  "body": []
}
```

### 4. Actualizar Producto

**Endpoint**: `PUT /products/{id}`

**Autenticación**: Requerida

**Parámetros**:
- `id` (integer, requerido): ID del producto

**Request** (todos los campos son requeridos):
```json
{
  "name": "Producto actualizado",
  "description": "Descripción actualizada",
  "price": 150.00,
  "currency_id": 1,
  "tax_cost": 20.00,
  "manufacturing_cost": 60.00
}
```

**Response** (200 OK):
```json
{
  "error": [],
  "body": {
    "product": {
      "id": 1,
      "name": "Producto actualizado",
      "description": "Descripción actualizada",
      "price": "150.00",
      "currency_id": 1,
      "tax_cost": "20.00",
      "manufacturing_cost": "60.00",
      "created_at": "2026-10-26T00:00:00.000000Z",
      "updated_at": "2026-10-26T00:05:00.000000Z",
      "deleted_at": null
    }
  }
}
```

### 5. Eliminar Producto

**Endpoint**: `DELETE /products/{id}`

**Autenticación**: Requerida

**Parámetros**:
- `id` (integer, requerido): ID del producto

**Response** (200 OK):
```json
{
  "error": [],
  "body": []
}
```

**Nota**: El producto se elimina de forma lógica (soft delete). El registro permanece en la base de datos con un timestamp en `deleted_at`.

### 6. Listar Precios de un Producto

**Endpoint**: `GET /products/{id}/prices`

**Autenticación**: Requerida

**Parámetros**:
- `id` (integer, requerido): ID del producto
- `per_page` (integer, opcional): Número de precios por página (1-100, default: 10)
- `page` (integer, opcional): Número de página (default: 1)
- `sort_by` (string, opcional): Campo para ordenar (id, product_id, currency_id, price, created_at, updated_at)
- `sort_direction` (string, opcional): Dirección del ordenamiento (asc, desc)

**Response** (200 OK):
```json
{
  "error": [],
  "body": {
    "prices": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "product_id": 1,
          "currency_id": 1,
          "price": "120.00",
          "created_at": "2026-10-26T00:00:00.000000Z",
          "updated_at": "2026-10-26T00:00:00.000000Z",
          "divisa": {
            "id": 1,
            "name": "USD",
            "symbol": "$",
            "exchange_rate": "1.00000000",
            "created_at": "2026-10-26T00:00:00.000000Z",
            "updated_at": "2026-10-26T00:00:00.000000Z"
          }
        }
      ],
      "per_page": 10,
      "total": 1,
      "last_page": 1
    }
  }
}
```

### 7. Crear Precio

**Endpoint**: `POST /products/{id}/prices`

**Autenticación**: Requerida

**Parámetros**:
- `id` (integer, requerido): ID del producto

**Request**:
```json
{
  "price": 120.00,
  "currency_id": 1
}
```

**Validaciones**:
- `price`: requerido, decimal (0 a 9999999999999.99), máximo 2 decimales
- `currency_id`: requerido, integer, debe existir en la tabla divisas

**Response** (201 Created):
```json
{
  "error": [],
  "body": {
    "price": {
      "id": 1,
      "product_id": 1,
      "currency_id": 1,
      "price": "120.00",
      "created_at": "2026-10-26T00:00:00.000000Z",
      "updated_at": "2026-10-26T00:00:00.000000Z",
      "divisa": {
        "id": 1,
        "name": "USD",
        "symbol": "$",
        "exchange_rate": "1.00000000",
        "created_at": "2026-10-26T00:00:00.000000Z",
        "updated_at": "2026-10-26T00:00:00.000000Z"
      }
    }
  }
}
```

## Códigos de Estado

- `200 OK`: Operación exitosa
- `201 Created`: Recurso creado exitosamente
- `400 Bad Request`: Datos de validación inválidos
- `401 Unauthorized`: Token de autenticación inválido o ausente
- `404 Not Found`: Recurso no encontrado
- `405 Method Not Allowed`: Método HTTP no permitido
- `500 Internal Server Error`: Error interno del servidor

## Formato de Error

**Error de validación** (400):
```json
{
  "error": {
    "name": ["The name field is required."],
    "price": ["The price field must be at least 0."]
  },
  "body": []
}
```

**Error de autenticación** (401):
```json
{
  "error": ["Unauthenticated"],
  "body": []
}
```

**Error de recurso no encontrado** (404):
```json
{
  "error": ["Product not found"],
  "body": []
}
```

## Modelo de Datos

### Tabla: productos
- `id` (integer): Identificador único
- `name` (string): Nombre del producto
- `description` (string): Descripción del producto
- `price` (decimal): Precio del producto en la divisa base
- `currency_id` (integer): ID de la divisa base
- `tax_cost` (decimal): Costo de impuestos
- `manufacturing_cost` (decimal): Costo de fabricación
- `created_at` (timestamp): Fecha de creación
- `updated_at` (timestamp): Fecha de actualización
- `deleted_at` (timestamp, nullable): Fecha de eliminación (soft delete)

### Tabla: divisas
- `id` (integer): Identificador único
- `name` (string): Nombre de la divisa
- `symbol` (string): Símbolo de la divisa
- `exchange_rate` (decimal): Tasa de cambio
- `created_at` (timestamp): Fecha de creación
- `updated_at` (timestamp): Fecha de actualización

### Tabla: precios_productos
- `id` (integer): Identificador único
- `product_id` (integer): ID del producto
- `currency_id` (integer): ID de la divisa
- `price` (decimal): Precio en la divisa especificada
- `created_at` (timestamp): Fecha de creación
- `updated_at` (timestamp): Fecha de actualización

## Ejemplos de Uso

### cURL

**Login**:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

**Listar productos**:
```bash
curl -X GET "http://localhost:8000/api/products?per_page=10" \
  -H "Authorization: Bearer {tu_token}" \
  -H "Accept: application/json"
```

**Crear producto**:
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {tu_token}" \
  -d '{
    "name": "Nuevo producto",
    "description": "Descripción",
    "price": 100.00,
    "currency_id": 1,
    "tax_cost": 16.00,
    "manufacturing_cost": 50.00
  }'
```

### JavaScript (Fetch)

```javascript
// Login
const loginResponse = await fetch('http://localhost:8000/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    email: 'admin@example.com',
    password: 'password'
  })
});

const { body } = await loginResponse.json();
const token = body.access_token;

// Listar productos
const productsResponse = await fetch('http://localhost:8000/api/products', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});

const products = await productsResponse.json();
```

### Python

```python
import requests

# Login
response = requests.post(
    'http://localhost:8000/api/login',
    json={
        'email': 'admin@example.com',
        'password': 'password'
    },
    headers={'Accept': 'application/json'}
)

token = response.json()['body']['access_token']

# Listar productos
response = requests.get(
    'http://localhost:8000/api/products',
    headers={
        'Authorization': f'Bearer {token}',
        'Accept': 'application/json'
    }
)

products = response.json()
```

## Documentación Interactiva

La API incluye documentación interactiva con Swagger UI:

1. Inicia la aplicación: `make up`
2. Abre en tu navegador: `http://localhost:8000/docs/swagger-ui.html`

## Recursos Adicionales

- **OpenAPI Spec**: `public/docs/openapi.yaml`
- **Postman Collection**: `docs/postman_collection.json`
- **Insomnia Collection**: `docs/insomnia.json`

## Soporte

Para reportar problemas o sugerencias, abre un issue en el repositorio del proyecto.
