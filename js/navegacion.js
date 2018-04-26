function Consulta(query) {
		
		oAjax.request="customQuery&query="+query+"&tipo=Q";
		oAjax.async=false;
		oAjax.send(respConsulta);

		function respConsulta(data) {
			return data.responseText;
		}
	}	
function RecordCount(query) {
	if (query=='') {
		return false;
	}

	var r=Consulta(query);
	j=JSON.parse(r)
	return j[0].cant;
}
$(document).ready(function () {

    
    function exportTableToCSV($table, filename) {
        var $headers = $table.find('tr:has(th)')
            ,$rows = $table.find('tr:has(td)')

            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            ,tmpColDelim = String.fromCharCode(11) // vertical tab character
            ,tmpRowDelim = String.fromCharCode(0) // null character

            // actual delimiter characters for CSV format
            ,colDelim = '";"'
            ,rowDelim = '"\r\n"';

            // Grab text from table into CSV formatted string
            var csv = '"';
            csv += formatRows($headers.map(grabRow));
            csv += rowDelim;
            csv += formatRows($rows.map(grabRow)) + '"';

            // Data URI
            var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

        // For IE (tested 10+)
        if (window.navigator.msSaveOrOpenBlob) {
            var blob = new Blob([decodeURIComponent(encodeURI(csv))], {
                type: "text/csv;charset=utf-8;"
            });
            navigator.msSaveBlob(blob, filename);
        } else {
            $(this)
                .attr({
                    'download': filename
                    ,'href': csvData
                    //,'target' : '_blank' //if you want it to open in a new window
            });
        }

        //------------------------------------------------------------
        // Helper Functions 
        //------------------------------------------------------------
        // Format the output so it has the appropriate delimiters
        function formatRows(rows){
            return rows.get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim);
        }
        // Grab and format a row from the table
        function grabRow(i,row){
             
            var $row = $(row);
            //for some reason $cols = $row.find('td') || $row.find('th') won't work...
            var $cols = $row.find('td'); 
            if(!$cols.length) $cols = $row.find('th');  

            return $cols.map(grabCol)
                        .get().join(tmpColDelim);
        }
        // Grab and format a column from the table 
        function grabCol(j,col){
            var $col = $(col),
                $text = $col.text();

            return $text.replace('"', '""'); // escape double quotes

        }
    }


    // This must be a hyperlink
    $("#botonExcel").click(function (event) {
        // var outputFile = 'export'
        exportTable();
        var outputFile = window.prompt("What do you want to name your output file") || 'export';
        outputFile = outputFile.replace('.csv','') + '.csv'
         
        // CSV
        exportTableToCSV.apply(this, [$('#tmpDiv > table'), outputFile]);
        
        // IF CSV, don't do event.preventDefault() or return false
        // We actually need this to be a typical hyperlink
    });
});
		

		
function navigate(to) {
	if (to=='atras') {
		oCurrentReg.current-=oMaxReg.maxcant;
		if (oCurrentReg.current<0) oCurrentReg.current=0;
		indexMin=oCurrentReg.current;
		indexMax=oMaxReg.maxcant;
	}

	if (to=='sig') {
		oCurrentReg.current+=oMaxReg.maxcant;
		//if (oCurrentReg.current>oR.recordSet.length) oCurrentReg.current=oR.recordSet.length;
		indexMin=oCurrentReg.current;
		indexMax=oMaxReg.maxcant;

	}

	var btnAnt=document.getElementById("btnAnt");
	var btnSig=document.getElementById("btnSig");
	var cantFinal = 1*(oCurrentReg.current+oMaxReg.maxcant);

/*	if ((oCurrentReg.current+oMaxReg.maxcant)>oCantReg.cant) {
		btnSig.style.display="none";
		cantFinal=oCantReg.cant;
	}
	else {
		btnSig.style.display="";
		
	}*/

	if (oCurrentReg.current==0) 
		btnAnt.style.display="none";
	else 
		btnAnt.style.display="";

	var q=oQuery.query + " limit "+indexMin+","+indexMax;
	oAjax.server="ajaxfunciones.php?consulta=";
	oAjax.request="customQuery&query="+q+"&tipo=Q";
	oAjax.send(respNavigate);

	function respNavigate(data) {
		espera('off');
		if (data.responseText=="") {
			return false;
		}
		var obj=JSON.parse(data.responseText);

		JsonToTable(obj,'tblReporte', true);

		if(obj.length<oMaxReg.maxcant) {
			toggle('btnSig', 'visible', false);
		} else {
			toggle('btnSig', 'visible', true);
		}
	}
/*	var tbl=document.getElementById("tbodyLista");
	tbl.innerHTML="";
	document.getElementById("spanCant").innerHTML="Registros "+oCurrentReg.current+" al "+cantFinal;
    	for (var i = oCurrentReg.current; i < (oCurrentReg.current+oMaxReg.maxcant); i++) {
    		var tr=tbl.insertRow(-1);
    		var cell=[];
    		cell[0]=tr.insertCell(0);
    		cell[0].innerHTML=obj[i].ID;
    		cell[1]=tr.insertCell(1);
    		cell[1].innerHTML=obj[i].Fecha;
    		cell[2]=tr.insertCell(2);
    		cell[2].innerHTML=obj[i].Tipo;
    		cell[3]=tr.insertCell(3);
    		cell[3].innerHTML=obj[i].Usuario;
    		cell[4]=tr.insertCell(4);
    		cell[4].innerHTML=obj[i].Entorno;
    		cell[5]=tr.insertCell(5);
    		cell[5].innerHTML=obj[i].Serie;
    		cell[6]=tr.insertCell(6);
    		cell[6].innerHTML=obj[i].Remito;
    		cell[7]=tr.insertCell(7);
    		cell[7].innerHTML=obj[i].Hoja;
    		cell[8]=tr.insertCell(8);
    		cell[8].innerHTML=obj[i].Estado;

/*	            		for (var j = 4; j < 10; j++) {
    			cell[j].style.textAlign="right";

    		}
    	}	*/	        	

}
function exportTable() {
	var obj=oR.recordSet;
	var tabla=document.getElementById("tmpTable");
	

	var tbl=document.getElementById("tmpBody");
	tbl.innerHTML="";
	
	for (var i = 0; i < obj.length; i++) {
		var tr=tbl.insertRow(-1);
		var cell=[];
		cell[0]=tr.insertCell(0);
		cell[0].innerHTML=obj[i].ID;
		cell[1]=tr.insertCell(1);
		cell[1].innerHTML=obj[i].Fecha;
		cell[2]=tr.insertCell(2);
		cell[2].innerHTML=obj[i].Tipo;
		cell[3]=tr.insertCell(3);
		cell[3].innerHTML=obj[i].Usuario;
		cell[4]=tr.insertCell(4);
		cell[4].innerHTML=obj[i].Entorno;
		cell[5]=tr.insertCell(5);
		cell[5].innerHTML=obj[i].Serie;
		cell[6]=tr.insertCell(6);
		cell[6].innerHTML=obj[i].Remito;
		cell[7]=tr.insertCell(7);
		cell[7].innerHTML=obj[i].Hoja;
		cell[8]=tr.insertCell(8);
		cell[8].innerHTML=obj[i].Estado;

/*	            		for (var j = 4; j < 10; j++) {
			cell[j].style.textAlign="right";

		}*/
	}	


}		
function loadUsers() {
	espera('on');
	var xmlhttp = new XMLHttpRequest();
	//var xmlhttp =new ActiveXObject("Microsoft.XMLHTTP");
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var resp=xmlhttp.responseXML;
        	//alert(resp);
        	var str = resp.getElementsByTagName("string")[0].childNodes[0].nodeValue;
        	obj=JSON.parse(str);

			tbl=document.getElementById("tbBodyUsers");
			tbl.innerHTML='';

			var cantres=obj.length;

        	for (var i = 0; i < cantres; i++) {
        		var tr=tbl.insertRow(-1);
        		var cell=[];
        		cell[0]=tr.insertCell(0);
        		cell[0].innerHTML=obj[i].FILIAL;
        		cell[1]=tr.insertCell(1);
        		cell[1].innerHTML='<a href="#" class="linkUser" onclick="mostrarUser(\''+obj[i].USUARIO+'\')">'+obj[i].USUARIO+'</a>';
        		cell[2]=tr.insertCell(2);
        		cell[2].innerHTML=obj[i].NOMBRE;
        		/*cell[3]=tr.insertCell(3);
        		cell[3].innerHTML=obj[i].PASS;*/
        		cell[3]=tr.insertCell(3);
        		cell[3].innerHTML=obj[i].ACTIVO;
        		cell[4]=tr.insertCell(4);
        		cell[4].innerHTML=obj[i].ALTA;
        		cell[5]=tr.insertCell(5);
        		cell[5].innerHTML=obj[i].TIPO;
        		cell[6]=tr.insertCell(6);
        		cell[6].innerHTML=obj[i].MAIL;

/*	            		for (var j = 4; j < 10; j++) {
        			cell[j].style.textAlign="right";

        		}*/
        	}		        	
        	espera('off');
        }
    }
	//xmlhttp.open("GET","http://metro.fidia.com.ar:8310/ws_fidia/seguridad.asmx/ListaUsuarios?user=*",true);
	xmlhttp.open("GET",oURL.url+"seguridad.asmx/LeerUsuarios?user=",true);
	xmlhttp.send();			
}
function mostrarUser(user) {
	var div=document.getElementById("divLoading");
	var msg=document.getElementById("divUserInfo");
	div.style.display="";
	msg.style.display="";

	
	var xmlhttp = new XMLHttpRequest();
	//var xmlhttp =new ActiveXObject("Microsoft.XMLHTTP");
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var resp=xmlhttp.responseXML;
        	//alert(resp);
        	var str = resp.getElementsByTagName("string")[0].childNodes[0].nodeValue;
        	obj=JSON.parse(str);

        	document.getElementById("txtUsuario").value=obj[0].USUARIO;
        	document.getElementById("txtNombre").value=obj[0].NOMBRE;
        	document.getElementById("txtPass1").value=obj[0].PASS;
        	document.getElementById("txtPass2").value=obj[0].PASS;
        	AsignarCombo('cboActivo', obj[0].ACTIVO);
        	AsignarCombo('cboTipo', obj[0].TIPO);
        	document.getElementById("txtMail").value=obj[0].MAIL;
        	document.getElementById("txtModo").value="EDIT";


        }
    }
	//xmlhttp.open("GET","http://metro.fidia.com.ar:8310/ws_fidia/seguridad.asmx/ListaUsuarios?user=*",true);
	xmlhttp.open("GET",oURL.url+"seguridad.asmx/LeerUsuarios?user="+user,true);
	xmlhttp.send();

}	
function cerrarVentana() {
	var div=document.getElementById("divLoading");
	var msg=document.getElementById("divUserInfo");
	div.style.display="none";
	msg.style.display="none";

}

