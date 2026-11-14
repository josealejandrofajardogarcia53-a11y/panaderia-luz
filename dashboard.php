<?php
require_once "config/database.php"; require_once "includes/auth.php"; exigirLogin();
$productos = (int)$pdo->query("SELECT COUNT(*) FROM productos WHERE activo=1")->fetchColumn();
$clientes = (int)$pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$pedidos = (int)$pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$pendientes = (int)$pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado='Pendiente'")->fetchColumn();
$titulo="Panel principal"; require "includes/header.php";
?>
<h1>Panel principal</h1><p>Hola, <?= escapar($_SESSION["usuario"]["nombre"]) ?>. Rol: <?= escapar($_SESSION["usuario"]["rol"]) ?>.</p>
<div class="tarjetas">
<div class="tarjeta"><strong><?= $productos ?></strong><span>Productos activos</span></div>
<div class="tarjeta"><strong><?= $clientes ?></strong><span>Clientes registrados</span></div>
<div class="tarjeta"><strong><?= $pedidos ?></strong><span>Pedidos registrados</span></div>
<div class="tarjeta"><strong><?= $pendientes ?></strong><span>Pedidos pendientes</span></div>
</div>
<section class="panel"><h2>¿Qué puedes hacer?</h2><p>Registra productos, clientes y pedidos; consulta el estado de cada pedido y revisa el reporte general.</p>
<div class="acciones"><a class="boton" href="pedidos.php">Gestionar pedidos</a><a class="boton secundario" href="productos.php">Ver productos</a><a class="boton secundario" href="reportes.php">Ver reportes</a></div></section>
<?php require "includes/footer.php"; ?>
