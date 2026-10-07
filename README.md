# StyleNow

Sistema web de gestión para salones de belleza, desarrollado con **PHP y Laravel**. Permite administrar reservas, clientes, empleados, inventario y facturación desde un solo lugar, con paneles distintos según el rol de cada usuario.

## Funcionalidades

**Autenticación**
- Registro e inicio de sesión de usuarios
- Cambio y recuperación de contraseña por correo
- Bloqueo de cuenta con notificación por correo

**Panel de administrador**
- Dashboard con estadísticas
- Gestión de usuarios y empleados
- Reservas y lista de citas
- Facturación
- Inventario: productos, proveedores y solicitudes
- Reportes
- Recordatorios y configuración del sistema

**Panel de empleado y de cliente**
- Vistas propias para cada rol

**Correos automáticos**
- Confirmación de cita
- Recordatorio de cita y de retorno
- Envío de factura por correo
- Recuperación de contraseña

## Tecnologías

- PHP
- Laravel (Blade, Eloquent, Artisan)
- HTML, CSS y JavaScript
- [MySQL / SQL Server] 

## Requisitos

- PHP 8.1 o superior
- Composer
- Node.js y npm
- [MySQL / SQL Server]

## Instalación

1. Clona el repositorio:
   ```bash
   git clone https://github.com/jordanjr1/StyleNow.git
   cd StyleNow
   ```

2. Instala las dependencias:
   ```bash
   composer install
   npm install
   ```

3. Crea el archivo de entorno y genera la clave de la aplicación:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configura en el archivo `.env` los datos de tu base de datos y del correo (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `MAIL_*`).

5. Ejecuta las migraciones:
   ```bash
   php artisan migrate
   ```

6. Inicia el servidor:
   ```bash
   php artisan serve
   ```

La aplicación quedará disponible en `http://127.0.0.1:8000`.

## Autor

**Jordan Ramos**
Estudiante de desarrollo de software, Instituto Tecnológico Cordillera
Correo: jordanramos3323@gmail.com
GitHub: [@jordanjr1](https://github.com/jordanjr1)
