<?php
require_once "config/database.php";
require_once "includes/auth.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validarCsrf();
    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");
    $clave = $_POST["clave"] ?? "";
    if ($nombre === "" || !filter_var($correo, FILTER_VALIDATE_EMAIL) || $telefono === "" || strlen($clave) < 8) {
        $error = "Completa los campos. La contraseña debe tener mínimo 8 caracteres.";
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO usuarios(nombre,correo,password,rol) VALUES(?,?,?,'cliente')");
            $stmt->execute([$nombre,$correo,password_hash($clave,PASSWORD_DEFAULT)]);
            $uid = (int)$pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO clientes(usuario_id,nombre,telefono,correo,direccion) VALUES(?,?,?,?,?)");
            $stmt->execute([$uid,$nombre,$telefono,$correo,$direccion]);
            $pdo->commit();
            header("Location: login.php?registro=ok"); exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = "No se pudo crear la cuenta. Verifica que el correo no esté registrado.";
        }
    }
}
$titulo = "Registro de cliente"; require "includes/header.php";
?>
<section class="panel login"><h1>Crear cuenta de cliente</h1>
<?php if($error): ?><div class="alerta"><?= escapar($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= escapar(csrfToken()) ?>">
<label>Nombre completo</label><input name="nombre" required>
<label>Correo</label><input type="email" name="correo" required>
<label>Teléfono</label><input name="telefono" required>
<label>Dirección</label><input name="direccion">
<label>Contraseña (mínimo 8 caracteres)</label><input type="password" name="clave" minlength="8" required>
<button class="boton">Registrarme</button>
</form><p><a href="login.php">Volver al inicio de sesión</a></p></section>
<?php require "includes/footer.php"; ?>
