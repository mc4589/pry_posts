# Grupo 2

**Integrantes del equipo:**

- Jonathan Hernández 
- Marco Chacón 
- Carlos Fernández 
- Sandy Mariño
- Sergio Condo 
- Carlos Cantuña 


# Microservicio de Publicaciones (Posts) – Laravel 12 + PostgreSQL

**Proyecto:** `PRY_POST_MICROSERVICIO`  
**Base de datos:** PostgreSQL  
**Puerto:** `8001`  


## Objetivo del microservicio
Gestionar un CRUD completo de publicaciones (posts) con las siguientes reglas:
- Solo usuarios autenticados pueden acceder
- El token se valida contra el microservicio de autenticación
- Solo el dueño del post (o un administrador) puede editar/eliminar

## Características implementadas

| Funcionalidad                  | Endpoint                  | Método | Protección |
|-------------------------------|---------------------------|--------|------------|
| Listar todos los posts        | `/api/posts`              | GET    | Token válido |
| Crear post                    | `/api/posts`              | POST   | Token válido |
| Ver post por ID               | `/api/posts/{id}`         | GET    | Token válido |
| Actualizar post               | `/api/posts/{id}`         | PUT/PATCH | Token + dueño o admin |
| Eliminar post                 | `/api/posts/{id}`         | DELETE | Token + dueño o admin |

## Comunicación entre microservicios
- Middleware personalizado: `CheckAuthToken`
- Valida el Bearer Token contra:  
  `http://192.168.56.1:8000/api/validate-token`
- Guarda el usuario autenticado en `$request->attributes->get('auth_user')`
- Autorización: solo el propietario o perfil `administrador` puede modificar/eliminar

## Modelo Post
```php
protected $fillable = ['title', 'content', 'user_id'];
public function user() { return $this->belongsTo(User::class); }


## Estructura Clave
app/Models/Post.php
app/Http/Controllers/Api/PostController.php  ← CRUD completo
app/Http/Middleware/CheckAuthToken.php       ← validación remota
bootstrap/app.php                            ← registro de middleware auth.micro
routes/api.php                               ← todas las rutas protegidas con auth.micro
.env → AUTH_SERVICE_URL=http://(IP de la red):8000

## Ejemplo de creación con POST
{
  "title": "Paisajes de mi lindo Ecuador",
  "content": "Entre las maravillas naturales del Ecuador constan el parque nacional Machalilla, Laguna de Cuyabeno, El volcán Chimborazo"
}

Mensaje mostrado:
{
  "id": 5,
  "title": "Paisajes de mi lindo Ecuador",
  "content": "...",
  "user_id": 2,
  "user": {
    "id": 1,
    "nombre": "Plivio Torres",
    "perfil": "administrador"
  }
}

## Arquitectura del microservicio:
[Cliente / Postman]
       ↓ (Bearer Token)
[Microservicio Posts] → valida token → [Microservicio Autenticación]
       ↑                          ← devuelve user + perfil
       ↓
[PostgreSQL] ← guarda posts con user_id
