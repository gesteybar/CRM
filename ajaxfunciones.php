<?
session_name('crmdev');
session_start();

if (isset($_GET['consulta']))
	$consulta=$_GET['consulta'];
else 
	$consulta=$_POST['consulta'];

$dateformat='%d/%m/%Y';
header('Access-Control-Allow-Origin: *');

include("phpdatabase.php");

function GrabarAccion($usuario, $pi, $accion) {
    $cadena="insert into Historial (Fecha, Usuario, PI, Accion) values (";
    $cadena.="NOW(), '".$usuario."', '".$pi."', '".$accion."');";
	
	$dbi=GetConnection();
    mysql_query($dbi,$cadena);
}
function nombreArchivo($archivo) {
	$ult=strrpos($archivo, '/');
	if (!$ult) 
		$nombre=substr($archivo, $ult);	
	else
		$nombre=substr($archivo, $ult+1);
	return $nombre;
}
function extensionArchivo($archivo) {
	$ult=strrpos($archivo, '.');
	if (!$ult) 
		$nombre=substr($archivo, $ult);	
	else
		$nombre=substr($archivo, $ult+1);
	return $nombre;
}
function toJSON($r) {
	if (mysqli_num_rows($r)==0) return '{}';

	$resp='[';

	$campos=mysqli_fetch_fields($r);
	
	while ($f=mysqli_fetch_array($r)) {
		$resp.='{';
		$i=0;
		foreach ($campos as $campo) {
			$resp.='"'.$campo->name.'":"'.$f[$i].'",';
			$i++;
		}
		
		
		$resp=substr($resp,0,strlen($resp)-1);
		$resp.="},";
		}
 	$resp=substr($resp,0,strlen($resp)-1);
 	$resp.="]";


	return $resp;

}
function query($q, $t, $db) {
		if (!isset($db) || $db=='' || $db == null)
			$db=GetConnection();

		//tipos: Q: query | E:Execution
		if ($t=='Q') {
			
			$r=mysqli_query($db,$q);
			if (mysqli_errno($db)!=0) 
				$resp='error: '.mysqli_error($db);
			else
				$resp=toJSON($r);

			
		} else {
			mysqli_query($db,$q);
			if (mysqli_errno($db)!=0) 
				$resp='error: '.mysqli_error($db);
			else
				$resp='ok';
		}

		return $resp;

}

