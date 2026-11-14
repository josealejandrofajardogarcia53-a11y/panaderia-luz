<?php
require_once "config/database.php";
$mensaje = "";
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        correo VARCHAR(120) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        rol ENUM('admin','empleado','cliente') NOT NULL DEFAULT 'cliente',
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    $pdo->exec("CREATE TABLE IF NOT EXISTS clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NULL UNIQUE,
        nombre VARCHAR(100) NOT NULL,
        telefono VARCHAR(30) NOT NULL,
        correo VARCHAR(120) NULL,
        direccion VARCHAR(180) NULL,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_cliente_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
    ) ENGINE=InnoDB");
    $pdo->exec("CREATE TABLE IF NOT EXISTS productos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        precio DECIMAL(10,2) NOT NULL,
        existencia INT NOT NULL DEFAULT 0,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    $pdo->exec("CREATE TABLE IF NOT EXISTS pedidos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_id INT NOT NULL,
        usuario_id INT NOT NULL,
        fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        estado ENUM('Pendiente','En preparación','Listo','Entregado','Cancelado') NOT NULL DEFAULT 'Pendiente',
        total DECIMAL(10,2) NOT NULL DEFAULT 0,
        observacion VARCHAR(255) NULL,
        CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
        CONSTRAINT fk_pedido_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ) ENGINE=InnoDB");
    $pdo->exec("CREATE TABLE IF NOT EXISTS detalle_pedido (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pedido_id INT NOT NULL,
        producto_id INT NOT NULL,
        cantidad INT NOT NULL,
        precio_unitario DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        CONSTRAINT fk_detalle_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
        CONSTRAINT fk_detalle_producto FOREIGN KEY (producto_id) REFERENCES productos(id)
    ) ENGINE=InnoDB");
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $stmt->execute(["admin@panaderialuz.com"]);
    if (!$stmt->fetch()) {
        $hash = password_hash("Admin123*", PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios(nombre,correo,password,rol) VALUES(?,?,?,'admin')");
        $stmt->execute(["Administrador", "admin@panaderialuz.com", $hash]);
    }
    $mensaje = "Instalación completada. Usuario: admin@panaderialuz.com | Contraseña inicial: Admin123*. Por seguridad, elimina instalar.php después de instalar.";
} catch (PDOException $e) {
    $mensaje = "Error durante la instalación. Revisa que la base de datos panaderia_luz exista y que MySQL esté activo.";
}
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Instalación</title><link rel="stylesheet" href="assets/style.css"></head>
<body><main class="contenedor"><section class="panel"><h1>Instalación de Panadería Luz</h1><p><?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?></p><a class="boton" href="login.php">Ir al inicio de sesión</a></section></main></body></html>
