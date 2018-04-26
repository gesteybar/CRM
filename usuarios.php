<?
session_name('crmdev');
session_start();
//echo var_dump($_SESSION).'<br>'.session_name().'<br>'.session_id();?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>CRM - Home</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="./css/gral.css">
<link rel="stylesheet" type="text/css" href="./css/usuarios.css">
<link rel="shortcut icon" type="image/x-icon" href="./imagenes/logo.ico">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.4.custom.min.js"></script>  
<script src="./js/frame.js"></script>
<script src="./js/permisos.js"></script>
<script type="text/javascript">
	/*----------------------------variables de módulo ---------------------------------*/
	var currUser=<?= isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 0 ?>;
	var hidUsuario=0;

	if (!checkAccess(currUser,'3')) 
		window.history.back();
</script>
<script type="text/javascript">
function mostrar(ventana) {
	$("#"+ventana).show();
	$("#"+ventana).parent().show();
}
function cerrar(ventana) {
	$("#"+ventana).hide();
	$("#"+ventana).parent().hide();

}
function cargarUsuarios() {
	oAjax.request="cargarUsuarios";
	oAjax.send(resp);

	function resp(data) {
		if (data.responseText.length<3) {
			setValue('tbodyUsuarios', '');
			return false;
		}

		var obj=JSON.parse(data.responseText);
		JsonToTable(obj,'tbodyUsuarios', false);
		AgregarColumna('tbodyUsuarios');
		setValue('spanCantidad', Filas('tbodyUsuarios'));
		AgregarBotonTabla('tbodyUsuarios', 4, 'menu2.png', 'popup', 0);
	}
}

function popup(id, obj) {
	var rect = obj.getBoundingClientRect();
	//console.log(rect.top, rect.right, rect.bottom, rect.left);	
	var estado=obj.parentNode.parentNode.cells[3].innerText;
	if (estado=="Activo") {
		var fc='Bloquear usuario';
		var img='block.png';
	} else {
		var fc='Habilitar usuario';
		var img='greenalert.png';		
	}	
	var jason=[{"Op":"Ver / Modificar usuario","Fc":"editar('"+id+"')", "img":"pencil.png"},{"Op":"Permisos", "Fc":"permisos('"+id+"')", "img":"permisos.png"},{"Op":fc, "Fc":"blockear('"+id+"')", "img":img},{"Op":"Borrar usuario", "Fc":"eliminarUsuario('"+id+"')", "img":"trash.png"}];
	showPopup(jason, rect.left-100, rect.top, '','classPopup',document.getElementsByClassName('fondo')[0]);	
	
}

function nuevoUsuario() {
	hidUsuario=0;
	$("#spanID").text(hidUsuario);
	setValue('winNuevoTitulo', 'Nuevo usuario');
	mostrar('winNuevo');
}
function editar(id) {
	quitarPopup();
	oAjax.request="LeerUsuarios?user="+id;
	oAjax.send(resp);

	function resp(data) {
		if (data.responseText.length<3) {
			alert('El usuario no existe');
			cargarUsuarios();
			return	false;
		}

		var obj=JSON.parse(data.responseText);
		$("#spanID").text(obj[0].idUsuario);
		$("#txtLogin").val(obj[0].Login);
		$("#txtNombre").val(obj[0].Nombre);
		$("#txtMail").val(obj[0].Mail);
		$("#cboCambia").val(obj[0].CambiaPass);
		$("#cboSector").val(obj[0].idSector);
		$("#cboEstado").val(obj[0].idEstado);

		setValue('winNuevoTitulo', 'Modificar usuario');
		mostrar('winNuevo');		
	}
}
function validarUsuario() {
	//valida la contraseña si se ingresa alguna
	var UpdatePass="N";
	if ($("#spanID").text()=='0' && $("#txtPass").val()=='') {
				alert('Se requiere definir una contraseña para el alta de usuario');
				return false;
			}

	if ($("#txtPass").val()!='') {
		
		if (($("#txtPass").val()!=$("#txtPass2").val())) {
			alert('La contraseña y la verificación de contraseña no coinciden');
			return false;
		}

		UpdatePass="S";
	}

	var idUsuario=$("#spanID").text();
	var sector=$("#cboSector").val();
	var estado=$("#cboEstado").val();
	var login=$("#txtLogin").val();
	var pass=$("#txtPass").val();
	var nombre=$("#txtNombre").val();
	var mail=$("#txtMail").val();
	var cambia=$("#cboCambia").val();

	if (UpdatePass=="S") {
		var pass=$("#txtPass").val();
		oAjax.request="IngresarUsuario?idUsuario="+idUsuario+"&idSector="+sector+"&idEstado="+estado+"&Login="+login+"&Pass="+pass+"&ZUSCOD=&Nombre="+nombre+"&Mail="+mail+"&Cambia="+cambia;
	}
	else 
		oAjax.request="IngresarUsuario?idUsuario="+idUsuario+"&idSector="+sector+"&idEstado="+estado+"&Login="+login+"&Pass=&ZUSCOD=&Nombre="+nombre+"&Mail="+mail+"&Cambia="+cambia;

	oAjax.send(resp);

	function resp(data) {
		if (data.responseText.length<3) {
			alert('El usuario no existe');
			cerrar('winNuevo');
			cargarUsuarios();
			return	false;
		}

		var obj=JSON.parse(data.responseText);
		if (!isNaN(obj[0].respuesta)) {
			cerrar('winNuevo');
			cargarUsuarios()
		} else {
			alert(obj[0].respuesta);

		}
	}
}
function blockear(id) {
	var user=id;
	quitarPopup();
	oAjax.request="BlockUser?user="+user+"&autor="+currUser;
	oAjax.send(resp);

	function resp(data) {
		if (data.responseText.length<3) {
			alert('El usuario no existe');
			cargarUsuarios();
			return	false;
		}

		var obj=JSON.parse(data.responseText);
		if (!isNaN(obj[0].respuesta)) {
			cargarUsuarios()
		} else {
			alert(obj[0].respuesta);

		}
	}	

}
function eliminarUsuario(id) {
	if (!confirm('Confirma eliminar permanentemente este usuario?'))
		return false;
	
	var user=id;
	quitarPopup();
	oAjax.request="DeleteUser?user="+user+"&autor="+currUser;
	oAjax.send(resp);

	function resp(data) {
		if (data.responseText.length<3) {
			alert('El usuario no existe');
			cargarUsuarios();
			return	false;
		}

		var obj=JSON.parse(data.responseText);
		if (!isNaN(obj[0].respuesta)) {
			cargarUsuarios()
		} else {
			alert(obj[0].respuesta);

		}
	}	
}
function showMenu() {
	
	$( "#divMenu" ).animate({
	    //width: "70%",
	    //opacity: 0.4,
	    marginLeft: "260px"
	    //fontSize: "3em",
	    //borderWidth: "10px"
	}, 500 );
	
}

