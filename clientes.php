<?php
require_once "config/database.php";require_once "includes/auth.php";exigirRoles(["admin","empleado"]);
$mensaje="";
if($_SERVER["REQUEST_METHOD"]==="POST"){validarCsrf();$accion=$_POST["accion"]??"";
if($accion==="guardar"){$id=(int)($_POST["id"]??0);$nombre=trim($_POST["nombre"]??"");$telefono=trim($_POST["telefono"]??"");$correo=trim($_POST["correo"]??"");$direccion=trim($_POST["direccion"]??"");
if($nombre===""||$telefono==="")$mensaje="Nombre y teléfono son obligatorios.";
elseif($id){$s=$pdo->prepare("UPDATE clientes SET nombre=?,telefono=?,correo=?,direccion=? WHERE id=?");$s->execute([$nombre,$telefono,$correo,$direccion,$id]);$mensaje="Cliente actualizado.";}
else{$s=$pdo->prepare("INSERT INTO clientes(nombre,telefono,correo,direccion) VALUES(?,?,?,?)");$s->execute([$nombre,$telefono,$correo,$direccion]);$mensaje="Cliente registrado.";}}
elseif($accion==="eliminar"){$s=$pdo->prepare("SELECT COUNT(*) FROM pedidos WHERE cliente_id=?");$s->execute([(int)$_POST["id"]]);if((int)$s->fetchColumn()>0)$mensaje="No se puede eliminar: el cliente tiene pedidos registrados.";else{$s=$pdo->prepare("DELETE FROM clientes WHERE id=?");$s->execute([(int)$_POST["id"]]);$mensaje="Cliente eliminado.";}}}
$editar=null;if(isset($_GET["editar"])){$s=$pdo->prepare("SELECT * FROM clientes WHERE id=?");$s->execute([(int)$_GET["editar"]]);$editar=$s->fetch();}
$lista=$pdo->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll();$titulo="Clientes";require "includes/header.php";
?>
<h1>Gestión de clientes</h1><?php if($mensaje):?><div class="exito"><?=escapar($mensaje)?></div><?php endif;?>
<section class="panel"><h2><?= $editar?"Editar cliente":"Registrar cliente"?></h2><form method="post" class="form-grid">
<input type="hidden" name="csrf" value="<?=escapar(csrfToken())?>"><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id" value="<?=escapar($editar["id"]??0)?>">
<div><label>Nombre</label><input name="nombre" required value="<?=escapar($editar["nombre"]??"")?>"></div><div><label>Teléfono</label><input name="telefono" required value="<?=escapar($editar["telefono"]??"")?>"></div>
<div><label>Correo</label><input type="email" name="correo" value="<?=escapar($editar["correo"]??"")?>"></div><div><label>Dirección</label><input name="direccion" value="<?=escapar($editar["direccion"]??"")?>"></div><button class="boton">Guardar cliente</button><?php if($editar):?><a class="boton secundario" href="clientes.php">Cancelar</a><?php endif;?></form></section>
<section class="panel"><h2>Clientes registrados</h2><div class="tabla-wrap"><table><tr><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Dirección</th><th>Acciones</th></tr>
<?php foreach($lista as $c):?><tr><td><?=escapar($c["nombre"])?></td><td><?=escapar($c["telefono"])?></td><td><?=escapar($c["correo"])?></td><td><?=escapar($c["direccion"])?></td><td><a href="?editar=<?=$c["id"]?>">Editar</a> <form method="post" class="inline" onsubmit="return confirm('¿Eliminar cliente?')"><input type="hidden" name="csrf" value="<?=escapar(csrfToken())?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?=$c["id"]?>"><button class="link danger">Eliminar</button></form></td></tr><?php endforeach;?></table></div></section>
<?php require "includes/footer.php"; ?>
