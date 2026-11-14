<?php
require_once "config/database.php"; require_once "includes/auth.php"; exigirRoles(["admin","empleado"]);
$mensaje=""; $editar=null;
if ($_SERVER["REQUEST_METHOD"]==="POST") {
    validarCsrf(); $accion=$_POST["accion"]??"";
    if ($accion==="guardar") {
        $id=(int)($_POST["id"]??0); $nombre=trim($_POST["nombre"]??""); $descripcion=trim($_POST["descripcion"]??"");
        $precio=(float)($_POST["precio"]??0); $existencia=(int)($_POST["existencia"]??-1);
        if($nombre==="" || $precio<=0 || $existencia<0) $mensaje="Verifica nombre, precio y existencia.";
        else if($id>0){$s=$pdo->prepare("UPDATE productos SET nombre=?,descripcion=?,precio=?,existencia=? WHERE id=?");$s->execute([$nombre,$descripcion,$precio,$existencia,$id]);$mensaje="Producto actualizado.";}
        else {$s=$pdo->prepare("INSERT INTO productos(nombre,descripcion,precio,existencia) VALUES(?,?,?,?)");$s->execute([$nombre,$descripcion,$precio,$existencia]);$mensaje="Producto registrado.";}
    } elseif($accion==="eliminar") {
        $id=(int)$_POST["id"];
        $s=$pdo->prepare("SELECT COUNT(*) FROM detalle_pedido WHERE producto_id=?");$s->execute([$id]);
        if((int)$s->fetchColumn()>0){$s=$pdo->prepare("UPDATE productos SET activo=0 WHERE id=?");$s->execute([$id]);$mensaje="Producto desactivado porque aparece en pedidos.";}
        else {$s=$pdo->prepare("DELETE FROM productos WHERE id=?");$s->execute([$id]);$mensaje="Producto eliminado.";}
    }
}
if(isset($_GET["editar"])){$s=$pdo->prepare("SELECT * FROM productos WHERE id=?");$s->execute([(int)$_GET["editar"]]);$editar=$s->fetch();}
$lista=$pdo->query("SELECT * FROM productos WHERE activo=1 ORDER BY id DESC")->fetchAll();
$titulo="Productos";require "includes/header.php";
?>
<h1>Gestión de productos</h1><?php if($mensaje):?><div class="exito"><?=escapar($mensaje)?></div><?php endif;?>
<section class="panel"><h2><?= $editar?"Editar producto":"Registrar producto" ?></h2>
<form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?=escapar(csrfToken())?>"><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id" value="<?=escapar($editar["id"]??0)?>">
<div><label>Nombre</label><input name="nombre" required value="<?=escapar($editar["nombre"]??"")?>"></div>
<div><label>Precio (COP)</label><input type="number" name="precio" min="1" step="0.01" required value="<?=escapar($editar["precio"]??"")?>"></div>
<div><label>Existencia</label><input type="number" name="existencia" min="0" required value="<?=escapar($editar["existencia"]??"0")?>"></div>
<div><label>Descripción</label><input name="descripcion" value="<?=escapar($editar["descripcion"]??"")?>"></div>
<button class="boton">Guardar producto</button><?php if($editar):?><a class="boton secundario" href="productos.php">Cancelar</a><?php endif;?></form></section>
<section class="panel"><h2>Productos registrados</h2><div class="tabla-wrap"><table><tr><th>Nombre</th><th>Precio</th><th>Existencia</th><th>Acciones</th></tr>
<?php foreach($lista as $p):?><tr><td><?=escapar($p["nombre"])?></td><td>$<?=number_format($p["precio"],0,",",".")?></td><td><?=escapar($p["existencia"])?></td><td><a href="?editar=<?=$p["id"]?>">Editar</a>
<form method="post" class="inline" onsubmit="return confirm('¿Eliminar o desactivar este producto?')"><input type="hidden" name="csrf" value="<?=escapar(csrfToken())?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?=$p["id"]?>"><button class="link danger">Eliminar</button></form></td></tr><?php endforeach;?>
</table></div></section><?php require "includes/footer.php"; ?>
