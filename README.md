<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"><img src="https://images.contentstack.io/v3/assets/blt7151619cb9560896/blta30d0168850404a8/65fda6758f44440029c3a12a/la1a1agcxt7ppntea-logo-marks.svg"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
</p>

## Pasos para la instalación

1. **Instalar Node.js v.20:**
   - [Descargar e instalar Node.js](https://nodejs.org/)

2. **Instalar builderbot:**
    ```bash
    pnpm create builderbot@latest
    ```

3. **Instalar los paquetes necesarios:**
    ```bash
    pnpm install or  npm install
    ```

4. **Iniciar el servidor:**
    ```bash
    pnpm run dev o npm run dev
    ```

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