<p align="center" style="display: flex; justify-content:center; gap:15px"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a>
<a href="https://https://mongodb.com/" target="_blank"><img src="https://images.contentstack.io/v3/assets/blt7151619cb9560896/blta30d0168850404a8/65fda6758f44440029c3a12a/la1a1agcxt7ppntea-logo-marks.svg" width="300" alt="Mongodb Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
</p>

## Pasos de instalación - WINDOWS 🪟
1. **Descargar e instalar pecl php extension mongodb:**
  - [Selecciona la version estable más reciente](https://pecl.php.net/package/mongodb)
  - [Descarga segun tu version de php](https://pecl.php.net/package/mongodb/1.18.0/windows)
2. **Copiar y pegar el archivo php_mongodb.dll a las extensiones de tu version de php**
  - posible ruta: \bin\php\php-8.3.11-nts-Win32-vs16-x64\ext

3. **Agregar la extension de mongodb en php.ini:**
  - extension=mongodb

4. **Descargar e instalar el administrador de colecciones de mongodb**
  - [Selecciona la version y el tipo de paquete](https://www.mongodb.com/try/download/community)

## Endpoints
- **BASE_URL** = ""
### Autenticación - Login 
- **Descripción:** Api para iniciar sesión
- **Método:** POST
- **URL:** `BASE_URL/api/login`
- **Status** Ready 💪😎
- **Body (JSON) REQUEST:**
    ```json
    {
      "email":"waaaa@gmail.com",
      "password":"123456"
    }
    ```

- **Body (JSON) RESPONSE:**
    
    ```json
    {
      "status": true,
      "message": "Usuario autenticado correctamente.",
      "token": "66d8a567ee88dd0f0300dd12|4KlWOWFRpL7iNivjvjAk6A9KwLmzz3iNT1538hdn1dd82aba"
    }
    ```

### Autenticación - Register 
- **Descripción:** Api para registro de usuario
- **Método:** POST
- **URL:** `BASE_URL/api/register`
- **Status** Ready 💪😎
- **Body (JSON) REQUEST:**
  ```json
  {
    "email":"waaaa@gmail.com",
    "password":"123456",
    "role":"paisi",
    "name":"waaaasaberto Maximo"
  }
  ```

- **Body (JSON) RESPONSE:**
  
  ```json
  {
    "status": true,
    "message": "Usuario registrado correctamente",
    "user": {
      "name": "waaaasaberto Maximo",
      "email": "waaaa@gmail.com",
      "role": "paisi"
    }
  }
  ```

### Autenticación - Me 
- **Descripción:** Api para obtener datos de usuario logueado
- **Método:** GET
- **URL:** `BASE_URL/api/me`
- **Status** Ready 💪😎
- **Authorization:**
  Bearer token
- **Body (JSON) RESPONSE:**
  ```json
  {
    "status": true,
    "user": {
      "name": "waaaasaberto Maximo",
      "email": "waaaa@gmail.com",
      "role": "paisi"
    }
  }
  ```

### Autenticación - Logout 
- **Descripción:** Api para cerrar sesión
- **Método:** POST
- **URL:** `BASE_URL/api/logout`
- **Status** Ready 💪😎
- **Authorization:**
  Bearer token
- **Body (JSON) RESPONSE:**
  ```json
  {
    "status": true,
    "message": "Cierre de sesión exitoso"
  }
  ```

### Usuarios - Lista de usuarios
- **Descripción:** Api para obtener usuarios registrados
- **Método:** GET
- **URL:** `BASE_URL/api/users`
- **Status** Ready 💪😎 
- **Body (JSON) RESPONSE:**
  ```json
  [
    {
      "_id": "66d930c974dc5f34c60e4264",
      "name": "Jonathan",
      "email": "jonathan123@gmail.com",
      "role": "admin",
      "updated_at": "2024-09-05T04:17:13.749000Z",
      "created_at": "2024-09-05T04:17:13.749000Z"
    },
    {
      "_id": "66d930ca74dc5f34c60e4265",
      "name": "Estudiante",
      "email": "estudiante@gmail.com",
      "role": "estudiante",
      "updated_at": "2024-09-05T04:17:14.297000Z",
      "created_at": "2024-09-05T04:17:14.297000Z"
    },
    {
      "_id": "66d930ca74dc5f34c60e4266",
      "name": "PAISI",
      "email": "paisi@gmail.com",
      "role": "paisi",
      "updated_at": "2024-09-05T04:17:14.843000Z",
      "created_at": "2024-09-05T04:17:14.843000Z"
    },
    {
      "_id": "66d930cb74dc5f34c60e4267",
      "name": "Coordinador",
      "email": "coordinador@gmail.com",
      "role": "coordinador",
      "updated_at": "2024-09-05T04:17:15.392000Z",
      "created_at": "2024-09-05T04:17:15.392000Z"
    },...
  ]
  ```

### Usuarios - Crear usuarios
- **Descripción:** Api para registrar un usuario
- **Método:** POST
- **URL:** `BASE_URL/api/users`
- **Status** Pending 🥹🥹
- **Body (JSON) REQUEST:**
  ```json
  {
    null
  }
  ```
- **Body (JSON) RESPONSE:**
  ```json
  {
    null
  }
  ```