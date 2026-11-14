<?php
require_once "config/database.php";require_once "includes/auth.php";exigirRoles(["admin"]);
$mensaje="";
if($_SERVER["REQUEST_METHOD"]==="POST"){validarCsrf();$accion=$_POST["accion"]??"";
if($accion==="crear"){$nombre=trim($_POST["nombre"]??"");$correo=trim($_POST["correo"]??"");$clave=$_POST["clave"]??"";$rol=$_POST["rol"]??"empleado";
if($nombre===""||!filter_var($correo,FILTER_VALIDATE_EMAIL)||strlen($clave)<8||!in_array($rol,["admin","empleado"],true))$mensaje="Verifica los datos. La contraseña debe tener mínimo 8 caracteres.";
else{try{$s=$pdo->prepare("INSERT INTO usuarios(nombre,correo,password,rol) VALUES(?,?,?,?)");$s->execute([$nombre,$correo,password_hash($clave,PASSWORD_DEFAULT),$rol]);$mensaje="Usuario creado.";}catch(PDOException $e){$mensaje="No se pudo crear. El correo puede estar registrado.";}}}
elseif($accion==="eliminar"){$id=(int)$_POST["id"];if($id===(int)$_SESSION["usuario"]["id"])$mensaje="No puedes eliminar tu propia cuenta.";else{$s=$pdo->prepare("DELETE FROM usuarios WHERE id=?");$s->execute([$id]);$mensaje="Usuario eliminado.";}}}
$usuarios=$pdo->query("SELECT id,nombre,correo,rol,creado_en FROM usuarios ORDER BY id DESC")->fetchAll();$titulo="Usuarios";require "includes/header.php";
?>
<h1>Administración de usuarios</h1><?php if($mensaje):?><div class="exito"><?=escapar($mensaje)?></div><?php endif;?>
<section class="panel"><h2>Crear empleado o administrador</h2><form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?=escapar(csrfToken())?>"><input type="hidden" name="accion" value="crear">
<div><label>Nombre</label><input name="nombre" required></div><div><label>Correo</label><input type="email" name="correo" required></div><div><label>Contraseña (mín. 8)</label><input type="password" name="clave" minlength="8" required></div><div><label>Rol</label><select name="rol"><option value="empleado">Empleado</option><option value="admin">Administrador</option></select></div><button class="boton">Crear usuario</button></form></section>
<section class="panel"><h2>Usuarios</h2><table><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Acción</th></tr><?php foreach($usuarios as $u):?><tr><td><?=escapar($u["nombre"])?></td><td><?=escapar($u["correo"])?></td><td><?=escapar($u["rol"])?></td><td><?php if($u["id"]!=$_SESSION["usuario"]["id"]):?><form method="post" onsubmit="return confirm('¿Eliminar usuario?')"><input type="hidden" name="csrf" value="<?=escapar(csrfToken())?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?=$u["id"]?>"><button class="link danger">Eliminar</button></form><?php else:?>Cuenta actual<?php endif;?></td></tr><?php endforeach;?></table></section>
<?php require "includes/footer.php"; ?>
