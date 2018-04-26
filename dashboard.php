<?/*
$id=session_id();
if (empty($id)) {
	$id=rand();
	session_id($id);
}*/
session_name('esmu');
session_start();
//echo var_dump($_SESSION).'<br>'.session_name().'<br>'.session_id();?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>eSMU - Home</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="./css/gral.css">
<link rel="stylesheet" href="./css/MenuStyles.css" type="text/css" /><style type="text/css">._css3m{display:none}</style>
<link rel="stylesheet" type="text/css" href="./css/smoothness/jquery-ui-1.10.4.custom.css">
<link rel="shortcut icon" type="image/x-icon" href="./imagenes/logo.ico">
<script src="./js/jquery-1.10.2.js"></script>
<script src="./js/jquery-ui-1.10.4.custom.min.js"></script>  
<script src="./js/permisos.js"></script>
<script src="./js/frame.js"></script>
<script type="text/javascript">

	function login() {
		var user=document.getElementById('txtUsuario');
		var pass=document.getElementById('txtPass');

	    var xmlhttp = new XMLHttpRequest();
	            xmlhttp.onreadystatechange = function() {
	                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	                  var resp=xmlhttp.responseText;
	                  
	                  if (resp=='error') {
	                  	alert('Usuario o contraseña incorrectas');
	                  	return false;
	                  }

	                  window.location.reload();
	                }
	            }
	        xmlhttp.open("GET","ajaxfunciones.php?consulta=login&usuario="+user.value+"&pass="+pass.value,true);
	        xmlhttp.send();
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
<script>
function mail() {
	window.location.href="./ajaxfunciones.php?consulta=mail";
}
</script>

