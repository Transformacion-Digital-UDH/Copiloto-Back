<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"><img src="https://images.contentstack.io/v3/assets/blt7151619cb9560896/blta30d0168850404a8/65fda6758f44440029c3a12a/la1a1agcxt7ppntea-logo-marks.svg"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
</p>

## Pasos de instalación - WINDOWS 🪟
1. **Descargar e instalar pecl php extension mongodb:**
  - [Selecciona la version estable más reciente](https://pecl.php.net/package/mongodb)
  - [Descarga segun tu version de php](https://pecl.php.net/package/mongodb/1.18.0/windows)
  - Copiar y pegar el archivo php_mongodb.dll a las extensiones de tu version de php (ruta:\bin\php\php-8.3.11-nts-Win32-vs16-x64\ext)

2. **Agregar la extension de mongodb en php.ini:**
  - extension=mongodb

3. **Descargar e instalar el administrador de colecciones de mongodb**
  - [Selecciona la version y el tipo de paquete](https://www.mongodb.com/try/download/community)

## Endpoints
- **BASE_URL** = ""
### Autenticación - Login 
- **Método:** POST
- **URL:** `BASE_URL/v1/login`
- **Body (JSON) REQUEST:**
    ```json
    {
      "email":"example@example.com",
      "password":"password",
    }
- **Body (JSON) RESPONSE:**
    
    ```json
    {
      "status":true,   
      "token":"CASDASD_231ACSD",   
      "user": {
        "name": "name_user",
        "role": "admin",
      }
    }
    

### Autenticación - Register 
- **Método:** POST
- **URL:** `BASE_URL/v1/register`
- **Status** Pending 🥹🥹
- **Body (JSON) REQUEST:**
    ```json
    {
      null
    }
- **Body (JSON) RESPONSE:**
    
    ```json
    {
     null
    }