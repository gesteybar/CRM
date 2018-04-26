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
<link rel="shortcut icon" type="image/x-icon" href="./imagenes/logo.ico">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.4.custom.min.js"></script>  
<script src="./js/permisos.js"></script>
<script src="./js/frame.js"></script>
<script type="text/javascript">
	var currUser=<?= isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 0 ?>;
	function login() {
		var user=document.getElementById('txtLgUsuario');
		var pass=document.getElementById('txtLgPass');

		oAjax.request="LogIn?user="+user.value+"&pass="+pass.value;
		oAjax.send(resp);

		function resp(data) {
			if (data.responseText.length<3) {
				alert('Error en respuesta. Comuníquese con el área de sistemas');
				return false;
				
			} else {
				var obj=JSON.parse(data.responseText);
				if (!isNaN(obj[0].idUsuario)) {
					if (obj[0].CambiaPass=="S") {
						location.href='cambiapass.php?user='+obj[0].idUsuario;
						return false;
					}

					oAjax.server="ajaxfunciones.php?consulta=";
					oAjax.request="createSession&idUsuario="+obj[0].idUsuario+"&idEstado="+obj[0].idEstado+"&idSector="+obj[0].idSector+"&Nombre="+obj[0].Nombre;
					oAjax.send(resp2);
					function resp2(data) {
						location.reload();
					}
				}
				
			}

		}


	}

	function sign() {
		var user=document.getElementById('txtReqUsuario');
		var mail=document.getElementById('txtReqMail');
		var pais=document.getElementById('txtReqCountry');
		var perfil=document.getElementById('txtReqProfile');

	    var xmlhttp = new XMLHttpRequest();
	            xmlhttp.onreadystatechange = function() {
	                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	                  var resp=xmlhttp.responseText;
	                  var obj=JSON.parse(resp);
	                  if (obj.respuesta=='ok') {
	                  	document.getElementById("divSign").innerHTML='<p class="textoGeneral fs11">New account request has been sent<br>Thank You</p><button type="button" onclick="window.location.reload();" class="botonok">Close</button>';
	                  	return false;
	                  } else {
	                  	document.getElementById("divSign").innerHTML='<p class="textoGeneral fs11">Error sending request: '+obj.respuesta+'. Please try again</p><button type="button" onclick="window.location.reload();" class="botonok">Close</button>';
	                  	return false;

	                  }

	                  
	                }
	            }
	        xmlhttp.open("GET","mailalert.php?alerta=Sign&nombre="+user.value+"&mail="+mail.value+"&pais="+pais.value+"&perfil="+perfil.value,true);
	        xmlhttp.send();

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
<script type="text/javascript">
	$(document).ready(function(){
	  $("#cmdLogin").click(function(){
	  	$("#divSign").hide();
	    $("#divLogin").fadeIn();
	  });
	});

	$(document).ready(function(){
	  $("#cmdNew").click(function(){
	  	$("#divLogin").hide();
	    $("#divSign").fadeIn();
	  });
	});	

</script>

</head>
<body>
	<div class="fondo">
	<? include("phpdatabase.php");

		include("header.php"); 

		include("footer.html"); 

		if (!isset($_SESSION['idUsuario'])) {
			include("inc/login.php");
		}
		else {
	?>


<!--		<div id="divLateral">
			<ul>
				<li><a href="javascript:void(0);" onclick="showMenu();"><img src="./imagenes/menuwhite.png" title="Mostrar Menu"></a></li>
				<li><a href="javascript:void(0);" onclick="search();"><img src="./imagenes/lupawhite.png" title="Buscar"></a></li>
				<li><a href="javascript:void(0);" onclick="config();"><img src="./imagenes/gearwhite.png" title="Configuración"></a></li>
			</ul>
		</div>
		
		<div id="divMenu">
			<h2>Vistas disponibles</h2>
			<ul>
				<li><a href="#">Gestiones pendientes</a></li>
				<li><a href="#">Todas las gestiones</a></li>
				<li><a href="#">Autorizaciones</a></li>
			</ul>
		</div>	-->
		<div id="container">
			<div class="itemMenu">
				<div>
					<img src="imagenes/nuevasc.png">
					<h2>Nuevo Ticket</h2>
				</div>
				<p>Crear un nuevo incidente por reclamo o gestión interna</p>
			</div>
			<div class="itemMenu">
				<div>
					<img src="imagenes/menuwhite.png">
					<h2>Listados</h2>
				</div>
				<p>Listados de incidentes.</p>
			</div>
			<div class="itemMenu">
				<div>
					<img src="imagenes/briefcase.png">
					<h2>Autorizaciones</h2>
				</div>
				<p>Autorizaciones de incidentes que requieren un nivel superior</p>
			</div>
		</div>
	<? 

	} ?>

	</div>
</body>	