switch ($consulta) {
	case 'customQuery':
		
		if (sizeof($_GET)>0) {
			$query=$_GET['query'];
			$tipo=$_GET['tipo'];
		} else {
			$query=$_POST['query'];
			$tipo=$_POST['tipo'];			
		}
		
		$resp=query($query, $tipo);
		echo $resp;
		break;
	case 'createSession':
		$idUsuario=$_GET['idUsuario'];
		$idSector=$_GET['idSector'];
		$idEstado=$_GET['idEstado'];
		$Nombre=$_GET['Nombre'];

		$_SESSION['idUsuario']=$idUsuario;
		$_SESSION['idSector']=$idSector;
		$_SESSION['idEstado']=$idEstado;
		$_SESSION['Nombre']=$Nombre;

		break;
	case 'login':
		$user=$_GET['usuario'];
		$pass=$_GET['pass'];
		$cadena="select idUsuario, Login, Nombre, Mail from Usuarios where Login='".$user."' and Pass=MD5('".$pass."')";
		$db=GetConnectionSQL('AX');
		$r=mysqli_query($db, $cadena);
		
		if (mysqli_num_rows($r)==0) 
			echo 'error';
		else
			if ($f=mysqli_fetch_assoc($r)) {
					$_SESSION['idUsuario']=$f['idUsuario'];
					$_SESSION['nombre']=$f['Nombre'];
					$_SESSION['login']=$f['Login'];
					$_SESSION['mail']=$f['Mail'];
					
					echo 'ok';
			}
				
		break;
	case 'logoff':
		session_unset();
		session_destroy();
		echo 'ok';
		break;
	case 'cambiarPass':
		$usuario=$_GET['usuario'];
		$pass=$_GET['pass'];
		$idUsuario=$_GET['idUsuario'];

		if ($idUsuario=='')
			$cadena="update Usuarios set Pass=md5($pass), activo=1 where Login='$usuario'";
		else 
			$cadena="update Usuarios set Pass=md5($pass), activo=1 where idUsuario='$idUsuario'";

		echo query($cadena, 'E');

		break;
	case 'acceso':
		$mod=$_GET['modulo'];
		$nivel=$_GET['nivel'];
		$usuario=$_GET['usuario'];
		if ($usuario=='' || !isset($usuario) || $usuario==null) $usuario=$_SESSION['idUsuario'];
		$db=GetConnection();
		$cadena="select m.Pagina, FC_Permisos(".$usuario.",".$mod.") permiso, conv(FC_Permisos(".$usuario.",".$mod."),2,10) valor from Permisos p inner join Modulos m ON p.idModulo=m.idModulo WHERE p.idModulo=".$mod." AND idUsuario=".$usuario;
		$r=mysqli_query($db, $cadena);
		$f=mysqli_fetch_assoc($r);
		if (mysqli_errno($db)>0) 
			echo '{"respuesta":"'.mysqli_error($db).' - '. $cadena.'"}';
		else
			echo '{"respuesta":"'.$f['permiso'].'","decimal":"'.$f['valor'].'","ruta":"'.$f['Pagina'].'"}';
		break;	
	case 'docsPendientes':
		$idUsuario=$_GET['usuario'];
		$cadena="select count(1) cant from DocAutorizacion where UsrAutoriza=$idUsuario and FechaAprob is null";

		echo query($cadena, 'Q');
		break;
	case 'saveUser':
		$id=$_GET['idUsuario'];
		$login=$_GET['login'];
		$nombre=$_GET['nombre'];
		$pass=$_GET['pass'];
		$sector=$_GET['sector'];
		$perfil=$_GET['perfil'];
		$estado=$_GET['estado'];
		$mail=$_GET['mail'];

		$cadena="call SP_SaveUser($id,'$login','$nombre','$pass',$estado,'$perfil', '$mail')";

		$db=GetConnection();
		mysqli_query($db, $cadena);
		if (mysqli_errno($db)>0) 
			echo $cadena . " - Error: ". mysqli_error($db);
		else 
			echo 'ok';

		break;
	case 'deleteUser':
		$id=$_GET['id'];
		$cadena="delete from Usuarios where idUsuario=$id";
		$db = GetConnection();

		mysqli_query($db,$cadena);
		if (mysqli_errno($db)>0)
			echo mysqli_error($db);
		else
			echo 'ok';

		break;

	case 'grabarPermisos':
		$idUsuario=$_POST['idUsuario'];
		$json=json_decode($_POST['permisos'], true);
//die("bbb");
		$str="insert into Permisos (idUsuario, idModulo, idPais) values ";
		//die(var_dump($json));
		for ($i=0; $i < sizeof($json); $i++) { 

			if ($json[$i]['dato4']=='1') {

				$str.="(".$idUsuario.", '".$json[$i]['dato1']."',511),";
			}
		}
		$str=trim($str,',');	

		$db=GetConnection();

		mysqli_autocommit($db,false);

		$cadena="delete from Permisos where idUsuario=$idUsuario";
		mysqli_query($db,$cadena);
		if (mysqli_errno($db)>0) {
			echo mysqli_error($db);
			mysqli_rollback($db);
			die();
		}

		mysqli_query($db,$str);
		if (mysqli_errno($db)>0) {
			echo mysqli_error($db);
			mysqli_rollback($db);
			die();
		}
		else {
			mysqli_commit($db);
			echo 'ok';
		}

		break;
	case 'estructura':
		$est=$_GET['estructura'];
		$cadena="SELECT d.*, e.Tip FROM Diccionario d INNER JOIN Estructuras e ON e.idDiccionario=d.idDiccionario WHERE e.Codigo='$est' order by e.Orden";

		echo query($cadena, "Q");
		break;
	case 'getCampos':
		
		$cadena="SELECT idDiccionario, Alias FROM Diccionario where Tabla='Entidad' and incluidoABM=1 order by Alias";

		echo query($cadena, "Q");
		break;
	case 'guardarEstructura':
		$cadena=$_POST['datos'];
		$id=$_POST['id'];
		$nombre=$_POST['nombre'];

		$json=json_decode($cadena, true);

		$db=GetConnection();
		mysqli_query($db, 'start transaction');
		if ($id==0) {
			$str="insert into Tipologias (Nombre) values ('$nombre')";
			mysqli_query($db, $str);
			$id=mysqli_insert_id($db);
/*			$str="select max(Codigo) maximo from Estructuras";
			$r=mysqli_query($db, $str);
			$f=mysqli_fetch_assoc($r);
			$id=$f['maximo']+1;*/
		} else {
			$str="delete from Estructuras where Codigo=".$id;
			
			mysqli_query($db,$str);
			if (mysqli_errno($db)>0) {
				mysqli_query($db,'rollback');
				die("Error: ".mysqli_error($db)." ".$str);
			}

		}
		$str="insert into Estructuras (idDiccionario, Nombre, Codigo, Orden, Tip, Requerido) values ";

		for ($i=0; $i < sizeof($json); $i++) { 
			$str.="(".$json[$i]["id"].",'".$json[$i]["campo"]."','".$id."','".$json[$i]["orden"]."','".$json[$i]["descripción"]."','".$json[$i]["requerido"]."'),";
		}
		$str.="(1,'idEntidad',$id,999,'',1),";
		$str.="(2,'idTipologia',$id,999,'',1),";
		$str.="(34,'UltMod',$id,999,'',1),";
		$str.="(35,'UsuarioCreador',$id,999,'',1),";
		$str.="(36,'UsuarioEdit',$id,999,'',1)";

		//$str=trim($str, ",");
		mysqli_query($db, $str);
		if (mysqli_errno($db)>0) {
			mysqli_query($db, 'rollback');
			echo "Error: ".mysqli_error($db)." ".$str;
			die();
		}
		mysqli_query($db, 'commit');
		echo 'ok';
		break;
	case 'guardarEntidad':
		$idEntidad=$_POST['idEntidad'];
		$query=$_POST['query'];
		$json=json_decode($query, true);
		$esNuevo=true;
//guarda el registro de entidad, nuevo o modificado

		$db=GetConnection();
		//$resp=query('start transaction','E', $db);
		//mysqli_begin_transaction($db,MYSQLI_TRANS_START_READ_WRITE) ;

		if ($idEntidad==0) {
			$r=mysqli_query($db,'select max(idEntidad) as maximo from Entidad');
			$f=mysqli_fetch_assoc($r);
			$idEntidad=$f['maximo']+1;
			$str="insert into Entidad (";
			$ent=$json[0];
			foreach ($ent as $key => $value) {
				if ($key!='tabla') {
					$str.=$key.',';
				} 
			}
			$str=trim($str,',').") values (";
			foreach ($ent as $key => $value) {
				if ($key!='tabla') {
					if ($key=='idEntidad')
						$str.=$idEntidad.",";
					else 
						$str.="'".$value."',";
				} 
			}
			$str=trim($str,',').")";

			$resp=query($str, 'E', $db);
			if ($resp!='ok') {
				//mysqli_rollback($db);
				die($str."  -  ".$resp);
			}

		} else {
			$esNuevo=false;
			$ent=$json[0];
			$str="update Entidad set ";
			foreach ($ent as $key => $value) {
				if ($key!='tabla') {
					$str.=$key."='".$value."',";
				} 
			}
			$str=trim($str,',')." where idEntidad=$idEntidad";

			$resp=query($str, 'E', $db);
			if ($resp!='ok') {
				//mysqli_rollback($db);
				
				die($str."  -  ".$resp);
			}

			//die($str);
		}


//guarda los datos de las tablas adjuntas, ciclando en el json

		for ($i=1; $i < sizeof($json); $i++) { 
			$tbl=$json[$i];

			$r=mysqli_query($db,"select TablaRemota from Diccionario where Campo='".$tbl['tabla']."'");
			$f=mysqli_fetch_assoc($r);
			$tblRemota=$f['TablaRemota'];
			$str="delete from $tblRemota where idEntidad=$idEntidad";
			$resp=query($str,'E', $db);
			if ($resp!='ok') {
				//mysqli_rollback($db);
				die($str."  -  ".$resp);
			}
			$str="insert into ".$tblRemota." values ";
			$valores=$tbl['valores'];
/*				query('rollback','E', $db);
				die(var_dump($valores));*/
			for ($j=0; $j < sizeof($valores); $j++) { 
				$str.="('".$valores[$j]."',$idEntidad),";
			}
			$str=trim($str,',');
			//die($str);
			$resp=query($str, 'E', $db);
			if ($resp!='ok') {
				//mysqli_rollback($db);
				die($str."  -  ".$resp);
			}

		}

// agrega los permisos full al usuario creador
		if ($esNuevo) {
			$str="insert into LegajosPermisos (idUsuario, idEntidad, Nivel) values (".$_SESSION['idUsuario'].",$idEntidad,31)";
			$resp=query($str, "E", $db);
			if ($resp!='ok') {
				//mysqli_rollback($db);
				die($str."  -  ".$resp);
			}			
		}
		//$resp=query('commit', 'E', $db);
		//mysqli_commit($db);
		echo $resp;
		break;
	case 'cargarEntidad':
		$id=$_GET['idEntidad'];

		$str="select idTipologia from Entidad where idEntidad=$id";
		$db=GetConnection();
		$r=mysqli_query($db, $str);
		$f=mysqli_fetch_assoc($r);
		$tipo=$f['idTipologia'];

		$str="SELECT GROUP_CONCAT(d.Campo) campos FROM Diccionario d INNER JOIN Estructuras e ON d.idDiccionario=e.idDiccionario WHERE e.Codigo=$tipo AND d.TipoDato<>'tabla' order by incluidoABM DESC, Orden ASC;";

		$rc=mysqli_query($db,$str);
		$fc=mysqli_fetch_assoc($rc);
		$campos=$fc['campos'];

		$cadena="select $campos from Entidad where idEntidad=$id";

		$resp='['.trim(trim(query($cadena,'Q'),'['),']');

		$cadena=query("SELECT 'tblAccionistas' tabla,e.idPersona Codigo, CONCAT(IFNULL(p.Nombre,''),' ',IFNULL(p.Apellido,''),'',IFNULL(p.RazonSocial, '')) Dato FROM EntidadAccionistas e INNER JOIN Personas p ON e.idPersona=p.idPersona where e.idEntidad=$id", 'Q');
		if ($cadena!='{}')
			$resp.=",".$cadena;

		$cadena=query("SELECT 'tblApoderados' tabla, e.idPersona Codigo, CONCAT(IFNULL(p.Nombre,''),' ',IFNULL(p.Apellido,''),'',IFNULL(p.RazonSocial, '')) Dato FROM EntidadApoderados e INNER JOIN Personas p ON e.idPersona=p.idPersona where e.idEntidad=$id", 'Q');
		if ($cadena!='{}')
			$resp.=",".$cadena;

		$cadena=query("SELECT 'tblEmpresas' tabla, e.idEmpresa Codigo, em.RazonSocial Dato FROM EntidadEmpresas e INNER JOIN Empresas em ON e.idEmpresa=em.idEmpresa WHERE e.idEntidad=$id", 'Q');
		if ($cadena!='{}')
			$resp.=",".$cadena;

		$cadena=query("SELECT 'tblPaises' tabla, e.idPais Codigo, p.Pais Dato FROM EntidadPais e INNER JOIN Paises p ON e.idPais=p.idPais WHERE e.idEntidad=$id", 'Q');
		if ($cadena!='{}')
			$resp.=",".$cadena;


		$resp.="]";

		echo $resp;
		break;
	case 'cargarDocumentos':
		$idEntidad=$_GET['idEntidad'];
		$idUsuario=$_GET['idUsuario']||$_SESSION['idUsuario'];

		$cadena="SELECT d1.idDocumento, d1.Link, d1.Titulo, d1.Version, d1.FechaCarga, '' publico, FC_PermisosDoc($idUsuario,d1.idDocumento) nivel,
			IFNULL((SELECT IFNULL(Estado,'APROBADO') FROM DocAutorizacion WHERE idDocAutorizacion=(SELECT MAX(idDocAutorizacion) FROM DocAutorizacion WHERE idDocumento=d1.idDocumento)), 'APROBADO') Estado 
			FROM Documentos d1 WHERE d1.idEntidad=$idEntidad AND d1.Version=(SELECT MAX(d2.version) FROM Documentos d2 WHERE d2.idEntidad=d1.idEntidad AND d2.Titulo=d1.Titulo) AND LEFT(FC_PermisosDoc($idUsuario,d1.idDocumento),1)=1 ORDER BY d1.FechaCarga";

		echo query($cadena, "Q");
		break;
	case 'busqueda':
	
		$tipo=$_GET['tipo'];
		switch ($tipo) {
			case '1':

				$tipologia=$_GET['tipologia'];
				$campo=$_GET['campo'];
				$dato=$_GET['dato'];
				if ($tipo=='0') {

				}
				$cadena="Call SP_BUSCAR($tipologia, '$campo', '$dato');";
				break;
			case '2':
				$usuario=$_GET['dato'];
				$cadena="call SP_BuscarXUsuario('$usuario');";
				break;
			case '3':
				$campo=$_GET['campo'];
				$dato=$_GET['dato'];
				$cadena="CALL SP_BuscarXDocumento('$campo','$dato');";
				break;
			case '4':
				$campo=$_GET['campo'];
				$dato=$_GET['dato'];
				if ($campo=='Todos') $campo='';
				$cadena="CALL SP_Buscar(0,'$campo','$dato');";
				
				break;
			case '5':
				$campo=$_GET['campo'];
				$dato=$_GET['dato'];
				if ($campo=='Todos') $campo='';
				$cadena="CALL SP_BuscarEnTablas('$campo','$dato');";

			
		}
		
		$db=GetConnection();
		$resp="";
		$r=mysqli_query($db,$cadena);
		while ($f=mysqli_fetch_assoc($r)) {
			$resp.=$f['idEntidad'].',';
		}
		$resp=trim($resp,',');
		if ($resp=='') $resp='0';
//die($cadena);
		echo "<script>location.href='listado.php?ids=$resp'</script>";
		break;
	case 'guardarPermisosDoc':

		$idEntidad=$_POST['idEntidad'];
		$idDoc=$_POST['idDoc'];
		$json=json_decode($_POST['data'], true);

		$db=GetConnectionObj();
		$ent=$json[0];
		
		$db->autocommit(false);
		
		$cadena="delete from PermisosDoc where idDocumento=$idDoc";
		$sent=$db->prepare($cadena);
		
		if (!$sent->execute()) {
			$db->rollback();
			die($db->error);
		}

		$str="insert into PermisosDoc (idUsuario, idDocumento, idPerfil, Permisos) values ";
		for ($i=0; $i < sizeof($json); $i++) { 
			if ($json[$i]['dato1']=='Perfil')
				$str.="(null, $idDoc, ".$json[$i]['dato2'].",'".$json[$i]['dato4'].$json[$i]['dato5'].$json[$i]['dato6']."'),";
			else 
				$str.="(".$json[$i]['dato2'].", $idDoc, null,'".$json[$i]['dato4'].$json[$i]['dato5'].$json[$i]['dato6']."'),";
		}
		$str=trim($str,',');	

/*		$sent=$db->prepare($str);
		if (!$sent->execute()) {
			$db->rollback();
			die($db->error);
		}*/
		$db->query($str);
		if ($db->errno>0) {
			die($db->error);

		}
//die($str);
		//$db->rollback();
		$db->commit();
		//mysqli_commit($db);
		$db->close();

		echo 'ok';
		break;
	case 'validarDocumentos':
		$idEntidad=$_GET['idEntidad'];
		$idUsuario=$_SESSION['idUsuario'];

		$cadena="SELECT d1.idDocumento, d1.Link, d1.Titulo, d1.Version, d1.FechaCarga, d1.Publico, p.Permisos,  
				(SELECT IFNULL(Estado,'APROBADO') FROM DocAutorizacion WHERE idDocAutorizacion=(SELECT MAX(idDocAutorizacion) FROM DocAutorizacion WHERE idDocumento=d1.idDocumento)) Estado
					FROM Documentos d1 INNER JOIN PermisosDoc p ON d1.idDocumento=p.idDocumento AND 
					(p.idUsuario=$idUsuario OR p.idPerfil IN (SELECT idPerfil FROM Usuarios WHERE idUsuario=$idUsuario) )
					WHERE d1.idEntidad=$idEntidad AND d1.Version=(SELECT MAX(d2.version) FROM Documentos d2 WHERE d2.idEntidad=d1.idEntidad AND d2.Titulo=d1.Titulo) ORDER BY d1.FechaCarga";
		

		$resp=query($cadena, "Q");
		echo $resp;
		break;
	case 'guardarPermisosEnt':

		$idEntidad=$_POST['idEntidad'];
		$json=json_decode($_POST['data'], true);

		$db=GetConnectionObj();
		$ent=$json[0];
		
		$db->autocommit(false);
		
		$cadena="delete from LegajosPermisos where idEntidad=$idEntidad";
		$sent=$db->prepare($cadena);
		
		if (!$sent->execute()) {
			$db->rollback();
			die($db->error);
		}

		$str="insert into LegajosPermisos (idUsuario, idEntidad, idPerfil, nivel) values ";
		for ($i=0; $i < sizeof($json); $i++) { 
			$permisos=(string)($json[$i]['dato8'].$json[$i]['dato7'].$json[$i]['dato6'].$json[$i]['dato5'].$json[$i]['dato4']);
			$res=base_convert($permisos, 2, 10);
			if ($json[$i]['dato1']=='Perfil')
				$str.="(null, $idEntidad,".$json[$i]['dato2'].", '".$res."'),";
			else 
				$str.="(".$json[$i]['dato2'].", $idEntidad, null,'".$res."'),";
		}
		$str=trim($str,',');	

/*		$sent=$db->prepare($str);
		if (!$sent->execute()) {
			$db->rollback();
			die($db->error);
		}*/
		$db->query($str);
		if ($db->errno>0) {
			die($db->error.$str);

		}
//die($str);
		//$db->rollback();
		$db->commit();
		//mysqli_commit($db);
		$db->close();

		echo 'ok';
		break;
	case 'cargarPersonas':
		$tipo=$_GET['tipo'];
		$filtro=$_GET['filtro'];

		$cadena="select idPersona, concat(ifnull(RazonSocial,''),ifnull(concat(Apellido, ' ', Nombre), '')) persona, TipoPersona from Personas where 1=1 ";

		if ($tipo!='')
			$cadena.=" and TipoPersona='$tipo' ";

		if ($filtro!='') 
			$cadena.=" and $filtro ";

		$cadena.=" order by 2";

		echo query($cadena,"Q");

		break;
	case 'guardarPersona':
		$id=$_GET['id'];
		$nombre=$_GET['nombre'];
		$apellido=$_GET['apellido'];
		$razon=$_GET['razon'];
		$cargo=$_GET['cargo'];
		$tipo=$_GET['tipo'];

		$cadena="call SP_SavePersona($id, '$nombre', '$apellido', '$razon', '$cargo', '$tipo')";

		$resp=query($cadena, "E");

		echo $resp;
		break;
	case 'cargarEmpresas':
		
		$filtro=$_GET['filtro'];

		$cadena="select idEmpresa, RazonSocial, Pais from Empresas e inner join Paises p on e.idPais=p.idPais where 1=1 ";

		if ($filtro!='') 
			$cadena.=" and $filtro ";

		$cadena.=" order by 2";

		echo query($cadena,"Q");

		break;
	case 'guardarEmpresa':
		$id=$_GET['id'];
		$razon=$_GET['razon'];
		$pais=$_GET['pais'];

		$cadena="call SP_SaveEmpresa($id, '$razon','$pais')";

		$resp=query($cadena, "E");

		echo $resp;
		break;		
	case 'cargarGrupos':
		
		$filtro=$_GET['filtro'];

		$cadena="select idGrupo, Nombre from Grupos where 1=1 ";

		if ($filtro!='') 
			$cadena.=" and $filtro ";

		$cadena.=" order by 2";

		echo query($cadena,"Q");

		break;
	case 'guardarGrupo':
		$id=$_GET['id'];
		$nombre=$_GET['nombre'];
		
		$cadena="call SP_SaveGrupo($id, '$nombre')";

		$resp=query($cadena, "E");

		echo $resp;
		break;			
	case 'versionado':
		$id=$_GET['id'];
		
		$cadena="CALL SP_Versionado($id)";
		echo query($cadena, "Q");
		break;
	case 'cargarERel':
		$id=$_GET['idEntidad'];
		$tipo=$_GET['tipo'];

		if ($tipo=="1")
			$cadena="SELECT ee.idRelacion, t.Nombre Tipo, e2.Codigo, e2.Nombre, e2.Fecha,'1' tipo
				FROM EntidadEntidades ee 
				INNER JOIN Entidad e1 ON ee.idEntidad=e1.idEntidad
				INNER JOIN Entidad e2 ON ee.idEntidadRel=e2.idEntidad
				INNER JOIN Tipologias t ON e2.idTipologia=t.idTipologia
				WHERE ee.idEntidad=$id
				union
				SELECT ee.idRelacion, t.Nombre Tipo, e1.Codigo, e1.Nombre, e1.Fecha,'2'
				FROM EntidadEntidades ee 
				INNER JOIN Entidad e1 ON ee.idEntidad=e1.idEntidad
				INNER JOIN Entidad e2 ON ee.idEntidadRel=e2.idEntidad
				INNER JOIN Tipologias t ON e1.idTipologia=t.idTipologia
				WHERE ee.idEntidadRel=$id";

		echo query($cadena, "Q");
		break;
	case 'addMNR':
		$idEntidad=$_GET['idEntidad'];
		$add=$_GET['idAdd'];

		$cadena="insert into EntidadEntidades (idEntidad, idEntidadRel) values ($idEntidad, $add);";
		echo query($cadena, "E");
		break;
	case 'delMNR':
		$id=$_GET['id'];
		$cadena="delete from EntidadEntidades where idRelacion=$id";
		echo query($cadena, "E");
		break;
	case 'listaPendientes':
		$idUsuario=$_GET['idUsuario'];
		$cadena="SELECT da.idDocAutorizacion, FC_PermisosDoc($idUsuario,d.idDocumento), d.Link, Titulo, NombreArchivo, MAX(VERSION) VERSION, d.idEntidad, e.Codigo
			FROM Documentos d INNER JOIN Entidad e ON d.idEntidad=e.idEntidad
			LEFT JOIN DocAutorizacion da ON d.idDocumento=da.idDocumento 
			WHERE  da.FechaAprob IS NULL AND da.UsrAutoriza=$idUsuario GROUP BY d.idDocumento";

		echo query($cadena, "Q");
		break;
	case 'buscarPendiente':
		$idUsuario=$_GET['idUsuario'];
		$idEntidad=$_GET['idEntidad'];
		$cadena="SELECT d.idEntidad, e.Codigo, e.Nombre, da.FechaEmision, u.nombre Creador, u2.nombre Remitente, FC_PermisosDoc($idUsuario, $idEntidad) permisos
			FROM Documentos d 
			INNER JOIN Entidad e ON d.idEntidad=e.idEntidad
			LEFT JOIN Usuarios u ON d.idUsuario=u.idUsuario 
			LEFT JOIN DocAutorizacion da ON d.idDocumento=da.idDocumento 
			LEFT JOIN Usuarios u2 ON da.UsrRemitente = u2.idUsuario
			WHERE d.idDocumento=$idEntidad";

		echo query($cadena, "Q");

		break;
	case 'historialAprob':
		$idDoc=$_GET['idDoc'];

		$cadena="SELECT idDocAutorizacion, u.Nombre Remitente, u2.Nombre Aprobador, FechaEmision, FechaAprob, Estado, '' Menu 
				FROM DocAutorizacion da 
				INNER JOIN Usuarios u ON da.UsrRemitente=u.idUsuario
				LEFT JOIN Usuarios u2 ON da.UsrAutoriza=u2.idUsuario
				WHERE da.idDocumento=$idDoc
				ORDER BY idDocAutorizacion ASC";
		echo query($cadena, "Q");
		break;
	case 'evaluarDoc':
		$idDocAutorizacion=$_GET['id'];
		$resul=$_GET['resul'];

		if ($resul=='A') {
			$cadena="UPDATE DocAutorizacion SET FechaAprob=NOW(), Estado='APROBADO' WHERE idDocAutorizacion=$idDocAutorizacion";
			mysqli_autocommit($db, false);
			$resp=query($cadena, "E", $db);
			if ($resp!='ok') {
				mysqli_rollback($db);
				die($resp);
			}

			$cadena="UPDATE Documentos d
					INNER JOIN DocAutorizacion da ON d.idDocumento=da.idDocumento
					SET d.Publico=1 WHERE da.idDocAutorizacion=$idDocAutorizacion";
			$resp=query($cadena, "E", $db);
			if ($resp!='ok') {
				mysqli_rollback($db);
				die($resp);
			}

			mysqli_commit($db);
		} else {
			$cadena="UPDATE DocAutorizacion SET FechaAprob=NOW(), Estado='RECHAZADO' WHERE idDocAutorizacion=$idDocAutorizacion";
			$resp=query($cadena, "E", $db);
			if ($resp!='ok') {
				die($resp);
			}

		}
		echo $resp;
		
		break;
	case 'reenviarDoc':
		$id=$_GET['id'];
		$idUsuario=$_GET['idUsuario'];
		$Remitente=$_GET['remitente'];

		$cadena="INSERT INTO DocAutorizacion (idDocumento, idEntidad, UsrAutoriza, FechaEmision, Estado, UsrRemitente) (SELECT 
			idDocumento, idEntidad, $idUsuario, NOW(), 'PENDIENTE', $Remitente FROM DocAutorizacion WHERE idDocAutorizacion=$id)";

		$db=GetConnection();

		mysqli_autocommit($db,false);

		$resp=query($cadena, "E", $db);
		if ($resp!='ok') {
			mysqli_rollback($db);
			die($resp);
		}

		$cadena="UPDATE DocAutorizacion SET FechaAprob=NOW(), Estado='REENVIADO' WHERE idDocAutorizacion=$id";
		$resp=query($cadena, "E", $db);
		if ($resp!='ok') {
			mysqli_rollback($db);
			die($resp);
		}

		mysqli_commit($db);

		echo 'ok';
		break;
	case 'enviarLink':
		$idDoc=$_POST["idDoc"];
		$mail=$_POST["mail"];
		$usuario=$_POST["usuario"];
		$venc=$_POST["venc"];
		$cant=$_POST["cant"];
		$observ=$_POST["observ"];

		$cadena="insert into Links (idDocumento, Fecha, FechaVenc, Estado, UsuarioOrigen, UsuarioDestino, Observ, Token, Mail, Descargas) values ($idDoc, now(), '$venc', 'ABIERTA', ".$_SESSION['idUsuario'].", '$usuario', '$observ', MD5(now()), '$mail', $cant)";

		echo query($cadena, "E");

		break;
	case 'datosLink':
		$token=$_GET['token'];

		$cadena="SELECT l.*, u1.nombre remitente, u2.nombre destino, d.Titulo, d.NombreArchivo, d.Link