function guardarUsuario() {
	var modo=document.getElementById("txtModo").value;
	var user=document.getElementById("txtUsuario");
	var nombre=document.getElementById("txtNombre");
	var pass=document.getElementById("txtPass1");
	var pass2=document.getElementById("txtPass2");
	var mail=document.getElementById("txtMail");
	var cboActivo=document.getElementById("cboActivo");
	var cboTipo=document.getElementById("cboTipo");
	var activo=cboActivo.options[cboActivo.selectedIndex].value;
	var perfil=cboTipo.options[cboTipo.selectedIndex].value;

	if (pass!=pass2) 
	var xmlhttp = new XMLHttpRequest();
	//var xmlhttp =new ActiveXObject("Microsoft.XMLHTTP");
	espera('on');
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var resp=xmlhttp.responseXML;
        	//alert(resp);
        	var str = resp.getElementsByTagName("string")[0].childNodes[0].nodeValue;
        	if (str=='ok') {
        		if (modo!='EDIT') {
        			cambiarPass(user, pass);
        		}
        		alert('Usuario actualizado correctamente');
        		loadUsers();
        		cerrarVentana();
        	} else {
        		alert("Error guardando datos: "+str);
        	}



        	espera('off');
        }
    }
	//xmlhttp.open("GET","http://metro.fidia.com.ar:8310/ws_fidia/seguridad.asmx/ListaUsuarios?user=*",true);
	xmlhttp.open("GET",oURL.url+"seguridad.asmx/GuardarUsuario?user="+user.value+"&nombre="+nombre.value+"&pass="+pass.value+"&activo="+activo+"&perfil="+perfil+"&mail="+mail.value,true);
	xmlhttp.send();			
}
function cambiarPass(user, pass) {
	var xmlhttp = new XMLHttpRequest();
	//var xmlhttp =new ActiveXObject("Microsoft.XMLHTTP");
	
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var resp=xmlhttp.responseXML;
        	//alert(resp);
        	var str = resp.getElementsByTagName("string")[0].childNodes[0].nodeValue;
        	if (str!='ok') {
        		alert("Error guardando password: "+str);
        	}
	    }
    }
	//xmlhttp.open("GET","http://metro.fidia.com.ar:8310/ws_fidia/seguridad.asmx/ListaUsuarios?user=*",true);
	xmlhttp.open("GET",oURL.url+"seguridad.asmx/CambiaPassword?user="+user+"&pass="+pass,false);
	xmlhttp.send();				
}		
function newUser() {
	var div=document.getElementById("divLoading");
	var msg=document.getElementById("divUserInfo");
	div.style.display="";
	msg.style.display="";	
	

	document.getElementById("txtUsuario").value="";
	document.getElementById("txtNombre").value="";
	document.getElementById("txtPass1").value="";
	document.getElementById("txtPass2").value="";
	document.getElementById("txtPass1").disabled=false;
	document.getElementById("txtPass2").disabled=false;
	AsignarCombo('cboActivo', 'S');
	document.getElementById("txtMail").value="";
	document.getElementById("txtModo").value="NEW";

}
function DeleteUser() 		 {
	if (!confirm("Confirma eliminar el usuario seleccionado?")) return false;

	var user=document.getElementById("txtUsuario").value;
	var xmlhttp = new XMLHttpRequest();
	//var xmlhttp =new ActiveXObject("Microsoft.XMLHTTP");
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        	var resp=xmlhttp.responseXML;
        	//alert(resp);
        	var str = resp.getElementsByTagName("string")[0].childNodes[0].nodeValue;
        	if (str=='ok') {
        		alert("Usuario eliminado");
        		loadUsers();
        	}
        	else {
        		alert("Error eliminando usuario: "+str);
        	}
        }
    }
	//xmlhttp.open("GET","http://metro.fidia.com.ar:8310/ws_fidia/seguridad.asmx/ListaUsuarios?user=*",true);
	xmlhttp.open("GET",oURL.url+"seguridad.asmx/BorrarUsuario?user="+user,true);
	xmlhttp.send();		
}
function clsURL() {
	//this.url="http://172.16.36.33:8087/";
	this.url="http://localhost:32968/";
}
function clsMaxReg() {
	this.maxcant=10;
}
function clsCantReg() {
	this.cant=0;
}
function clsCurrentReg() {
	this.current=0;
}
function clsRecordSet() {
	this.recordSet="";
}
function clsQuery() {
	this.query="";
}

var oURL=new clsURL();
var oMaxReg=new clsMaxReg();
var oCantReg=new clsCantReg();
var oCurrentReg=new clsCurrentReg();
var oQuery=new clsQuery();
var indexMin=0;
var indexMax=indexMin+oMaxReg.maxcant;
var oR=new clsRecordSet();