$(document).mouseup(function(e) 
{
    var container = $("#divMenu");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
        $("#divMenu").animate({
        	marginLeft:"-260px"
        },100);
    }
    
});
</script>


</head>
<body>
	<div class="fondonegro" style="display:none">
		<div class="ventana" id="winNuevo">
			<h1 id="winNuevoTitulo"></h1>
			<table class="tablaBase tablaForm" id="tblForm">
				<tr>
					<td>ID Usuario</td>
					<td><span id="spanID"></span></td>
				</tr>
				<tr>
					<td>Login</td>
					<td><input type="text" id="txtLogin"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type="password" id="txtPass"></td>
					<td>Confirmación</td>
					<td><input type="password" id="txtPass2"></td>
				</tr>
				<tr>
					<td>Nombre y apellido</td>
					<td colspan="3"><input type="text" id="txtNombre"></td>
				</tr>
				<tr>
					<td>Sector</td>
					<td><select id="cboSector"></select></td>
					<td>Estado</td>
					<td><select id="cboEstado"></select></td>
					<script>LlenarComboSQL('cboSector', 'select idSector, Nombre from Sectores order by 1', false);
					LlenarComboSQL('cboEstado', "select idEstado, Estado from Estados where Grupo='Usuarios' order by 1", false);
					</script>
				</tr>
				<tr>
					<td>Mail</td>
					<td colspan="3"><input type="email" id="txtMail"></td>
				</tr>
				<tr>
					<td>Precisa cambiar contraseña</td>
					<td><select id="cboCambia"><option value="S">Si</option><option value="N">No</option></select></td>
				</tr>
				<tr>
					<td colspan="4" align="center">
						<button class="boton btnAzul" onclick="validarUsuario()">Aceptar</button>
						<button class="boton btnNaranja" onclick="cerrar('winNuevo')">Cerrar</button>
						
					</td>
				</tr>
			</table>

		</div>
	</div>
	<div class="fondo">
	<? include("phpdatabase.php");

		include("header.php"); 

		include("footer.html"); 


	?>

	<div id="wrapper">

		<h1>Maestro de usuarios</h1>
		<div id="tblContainer">
			<div id="barraBotones">
				<button class="boton toolbar" type="button" onclick="nuevoUsuario()"><img src="imagenes/nuevasc.png">Nuevo usuario</button>
				<button class="boton toolbar" type="button" onclick=""><img src="imagenes/guardar.png">Exportar</button>
				<button class="boton toolbar" type="button" onclick=""><img src="imagenes/printer.png">Imprimir</button>
			</div>
			<table class="tablaBase" id="tblUsuarios">
				<thead>
					<tr>
						<th>ID Usuario</th><th>Login</th><th>Nombre</th><th>Estado</th><th>Opciones</th>
					</tr>
					<tbody id="tbodyUsuarios">
						
					</tbody>
					<tfoot>
						<tr>
							<th colspan="6">Cantidad de elementos: <span id="spanCantidad"></span></th>
						</tr>
					</tfoot>
				</thead>
			</table>
		</div>
	</div>

	</div>

	<script type="text/javascript">cargarUsuarios()</script>
</body>	