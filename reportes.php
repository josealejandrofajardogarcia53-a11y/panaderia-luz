<?php
require_once "config/database.php";require_once "includes/auth.php";exigirRoles(["admin","empleado"]);
$totalPedidos=(int)$pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$pendientes=(int)$pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado='Pendiente'")->fetchColumn();
$entregados=(int)$pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado='Entregado'")->fetchColumn();
$ventas=(float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE estado<>'Cancelado'")->fetchColumn();
$porEstado=$pdo->query("SELECT estado,COUNT(*) cantidad FROM pedidos GROUP BY estado ORDER BY cantidad DESC")->fetchAll();
$titulo="Reportes";require "includes/header.php";
?>
<h1>Reporte general de pedidos</h1><div class="tarjetas"><div class="tarjeta"><strong><?=$totalPedidos?></strong><span>Total de pedidos</span></div><div class="tarjeta"><strong><?=$pendientes?></strong><span>Pendientes</span></div><div class="tarjeta"><strong><?=$entregados?></strong><span>Entregados</span></div><div class="tarjeta"><strong>$<?=number_format($ventas,0,",",".")?></strong><span>Valor no cancelado</span></div></div>
<section class="panel"><h2>Pedidos por estado</h2><table><tr><th>Estado</th><th>Cantidad</th></tr><?php foreach($porEstado as $r):?><tr><td><?=escapar($r["estado"])?></td><td><?=escapar($r["cantidad"])?></td></tr><?php endforeach;?></table>
<button class="boton" onclick="window.print()">Imprimir / Guardar como PDF</button></section>
<?php require "includes/footer.php"; ?>
