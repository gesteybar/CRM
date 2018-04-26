<?
	function GetConnection() {
		$dbi=mysqli_connect('172.16.38.243', 'esmu', 'Arimex2016$*', 'esmu_dev');
			if (!$dbi)
			  die('No se puede conectar a la base de datos');

			mysqli_query($dbi,"SET NAMES 'utf8'"); 
			
			return $dbi;
	}
	
	function GetConnectionSQL($server) {
		switch ($server) {
			case 'AX':
				$cadena="Driver={SQL Server Native Client 10.0};Server=127.0.0.1;Database=crmdev;";
				$dbi=odbc_connect($cadena, 'sa', 'Pi=3.1415927');
				break;
			
			default:
				
				break;
		}
		return $dbi;
	}
	
?>