FROM Links l INNER JOIN Usuarios u1 ON l.UsuarioOrigen=u1.idUsuario
INNER JOIN Documentos d ON l.idDocumento=d.idDocumento 
LEFT JOIN Usuarios u2 ON l.UsuarioDestino=u2.idUsuario where token='$token'";

		echo query($cadena, 'Q');
		break;
	case 'cargarReportes':

		$cadena="select idReporte,'' img, Nombre from Reportes order by Grupo, Nombre";
		$db=GetConnection();

		$r=mysqli_query($db, $cadena);

		if (mysqli_errno($db)>0) {
			echo '[{"Resultado":"Error","Descripcion":"'.mysqli_error($db).'","SQL":"'.$cadena.'"}]';
		}
		else 
			echo toJSON($r);

		break;
	case 'enviarAutorizar':
		$idEntidad=$_GET['idEntidad'];
		$idDoc=$_GET['idDoc'];
		$destino=$_GET['destino'];

		$db=GetConnection();

		$cadena="insert into DocAutorizacion (idDocumento, idEntidad, FechaEmision, Estado, UsrRemitente, UsrAutoriza) values 
        	($idDoc, ".$idEntidad.",now(), 'PENDIENTE', '".$_SESSION['idUsuario']."', ".$destino.")";
			mysqli_query($db,$cadena);
	        if (mysqli_errno($db)>0) {
	            echo '{"Entidad":"'.$_POST['idEntidad'].'","resultado":"1-'.mysqli_error($db).'"}';
	            die();
	        }

	    $cadena="insert into PermisosDoc values ($destino,$idDoc, null, 110)";
			mysqli_query($db,$cadena);
	        if (mysqli_errno($db)>0) {
	            echo '{"Entidad":"'.$idEntidad.'","resultado":"2-'.mysqli_error($db).'"}';
	            die();
	        }	

	    echo 'ok';

	    break;
	case 'cargarDetalleFirma':
		$id=$_GET['id'];
		$cadena="SELECT e.Codigo, e.Nombre Entidad, f.FechaSolicitud, f.Estado, f.FechaDefinicion, f.Observ, fd.Estado EstadoFirma, fd.FechaDefinicion, fd.FechaLimite, fd.idFirmaDetalle, fd.Observ, u1.nombre Remitente, u2.nombre Firmante, fd.Nivel, fd.Tipo, fd.idUsuario idFirmante, f.idEntidad  
			FROM Firmas f
			INNER JOIN FirmaDetalle fd ON f.idFirma=fd.idFirma
			INNER JOIN Usuarios u1 ON f.idUsuario=u1.idUsuario
			INNER JOIN Usuarios u2 ON fd.idUsuario=u2.idUsuario
			INNER JOIN Entidad e ON e.idEntidad=f.idEntidad
			where f.idFirma='$id' 
			order by fd.Nivel, u2.nombre";

		echo query($cadena, 'Q');
		break;
	case 'evaluarFirma':
		$id=$_GET['id'];
		$tipo=$_GET['tipo'];
		$observ=$_GET['observ'];

		$cadena="CALL SP_Firmar($id, '$tipo', '$observ')";

		echo query($cadena, 'E');
		
		break;
	case 'grabarCircuito':
		$idEntidad=$_POST['idEntidad'];
		$idRemitente=$_POST['idRemitente'];
		$observ1=$_POST['observ1'];
		$cadena="insert into Firmas (idEntidad, idUsuario, FechaSolicitud, Observ, Estado) values ($idEntidad, '$idRemitente', now(),'$observ1', 'ABIERTA')";

		$db=GetConnection();
		mysqli_autocommit($db,false);

		mysqli_query($db,$cadena);
        if (mysqli_errno($db)>0) {
        	mysqli_rollback($db);
            echo '{"query":"'.$cadena.'","resultado":"1-'.mysqli_error($db).'"}';
            die();
        }
		$firma=mysqli_insert_id($db);

		$json=json_decode($_POST['detalle'], true);
	//die("bbb");
		$nivel=str_replace('Nivel', '', $json[0]['nivel']);
		$tipo=$json[0]['tipo'];
		$estado="PENDIENTE";
		$str="INSERT INTO FirmaDetalle (idUsuario, idFirma, FechaLimite, Estado, Observ, Nivel, Tipo) VALUES ";
	//die(var_dump($json));
		for ($i=1; $i < sizeof($json); $i++) { 
			$keys=array_keys($json[$i]);

			if ($keys[0]=='nivel') {
				$nivel=str_replace('Nivel', '', $json[$i]['nivel']);
				$tipo=$json[$i]['tipo'];
				$estado='A LA ESPERA';
			} else {
				$str.="(".$json[$i]['firmante'].",".$firma.", '".$json[$i]['limite']."','$estado', '', $nivel, $tipo),";
			}
		}
		$str=trim($str,',');

		mysqli_query($db,$str);
        if (mysqli_errno($db)>0) {
        	mysqli_rollback($db);
            echo '{"query":"'.$str.'","resultado":"1-'.mysqli_error($db).'"}';
            die();
        }

        mysqli_commit($db);

		echo 'ok';
		break;
	case 'eliminarCircuito':
		$id=$_GET['id'];

		$cadena="delete from Firmas where idFirma=$id";

		echo query($cadena, 'E');
		break;
}


