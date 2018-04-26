<?
session_name('crmdev');
session_start();
//echo var_dump($_SESSION).'<br>'.session_name().'<br>'.session_id();?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>eSMU - Home</title>
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
	var currUser=<?= isset($_GET['user']) ? $_GET['user'] : 0 ?>;
	var hidUsuario=0;

	if (!checkAccess(currUser,'3')) 
		window.history.back();
</script>
<script type="text/javascript">
	function cargarDatos() {
		oAjax.request="LeerUsuarios?user="+currUser;
		oAjax.send(resp);

		function resp(data) {
			if (data.responseText.length<3) {
				alert('El usuario de refecrencia no existe');
				return false;
			}

			var obj=JSON.parse(data.responseText);
			$("#spanUser").text(obj[0].Nombre);
			$("#txtPass").val(obj[0].Pass.substring(0,8));
			$("#txtPass2").val(obj[0].Pass.substring(0,8));

			$("#txtPass").focus();


		}

	}

	function validarUsuario() {
		//valida la contraseña si se ingresa alguna
		if ($("#txtPass").val()!='') {
			if ($("#spanID").text()=='0') {
				alert('Se requiere definir una contraseña para el alta de usuario');
				return false;
			}

			if (($("#txtPass").val()!=$("#txtPass2").val())) {
				alert('La contraseña y la verificación de contraseña no coinciden');
				return false;
			}

		}

		oAjax.request="CambiarPass?user="+currUser+"&pass="+$("#txtPass").val();
		oAjax.send(resp);

		function resp(data) {
			if (data.responseText.length<3) {
				alert('El usuario de referencia no existe');
				return false;
			}
			
			var obj=JSON.parse(data.responseText);

			if (obj[0].respuesta=="1") {
				alert('Contraseña actualizada correctamente');
				location.href="index.php";
			} else {
				alert(obj[0].respuesta);
			}
			

		}

	}

</script>


</head>
<body>
	<? include("phpdatabase.php");

		include("header.php"); 

		include("footer.html"); 


	?>

	<div id="wrapper">

		<h1>Actualización de usuario</h1>

		<div class="ventana" id="winNuevo">
			<h1>Cambio de contraseña - <span id="spanUser"></span></h1>
			<table class="tablaBase tablaForm" id="tblForm">
				<tr>
					<td>Password</td>
					<td><input type="password" id="txtPass"></td>
					<td>Confirmación</td>
					<td><input type="password" id="txtPass2"></td>
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

	</div>

	<script type="text/javascript">cargarDatos()</script>
</body>	