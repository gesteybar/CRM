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
<link rel="stylesheet" type="text/css" href="./css/config.css">
<link rel="shortcut icon" type="image/x-icon" href="./imagenes/logo.ico">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.4.custom.min.js"></script>  
<script src="./js/frame.js"></script>
<script src="./js/permisos.js"></script>
<script type="text/javascript">
	var currUser=<?= isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 0 ?>;
	if (!checkAccess(currUser,'1')) 
		window.history.back();
</script>
<script type="text/javascript">


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


	?>
	<div id="wrapper">
		<h1>Opciones de configuración</h1>
		<div class="itemMenu">
			<div onclick="go(currUser,3)">
				<img src="imagenes/userwhite.png">
				<h2>Gestión de usuarios</h2>
			</div>
			<p>Administración de usuarios y permisos</p>
		</div>
		<div class="itemMenu">
			<div>
				<img src="imagenes/gearwhite.png">
				<h2>Parámetros</h2>
			</div>
			<p>Definición de parámetros de sistema</p>
		</div>		
	</div>

	</div>
</body>	