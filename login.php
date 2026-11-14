<?php
require_once "config/database.php";
require_once "includes/auth.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validarCsrf();
    $correo = trim($_POST["correo"] ?? "");
    $clave = $_POST["clave"] ?? "";
    $stmt = $pdo->prepare("SELECT id,nombre,correo,password,rol FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $u = $stmt->fetch();
    if ($u && password_verify($clave, $u["password"])) {
        session_regenerate_id(true);
        $_SESSION["usuario"] = ["id"=>$u["id"], "nombre"=>$u["nombre"], "correo"=>$u["correo"], "rol"=>$u["rol"]];
        header("Location: dashboard.php"); exit;
    }
    $error = "Correo o contraseña incorrectos.";
}
$titulo = "Iniciar sesión";
require "includes/header.php";
?>
<section class="login panel">
<h1>Bienvenido a Panadería Luz</h1>
<p>Ingresa para gestionar pedidos, clientes y productos.</p>
<?php if ($error): ?><div class="alerta"><?= escapar($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= escapar(csrfToken()) ?>">
<label>Correo electrónico</label><input type="email" name="correo" required>
<label>Contraseña</label><input type="password" name="clave" required>
<button class="boton" type="submit">Iniciar sesión</button>
</form>
<p class="nota">¿Eres cliente? <a href="registro.php">Crear cuenta</a></p>
</section>
<?php require "includes/footer.php"; ?>
