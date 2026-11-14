<?php
require_once "config/database.php";require_once "includes/auth.php";exigirRoles(["admin","empleado"]);
$mensaje="";
if($_SERVER["REQUEST_METHOD"]==="POST"){validarCsrf();$accion=$_POST["accion"]??"";
if($accion==="crear"){
$cliente=(int)($_POST["cliente_id"]??0);$producto=(int)($_POST["producto_id"]??0);$cantidad=(int)($_POST["cantidad"]??0);$obs=trim($_POST["observacion"]??"");
$s=$pdo->prepare("SELECT precio,existencia FROM productos WHERE id=? AND activo=1");$s->execute([$producto]);$p=$s->fetch();
if(!$cliente||!$p||$cantidad<1)$mensaje="Selecciona un cliente, producto y cantidad válida.";
elseif($p["existencia"]<$cantidad)$mensaje="No hay existencia suficiente para esa cantidad.";
else{try{$pdo->beginTransaction();$total=(float)$p["precio"]*$cantidad;
$s=$pdo->prepare("INSERT INTO pedidos(cliente_id,usuario_id,total,observacion) VALUES(?,?,?,?)");$s->execute([$cliente,$_SESSION["usuario"]["id"],$total,$obs]);$pedido=(int)$pdo->lastInsertId();
$s=$pdo->prepare("INSERT INTO detalle_pedido(pedido_id,producto_id,cantidad,precio_unitario,subtotal) VALUES(?,?,?,?,?)");$s->execute([$pedido,$producto,$cantidad,$p["precio"],$total]);
$s=$pdo->prepare("UPDATE productos SET existencia=existencia-? WHERE id=?");$s->execute([$cantidad,$producto]);$pdo->commit();$mensaje="Pedido #$pedido registrado correctamente.";}catch(Exception $e){if($pdo->inTransaction())$pdo->rollBack();$mensaje="No fue posible registrar el pedido.";}}}
elseif($accion==="estado"){$estado=$_POST["estado"]??"";$permitidos=["Pendiente","En preparación","Listo","Entregado","Cancelado"];if(in_array($estado,$permitidos,true)){$s=$pdo->prepare("UPDATE pedidos SET estado=? WHERE id=?");$s->execute([$estado,(int)$_POST["id"]]);$mensaje="Estado del pedido actualizado.";}}
elseif($accion==="eliminar"){$s=$pdo->prepare("SELECT estado FROM pedidos WHERE id=?");$s->execute([(int)$_POST["id"]]);$pedido=$s->fetch();if($pedido && $pedido["estado"]==="Pendiente"){$s=$pdo->prepare("DELETE FROM pedidos WHERE id=?");$s->execute([(int)$_POST["id"]]);$mensaje="Pedido pendiente eliminado. Nota: para mantener el inventario exacto, revisa la existencia del producto."; }else{$mensaje="Solo se pueden eliminar pedidos pendientes.";}}
}
$clientes=$pdo->query("SELECT id,nombre FROM clientes ORDER BY nombre")->fetchAll();$productos=$pdo->query("SELECT id,nombre,precio,existencia FROM productos WHERE activo=1 AND existencia>0 ORDER BY nombre")->fetchAll();
$lista=$pdo->query("SELECT p.*,c.nombre AS cliente,u.nombre AS empleado FROM pedidos p JOIN clientes c ON c.id=p.cliente_id JOIN usuarios u ON u.id=p.usuario_id ORDER BY p.id DESC")->fetchAll();
$titulo="Pedidos";require "includes/header.php";
?>
<h1>Gestión de pedidos</h1><?php if($mensaje):?><div class="exito"><?=escapar($mensaje)?></div><?php endif;?>
<section class="panel"><h2>Registrar pedido</h2><?php if(!$clientes||!$productos):?><p>Primero registra al menos un cliente y un producto con existencia disponible.</p><?php else:?><form method="post" class="form-grid">
<input type="hidden" name="csrf" value="<?=escapar(csrfToken())?>"><input type="hidden" name="accion" value="crear">
<div><label>Cliente</label><select name="cliente_id" required><option value="">Selecciona</option><?php foreach($clientes as $c):?><option value="<?=$c["id"]?>"><?=escapar($c["nombre"])?></option><?php endforeach;?></select></div>
<div><label>Producto</label><select name="producto_id" required><?php foreach($productos as $p):?><option value="<?=$p["id"]?>"><?=escapar($p["nombre"])?> · $<?=number_format($p["precio"],0,",",".")?> · Disp. <?=$p["existencia"]?></option><?php endforeach;?></select></div>
<div><label>Cantidad</label><input type="number" name="cantidad" min="1" value="1" required></div><div><label>Observación</label><input name="observacion"></div><button class="boton">Registrar pedido</button></form><?php endif;?></section>
<section class="panel"><h2>Pedidos registrados</h2><div class="tabla-wrap"><table><tr><th>N.º</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Registró</th><th>Acciones</th></tr>
<?php foreach($lista as $p):?><tr><td>#<?=$p["id"]?></td><td><?=escapar($p["cliente"])?></td><td><?=escapar($p["fecha"])?></td><td>$<?=number_format($p["total"],0,",",".")?></td><td><?=escapar($p["estado"])?></td><td><?=escapar($p["empleado"])?></td><td>
<form method="post" class="inline"><input type="hidden" name="csrf" value="<?=escapar(csrfToken())?>"><input type="hidden" name="accion" value="estado"><input type="hidden" name="id" value="<?=$p["id"]?>"><select name="estado"><?php foreach(["Pendiente","En preparación","Listo","Entregado","Cancelado"] as $e):?><option <?= $p["estado"]===$e?"selected":""?>><?=escapar($e)?></option><?php endforeach;?></select><button class="link">Actualizar</button></form>
<?php if($p["estado"]==="Pendiente"):?><form method="post" class="inline" onsubmit="return confirm('¿Eliminar pedido pendiente?')"><input type="hidden" name="csrf" value="<?=escapar(csrfToken())?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?=$p["id"]?>"><button class="link danger">Eliminar</button></form><?php endif;?></td></tr><?php endforeach;?></table></div></section>
<?php require "includes/footer.php"; ?>
