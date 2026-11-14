PANADERÍA LUZ - SIGEP
Sistema Web de Gestión de Pedidos
Tecnologías: PHP, MySQL, HTML, CSS y JavaScript (validaciones del navegador / impresión).

INSTALACIÓN EN XAMPP
1. Copia la carpeta Panaderia_Luz_Fase2 dentro de C:\xampp\htdocs\
2. Abre XAMPP e inicia Apache y MySQL.
3. Entra a http://localhost/phpmyadmin
4. Importa database/panaderia_luz.sql. El script crea la base panaderia_luz y sus tablas.
5. Abre http://localhost/Panaderia_Luz_Fase2/instalar.php para crear el usuario administrador inicial.
6. Inicia sesión en http://localhost/Panaderia_Luz_Fase2/login.php

USUARIO INICIAL
Correo: admin@panaderialuz.com
Contraseña: Admin123*
Cambia esta contraseña después de ingresar y elimina instalar.php una vez termine la instalación.

USO
- Productos: registra productos con precio y existencia.
- Clientes: registra clientes.
- Pedidos: selecciona cliente, producto y cantidad. El sistema calcula el total y descuenta existencia.
- Reportes: consulta resumen por estado y utiliza Imprimir / Guardar como PDF.
- Usuarios: el administrador crea empleados y otros administradores.
- Registro: los clientes pueden crear una cuenta propia.

SEGURIDAD Y CALIDAD
- Contraseñas almacenadas con password_hash y verificadas con password_verify.
- Consultas SQL preparadas mediante PDO.
- Control de sesión y roles.
- Token CSRF en formularios POST.
- Validación básica de campos y control de existencia.
- Transacción para guardar pedido, detalle y actualizar inventario.

NOTA ACADÉMICA
Este prototipo implementa las funciones principales de registro y gestión de pedidos para el problema definido en la Fase 1. Antes de una puesta en producción se deben añadir HTTPS, copias de seguridad automatizadas, auditoría, pruebas de carga y revisión profesional de seguridad.