</head>
<body>
	<div class="fondo">
	<? include("phpdatabase.php");

			include("header.php"); 

	//include("cabeceramail.php");

	if (isset($_SESSION["idUsuario"])) {
		
		if ($_SESSION['ChPass']==1) {
			echo "<script>location.href='./password.php';</script>";
			die();
		}
            switch ($_SESSION['tipo']) {
                case 'Kar':
                    $cadena=getQuery("Pending","",1);
                    $rnoti=mysql_query($cadena[1]);
                    $fnoti=mysql_fetch_assoc($rnoti);

                    $cadena=getQuery("SC","",1);
                    $rnoti2=mysql_query($cadena[4]);

                    $fnoti2=mysql_fetch_assoc($rnoti2);

                    $cadena=getQuery("BOD","",1);
                    $rnoti3=mysql_query($cadena[1]);
                    $fnoti3=mysql_fetch_assoc($rnoti3);
                    break;
                
                case 'Filial':
                    $cadena=getQuery("Filials",$_SESSION,20);
                    $rnoti=mysql_query($cadena[3]);

                    $fnoti=mysql_fetch_assoc($rnoti);

                    $cadena=getQuery("PendingIR", $_SESSION, 1);
                    $rnoti2=mysql_query($cadena[1]);
                    $fnoti2=mysql_fetch_assoc($rnoti2);

                    break;
                case 'Global Sourcing':
                    $cadena=getQuery("SC",$_SESSION,20);
                    $rnoti=mysql_query($cadena[7]);
                    $fnoti=mysql_fetch_assoc($rnoti);

                    $cadena=getQuery("TotvsCheck","",1);
                    $rnoti2=mysql_query($cadena[0]);
                    $fnoti2=mysql_fetch_assoc($rnoti2);

                    $cadena=getQuery("Pending",$_SESSION,1);
                    $rnoti3=mysql_query($cadena[3]);
                    $fnoti3=mysql_fetch_assoc($rnoti3);

                    break;

            } 
            
            

	?>
	<script>toggle("linkBack","visible", false);</script>
	<div class="divmain">
		<p class="welcome">Welcome to eSMU, <? echo $_SESSION['nombre']; ?> </p>
		<p class="lastupdate" style="margin-left:10%;">Last update: <? echo LastUpdate(); ?></p>
		<? switch ($_SESSION['tipo']) {
			case 'Kar':
				if ($fnoti['cant']>0) {?><p class="info" style="margin-left:15%;">You have <strong><? echo $fnoti['cant']; ?></strong> pending comments to read. <a href="pending.php">Click here to check them</a> </p><?}
				if ($fnoti2['cant']>0) {?><p class="info" style="margin-left:15%;">You have <strong><? echo $fnoti2['cant']; ?></strong> New Orders pending comments to read. <a href="sc.php?pend=1">Click here to check them</a> </p><?}
				if ($fnoti3['cant']>0) {?><p class="info" style="margin-left:15%;">There exists <strong><? echo $fnoti3['cant']; ?></strong> Products with BOD Updates, please check. <a href="Reportes.php">Click here to check them</a> </p><?}
				break;
			case 'Admin':
				?><p> </p><?
				break;
			case 'Filial':
				if ($fnoti['cant']>0) {?><p class="info" style="margin-left:15%;">You have <? echo $fnoti['cant']; ?> pending arrival PIs to set. <a href="filials.php?filter=F_ARRIBO=null|F_EMBARQ=not null|">Click here to check them out</a> </p><?}
				if ($fnoti2['cant']>0) {?><p class="info" style="margin-left:15%;">You have <? echo $fnoti2['cant']; ?> pending Inpsection Reports to check. <a href="pendingir.php">Click here to check them out</a> </p><?}
				break;
			case 'Global Sourcing':
				if ($fnoti['cant']+$fnoti['archivadas']>0) {?><p class="info" style="margin-left:15%;">Tiene <? echo $fnoti['cant']+$fnoti['archivadas']; ?> comentarios New Orders pendientes. <a href="sc.php?pend=1"><?= $fnoti['cant']; ?> Activas </a> | <a href="archivo.php"><?= $fnoti['archivadas']; ?> Archivadas </a> </p><?}
				if ($fnoti2['cant']>0) {?><p class="info" style="margin-left:15%;">Tiene <? echo $fnoti2['cant']; ?> New Orders pendientes de exportar a Totvs con diferencias. <a href="bod.php">Click aquí para verlos.</a> </p><?}
				if ($fnoti3['cant']>0) {?><p class="info" style="margin-left:15%;">Tiene <? echo $fnoti3['cant']; ?> comentarios SMU pendientes. <a href="gssmu.php?pend=1">Click aquí para verlos.</a> </p><?}
				break;
			case 'GaMa Electrical':

				break;
			default:
				
				break;
		}
		//echo "Formato de fecha: ".$_SESSION['DateFormat'];
		?>
<!--		<div class="infotimeline" id="divInfoSC">
			<table class="tblInfotimeline">
				<tr><th colspan="6">Last 10 New Orders updates</th></tr>
				<?/* $cadena="SELECT 'New order' Tipo, u.NroSC, PI, Fecha, u.Usuario Usuario, Comentario, u.id FROM SC_Updates u INNER JOIN SC s ON u.SCid=s.id ORDER BY Fecha DESC LIMIT 10";
				$dbi=GetConnection();
				$r10sc=mysqli_query($dbi, $cadena);
				while ($f10sc=mysqli_fetch_assoc($r10sc)) {
					echo '<tr><td>'.$f10sc['Fecha'].'</td><td>'.$f10sc['Usuario'].'</td><td>'.$f10sc['PI'].'</td><td>'.$f10sc['Comentario'].'</td></tr>';
				}
				*/?>
			</table>
		</div>
		<div class="infotimeline" id="divInfoESMU">
			
		</div>-->
		<div id="divManuales">
			<ul>
				<? switch ($_SESSION['tipo']) {
					case 'OEM': ?>
					<li><a href="./Templates/eSMU OEMs.pdf"><img src="./imagenes/icon_pdf.png" width="32">OEM user manual</a></li>
				<? break;
					case 'kar': ?>
					<li><a href="./Templates/eSMU KARs.pdf"><img src="./imagenes/icon_pdf.png" width="32">Kar user manual</a></li>

				<? break;
					default: ?>
				<li><a href="./Templates/eSMU Branches.pdf"><img src="./imagenes/icon_pdf.png" width="32">Branches user manual</a></li>
				<li><a href="./Templates/eSMU Price list.pdf"><img src="./imagenes/icon_pdf.png" width="32">Price list user manual</a></li>
				<? break;
					}?>


			</ul>
		</div>
	</div>
		<?}
		else { ?>
		<div style="height:auto;background-color: transparent;" align="center" id="divContent">
			<p class="textoGeneral fs13 fnegra" style="position:relative;left:0px;">Welcome to eSMU, the Ga.Ma online trading platform.</p>
			<p class="textoGeneral fs1 fnormal" style="position:relative;left:0px;">Please, log in or contact us to create a new account</p>
			<button class="botonok" id="cmdLogin" style="width:200px;font-size:1.5em">Log in</button><br>
			<button class="botonok" id="cmdNew" style="font-size:12px;margin-top:10px;">Apply for a new account</button>
			<div id="divLogin">
				<table>
					<tr>
						<td class="textoGeneral fs1 fnormal">User name:</td>
						<td><input type="text" id="txtUsuario"></td>
					</tr>
					<tr>
						<td class="textoGeneral fs1 fnormal">Password:</td>
						<td><input type="password" id="txtPass"></td>
					</tr>
					<tr><td colspan="2" align="center">
						<button id="cmdAceptar" class="botonAcceso" style="position:relative;" onclick="login();">Log in</button>
						<button class="botonAcceso" id="cmdCancelar" style="position:relative;" onclick="toggle('divLogin', 'visible');">Cancel</button>
					</td></tr>
				</table>
			</div>
			<div id="divSign">
				<table>
					<tr>
						<td class="textoGeneral fs1 fnormal">Full name:</td>
						<td><input type="text" id="txtReqUsuario"></td>
					</tr>
					<tr>
						<td class="textoGeneral fs1 fnormal">Country:</td>
						<td><input type="text" id="txtReqCountry"></td>
					</tr>
					<tr>
						<td class="textoGeneral fs1 fnormal">eMail Address:</td>
						<td><input type="text" id="txtReqMail"></td>
					</tr>
					<tr>
						<td class="textoGeneral fs1 fnormal">Profile:</td>
						<td><input type="text" id="txtReqProfile" placeholder="ie: kar, branch manager, qa analyst, etc."></td>
					</tr>
					<tr><td colspan="2" align="center">
						<button id="cmdAceptar" class="botonAcceso" style="position:relative;" onclick="sign();">Request credentials</button>
						<button class="botonAcceso" id="cmdCancelar" style="position:relative;" onclick="toggle('divSign', 'visible');">Cancel</button>
					</td></tr>
				</table>				
			</div>
			<div>
			
			</div>
		</div>
		<?}

		include("footer.html"); ?>

	</div>
</body>	