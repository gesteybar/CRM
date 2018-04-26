//********************************************************************************************************
//AJAX
//********************************************************************************************************
function clsFrameAjax() {
	this.responseJSON=function(oxhr) {
		var str = oxhr.responseXML.getElementsByTagName("string")[0].childNodes[0].nodeValue;
		return JSON.parse(str);
	}
	this.responseText=null;
	this.request="";
	this.async=true;
	this.server="http://localhost:8797/CRM.asmx/";
	//this.server="ajaxfunciones.php?consulta=";


	var xhr= new XMLHttpRequest();

	this.send=function(func) {
		init();
		if (this.request!='') {
			xhr.onreadystatechange=function () {
				if (xhr.readyState==4 && xhr.status==200) {
					xhr.responseText=xhr.responseText.replace(/\t/g, '');
					func(xhr);
				}
			}
			/*this.request=this.request.replace('||minus||', '-');
			this.request=this.request.replace('||plus||', '+');*/

			xhr.open("GET",this.server+this.request,this.async);
			xhr.send();
		}
	}
	this.sendPost=function(func) {
		init();
		if (this.request!='') {
			xhr.onreadystatechange=function () {
				if (xhr.readyState==4 && xhr.status==200) {
					func(xhr);
				}
			}
			xhr.open("POST", this.server,this.async);
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhr.setRequestHeader("Access-Control-Allow-Origin", "*");
			/*this.request=this.request.replace('||minus||', '-');
			this.request=this.request.replace('||plus||', '+');*/

			this.request=this.request.replace(/\t/g, ' ');
			xhr.send(this.request);		

		}
	}
	this.abort=function() {
	xhr.abort();
	}
	function init() {
		responseJSON=null;
		responseText=null;
		request="";
		async=true;
		server="http://localhost:8797/CRM.asmx/";
		//server="ajaxfunciones.php?consulta=";
	}
}

var oAjax=new clsFrameAjax;

//********************************************************************************************************
//Objeto usuario
//********************************************************************************************************
//Conserva las credenciales de acceso
function clsUser() {
	this.usuario='';
	this.pass='';
}
var oUser = new clsUser();


function setValue(ctrl, valor, dec) {
	var control=document.getElementById(ctrl);
	if (control.type=="text" || control.type=="password" || control.type=="textarea" || control.type=="date" || control.type=="hidden")
		{control.value=valor;return true;}
	if (control.type=="select-one") 
		{setComboItem(ctrl, valor);return true;}
	if (control.type=="number")
		if (dec==null || dec=='' || dec==undefined)
			control.value=valor;
		else
			control.value=Number(valor).toFixed(dec);
		
	control.innerHTML=valor;
	return true;


}
function getValue(ctrl) {
	var control=document.getElementById(ctrl);
	if (control.type=="text" || control.type=="password" || control.type=="textarea" || control.type=="date" || control.type=="hidden")
		return control.value;
	if (control.type=="select-one") 
		return valorCombo(ctrl);
	if (control.type=="number")
		return control.value;

	return control.innerHTML;

}
//********************************************************************************************************
//Funciones de llenado de tablas
//********************************************************************************************************
//Esta fc recibe un objeto json y una tabla que debe existir en el html. Llena la tabla con 
var    _tr_ = document.createElement('tr'),
    _th_ = document.createElement('th'),
    _td_ = document.createElement('td');
// Builds the HTML Table out of myList json data from Ivy restful service.
function JsonToTable(arr, tabla, headers) {
 	//var _table_ = document.createElement(tabla)
 	var _table_=document.getElementById(tabla);
	var table = _table_;//.cloneNode(false),
	table.innerHTML="";
	columns = addAllColumnHeaders(arr, table, headers);

	for (var i=0, maxi=arr.length; i < maxi; ++i) {
		var tr = _tr_.cloneNode(false);
		for (var j=0, maxj=columns.length; j < maxj ; ++j) {
			var td = _td_.cloneNode(false);
			cellValue = arr[i][columns[j]];
			td.appendChild(document.createTextNode(arr[i][columns[j]] || ''));
			tr.appendChild(td);
		}
		table.appendChild(tr);
	}
	return table;
 }
 
 // Adds a header row to the table and returns the set of columns.
 // Need to do union of keys from all records as some records may not contain
 // all records
 function addAllColumnHeaders(arr, table, headers) {
    var columnSet = []
	var tr = _tr_.cloneNode(false);         
	for (var i=0, l=arr.length; i < l; i++) {
		
	 for (var key in arr[i]) {
	     if (arr[i].hasOwnProperty(key) && columnSet.indexOf(key)===-1) {
	         columnSet.push(key);
	         if (headers) {
	         	 
	             var th = _th_.cloneNode(false);
	             th.appendChild(document.createTextNode(key));
	             tr.appendChild(th);
	         }
	     }
	 }
	 if (headers)
	 	table.appendChild(tr);
	}

 return columnSet;
 }

function AgregarBotonBorrarTabla(tabla, col, imagen, funcion, refcol,prefijo, clase,condicion, colcond, title) {
	AgregarBotonTabla(tabla, col, imagen, funcion, refcol,prefijo, clase,condicion, colcond, title);
}

function AgregarBotonTabla(tabla, col, imagen, funcion, refcol,prefijo, clase,condicion, colcond, title) {
	var tbl=document.getElementById(tabla);
	var tr=tbl.getElementsByTagName("tr");

	if (title== undefined) title='';
	
	if (refcol==="" || refcol==undefined || refcol==null)
		refcol=col;

	if (colcond==undefined || colcond=='') colcond=0;

	if (clase!='')
		var addClass="class='"+clase+"'";
	else
		var addClass='';
	
	for (var i = 0; i < tr.length; i++) {
		if (col<0) {
			for (var j = 0; j < tr[i].cells.length; j++) {
				var trtmp=tr[i];
				trtmp='<tr class="trClickable" onclick="'+funcion+'(\''+tr[i].cells[refcol].innerText+'\', this);">'+tr[i].innerHTML+'</tr>';
				//tr[i].cells[j].innerHTML ='<a '+addClass+' href="javascript:void(0)" onclick="'+funcion+'(\''+tr[i].cells[refcol].innerText+'\', this);">'+tr[i].cells[j].innerHTML+'</a>';
				tr[i].outerHTML=trtmp;
			}
		} else {
			var td=tr[i].cells;
			if (td.length>0)
				if (td[colcond].innerText==condicion || condicion=='' || condicion==undefined) {
					if (imagen!='')
						if (prefijo)
							td[col].innerHTML='<a '+addClass+' href="javascript:void(0)" onclick="'+funcion+'(\''+td[refcol].innerText+'\', this);"><img src="./imagenes/'+imagen+'" width="16" title="'+title+'"></a>'+td[col].innerHTML;	
						else
							td[col].innerHTML+='<a '+addClass+' href="javascript:void(0)" onclick="'+funcion+'(\''+td[refcol].innerText+'\', this);"><img src="./imagenes/'+imagen+'" width="16" title="'+title+'"></a>';
					else
						td[col].innerHTML ='<a '+addClass+' href="javascript:void(0)" onclick="'+funcion+'(\''+td[refcol].innerText+'\', this);">'+td[col].innerHTML+'</a>';
				}
		}
	}
}

function AgruparTabla (tabla, col, firstRow, condicion, colCond) {
	var tbl=document.getElementById(tabla);
	var tr=tbl.getElementsByTagName("tr");

	if (firstRow==undefined || firstRow==null || firstRow=='') {
		firstRow=0;
	}

	if (colCond==undefined || colCond=='') colCond=0;

	var ant="";
	for (var i = firstRow; i < tr.length; i++) {
		if (ant!=tr[i].cells[col].innerText) {
			ant=tr[i].cells[col].innerText;
			
			var th=document.createElement('th');
			th.colSpan=tbl.rows[0].cells.length;
			th.innerText=tr[i].cells[col].innerText;
			var trg=tbl.insertRow(i);
			trg.appendChild(th);
		}
	}

}

function AgregarFila(tabla, enFila, idTr, params) {
	if (enFila=='') enFila=-1;
	var tbl=document.getElementById(tabla);
	var tr=tbl.insertRow(enFila);
	tr.id="tr"+idTr;
	for (var i = 0; i < params.length; i++) {
		var td=tr.insertCell(i);
		td.innerHTML=params[i];
	}
	if (enFila==-1) {
		return tbl.getElementsByTagName('tr').length;
	} else {
		return enFila;
	}
}

function AgregarColumna(tabla) {
	//if (enCol=='') enCol=Filas(tabla);

	var myform=$("#"+tabla);
	var iter=0;

	myform.find('tr').each(function(){
		var trow = $(this);
		if(trow.index() >= 0){
		 trow.append('<td></td>');
		}
	});
	iter += 1;
}

function OcultarColumnaTabla(tabla, columna) {
	var tbl=document.getElementById(tabla);
	var tr=tbl.getElementsByTagName("tr");

	for (var i = 0; i < tr.length; i++) {
		var td=tr[i].cells;
		if (td.length>0 )
			td[columna].style.display='none';
	}

}

function AgregarEstiloTabla(tabla, row, col, estilo, clase, condicion, colcond) {
	//valores row:-1 todas las filas | -2 fila por medio | N* todas las filas a partir de N | *N todas hasta N | N fila unica | N-N desde hasta fila
	//valores col:-1 todas las columnas | -2 col por medio | N* todas las col a partir de N | *N todas hasta N | N col unica | N-N desde hasta col

	var tbl=document.getElementById(tabla);
	var tr=tbl.getElementsByTagName("tr");

	var dRow=0;var hRow=0;var dCol=0;var hCol=0;var rowInter=false;var colInter=false;
	switch (row) {
		case '-1': dRow=1;hRow=tr.length;break;
		case '-2': dRow=1;hRow=tr.length;rowInter=true; break;
		default: 
				if (row.indexOf('*')==0) {dRow=1;hRow=row.replace('*','');}
				if (row.indexOf('*')>0) {hRow=tr.length;dRow=row.replace('*','');}
				if (row.indexOf('-')>0) {var vec=row.split('-');dRow=vec[0];hRow=vec[1];}
				if (!isNaN(row)) {dRow=row;hRow=row;}

	}

	switch (col) {
		case '-1': dCol=0;hCol=tr[1].cells.length-1;break;
		case '-2': dCol=0;hCol=tr[1].cells.length-1;colInter=true; break;
		default: 
				if (col.indexOf('*')==0) {dCol=0;hCol=col.replace('*','');}
				if (col.indexOf('*')>0) {hCol=tr[1].cells.length-1;dCol=col.replace('*','');}
				if (col.indexOf('-')>0) {var vec=col.split('-');dCol=vec[0];hCol=vec[1];}
				if (!isNaN(col)) {dCol=col;hCol=col;}

	}

	if (colcond=='' || colcond== undefined) colcond=0;

	for (var i = dRow; i < hRow; i++) {
		if (!rowInter || (i % 2 == 0)) {
			var td=tr[i].cells;
			for (var j = dCol; j <= hCol; j++) {
				if (!colInter || (j % 2 != 0)) {
					if (td[colcond].innerHTML==condicion || condicion=='' || condicion==undefined) {
						if (estilo!='') {
							td[j].setAttribute("style", estilo);
						}
						td[j].className+=clase;
					}
				}
			}
		}
			
	}
	
}

function convertirAControl(tabla, fila, celda, tipoControl, className, style) {
	if (fila==undefined || fila==-1 || fila==null || fila=='') {
		var inicio=0; var fin=Filas(tabla);
	} else {
		var inicio=fila; var fin =fila+1;
	}

	for (var i = inicio; i < fin; i++) {
		var tr=document.getElementById(tabla).getElementsByTagName('tr')[i];
		var td=tr.cells[celda];
		switch (tipoControl) {
			case 'text':
				var o=document.createElement('input');o.type="text";o.id="ctr"+tipoControl+i;o.value=td.innerText;o.addClass(className);
				break;
			case 'checkbox':
				var o=document.createElement('input');o.type="checkbox";o.id="ctr"+tipoControl+i;
				if (td.innerText=='1') o.checked=true; else o.checked=false;
				o.value=td.innerText;
				break;

		}
		td.innerHTML="";
		td.appendChild(o);
		
	}
}

function Filas(tabla) {
	var tr=document.getElementById(tabla).getElementsByTagName('tr');
	return tr.length;
}

function infoFila(tabla, col, id) {
	var tr=document.getElementById(tabla).getElementsByTagName('tr');
	for (var i = 0; i < tr.length; i++) {
		if (tr[i].cells[col].innerText==id) {
			return tr[i];
		}
	}
}

function TableToJson(table, titulos) {
    var data = [];
    if (titulos) {
    	var thead=table.getElementsByTagName('thead')[0];
	    // first row needs to be headers
	    var headers = [];
	    var inicio=1;
	    for (var i=0; i<thead.rows[0].cells.length; i++) {
	        headers[i] = thead.rows[0].cells[i].innerHTML.toLowerCase().replace(/ /gi,'');
	    }
	} else {
		var headers=[];
		var inicio=0;
		for (var i = 0; i < table.rows[0].cells.length; i++) {
			headers[i]="dato"+i;
		}
	}

    // go through cells
    for (var i=inicio; i<table.rows.length; i++) {

        var tableRow = table.rows[i];
        var rowData = {};

        for (var j=0; j<tableRow.cells.length; j++) {
        	var obj=tableRow.cells[j].childNodes[0];
        	rowData[ headers[j] ] = tableRow.cells[j].innerHTML;
        	if (obj.tagName=="INPUT") {
        		if (obj.type=='text' || obj.type=='number' || obj.type=='date' || obj.type=='mail')
        			rowData[ headers[j] ] = obj.value;
        		if (obj.type=='checkbox')
        			rowData[ headers[j] ] = (obj.checked ? '1' : '0');
        	}
        	if (obj.tagName=="A") {
            	rowData[ headers[j] ] = obj.parentNode.innerText;
        	}
        	if (obj.tagName=="SELECT") {
            	rowData[ headers[j] ] = getValue(obj.id);
        	}

        }

        data.push(rowData);
    }       

    return data;
}

//********************************************************************************************************
//Funciones de llenado de combos
//********************************************************************************************************
//Esta Fc recibe un nombre de combo y una query y lo llena con el resultado
function LlenarComboSQL(combo, query, blanco) {
	var cbo=document.getElementById(combo);

	oAjax.request="CustomQuery?Cadena="+query+"&Tipo=Q";
	oAjax.send(resp);

	function resp(data) {
    	
    	
    	obj=JSON.parse(data.responseText);
    	cbo.innerHTML='';
    	if (obj.length==0) {
    		alert("Error en datos: "+obj.respuesta);
    	}
    	else {
    		JsonToCombo(obj, combo, blanco);
    	}
		
	}
}
function valorCombo(combo) {
  var cbo=document.getElementById(combo);
  if (cbo.selectedIndex==-1)
  	return '';
  
  var valor=cbo.options[cbo.selectedIndex].value;
  return valor;
}
function textoCombo(combo) {
  var cbo=document.getElementById(combo);
  if (cbo.selectedIndex==-1)
  	return '';
  
  var valor=cbo.options[cbo.selectedIndex].text;
  return valor;
}
//Esta fc recibe un objeto json y un combo. Se llena en base a campos de json
function JsonToCombo(j, cbo, blanco) {
	var combo=document.getElementById(cbo);
	
	combo.options.length=0;
	if (blanco) {
		var op = document.createElement("option");
		
		op.value="";
		op.text="";
		combo.options.add(op);
	}

	for (var i=0; i < j.length; i++) {
		var keys=Object.keys(j[i]);
		var op = document.createElement("option");
		
		var desc="";
		for (var l = 1; l < keys.length; l++) {
			desc+=j[i][keys[l]];
			if (l<keys.length-1) 
				desc+=" - ";
		}

		op.value=j[i][keys[0]];
		op.text=desc;

		combo.options.add(op);
    }
}
function setComboItem(cbo,valor) {
	var o=document.getElementById(cbo);
	for (var i = 0; i < o.options.length; i++) {
		if (o.options[i].value==valor) {
			o.selectedIndex=i;
		}
	}
	return false;
}
function toggle(control, tipo, estado) {
	var o=document.getElementById(control);
	switch (tipo) {
		case 'visible':

			if (estado==null)
				if (o.style.display=="")
					o.style.display="none";
				else
					o.style.display="";
			else
				if (estado) 
					o.style.display="";
				else 
					o.style.display="none";

			break;
		case 'disabled':
			if (estado==null)
				if (o.disabled)
					o.disabled=false;
				else
					o.disabled=true;
			else
				if (estado) 
					o.disabled=false;
				else 
					o.disabled=true;

			break;
		case 'enabled':
			if (estado==null)
				o.disabled=!o.disabled;
			else
				o.disabled=!estado;
			break;
		case 'table-row':

			if (estado==null)
				if (o.style.display=="table-row")
					o.style.display="none";
				else
					o.style.display="table-row";
			else
				if (estado) 
					o.style.display="table-row";
				else 
					o.style.display="none";

			break;

	}
}

function TableToJSON(idTabla, rowEncab) {
	var tbl=document.getElementById(idTabla);
	if (rowEncab=='' || rowEncab==null) rowEncab=0;
	var th=tbl.rows[rowEncab].cells;
	var tr=tbl.rows;

	/*for (var i = 0; i < th.length; i++) {
		th[i]=tbl.rows[rowEncab].cells[i].innerText;
	}*/

	var cadena='[';

	for (var i = rowEncab+1; i < tr.length; i++) {
		cadena+='{';
		for (var j = 0; j < th.length; j++) {
			cadena+='"'+th[j].innerText+'":"'+tr[i].cells[j].innerText+'"';
			if (j+1<th.length) cadena+=',';
		}
		cadena+='}'
		if (i+1<tr.length) cadena+=',';
	}
	cadena+=']';
	return cadena;
}

function espera(estado) {
	var div=document.getElementById("divLoading");
	var msg=document.getElementById("divMensaje");
	if (estado=='on') {
		div.style.display="";
		msg.style.display="";
	}
	else {
		div.style.display="none";
		msg.style.display="none";
	}
}

function printdiv(printpage, titulo)
{
	var headstr = '<html><head><title>'+titulo+'</title><link rel="stylesheet" type="text/css" href="../css/gral.css"><link rel="stylesheet" type="text/css" href="../css/sc.css"></head><body>';
	var footstr = "</body>";
	var newstr = document.all.item(printpage).innerHTML;
	var oldstr = document.body.innerHTML;
	document.body.innerHTML = headstr+newstr+footstr;
	window.print();
	document.body.innerHTML = oldstr;
	return false;
}
function fileName(fullPath) {
	
	if (fullPath) {
	    var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
	    var filename = fullPath.substring(startIndex);
	    if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
	        filename = filename.substring(1);
	    }
	    return filename;
	}
}
function BuscarDato(consulta) {
	//oAjax.server="ajaxfunciones.php?consulta=";
	oAjax.request="customQuery&query="+consulta+"&tipo=Q";
	oAjax.async=false;
	var resp="";
	oAjax.send(respBuscar);

	
	function respBuscar(data) {
		if (data.responseText.length<3) {
			resp= "";
			return false;
		}

		var obj=JSON.parse(data.responseText);
		for (var key in obj[0]) {
			resp= obj[0][key];
			return false;
		}
	}
	return resp;

}
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
function JsonParser(str) {
	str=str.replace(/\t/g, '');
	str=str.replace(/\&/g, '');
	//str=str.replace(/\"/g, '´´');
	//str=str.replace(/\'/g, "´");

	return JSON.parse(str);
}


function ocultarColumnas(tabla, usuario, vista, offSet) {
	var tbl=document.getElementById(tabla);
	if (offSet==null || offSet==undefined || offSet=="") offSet=0;

	var c=BuscarDato("SELECT GROUP_CONCAT(Campo) campos FROM UserParam WHERE Vista='"+vista+"' AND idUsuario='"+usuario+"'");
	if (c==null || c=='') return false;
	var campos=c.split(',');

	for (var i = offSet; i < tbl.rows[0].cells.length; i++) {
		var th=tbl.rows[0].cells[i];
		if (th.tagName=='TH') {
			
			if (arraySearch(th.innerText)==undefined) {
				OcultarColumnaTabla(tabla, i);
			}

		}
	}

	function arraySearch(campo) {
		return campos.find(function(dato) {return campo.trim()==dato.trim()});

	}
}

function cargarColumnas(desde, hasta, fila, offSet, vista, usuario, funcion) {
	var tbl1=document.getElementById(desde);
	var tbl2=document.getElementById(hasta);

	if (fila=='' || fila==undefined) fila=0;
	if (offSet=='' || offSet==undefined) offSet=0;

	for (var i = offSet; i < tbl1.rows[fila].cells.length; i++) {
		var tr=tbl2.insertRow()
		var td1=tr.insertCell(0);
		var td2=tr.insertCell(1);

		td1.innerText=tbl1.rows[fila].cells[i].innerText;
		td2.innerHTML='<a href="javascript: void(0);" onclick="'+funcion+'(this)"><img src="./imagenes/off.png"></a>';
	}


	var c=BuscarDato("SELECT GROUP_CONCAT(Campo) campos FROM UserParam WHERE Vista='"+vista+"' AND idUsuario='"+usuario+"'");
	if (c==null || c=='') return false;
	var campos=c.split(',');

	for (var i = 0; i < tbl2.rows.length; i++) {
		var th=tbl2.rows[i].cells[0];
			
		if (arraySearch(th.innerText)!=undefined) {
			tbl2.rows[i].cells[1].childNodes[0].childNodes[0].src="./imagenes/on.png";
		}

	}

	function arraySearch(campo) {
		return campos.find(function(dato) {return campo.trim()==dato.trim()});

	}


}

function showPopup (obj, x, y, style, classname, owner) {
/*----------------------------DESCRIPCION DEL METODO-----------------------
la fc recibe un vector de objetos JS con la estructura:
[{"Op":"opcion1", "Fc":"Funcion1", "img":"imagen sin directorio"}]
x e y son las coordenadas para tomar posicion
classname es la clase asignada a la div que se crea, debe incluir formato de div, ul, li, a, etc...
owner es el contenedor del popup*/

	var div=document.createElement('div');
	var ul=document.createElement('ul');

	div.id="_divPopup"
	if (classname=="") {
		div.style.backgroundColor="white";
		div.color="gray";
		div.style.border="1px solid blue";
	} else {
		div.classList.add(classname);
	}
	div.style.position="absolute";
	div.style.display="none";
	div.style.zIndex="10000";
	div.style.width="400px";
	div.style.heigth="200px";
	div.style.left=x+"px";
	div.style.top=y+"px";

	for (var i = 0; i < obj.length; i++) {
		var li=document.createElement('li');
		if (obj[i].img!="")
			li.innerHTML='<a href="javascript:void(0);" onclick="'+obj[i].Fc+'"><img src="imagenes/'+obj[i].img+'" class="icono">'+obj[i].Op+'</a>';
		else
			li.innerHTML='<a href="javascript:void(0);" onclick="'+obj[i].Fc+'">'+obj[i].Op+'</a>';
		ul.appendChild(li);
	}
	div.appendChild(ul);
	owner.appendChild(div);

	$(_divPopup).fadeIn();

}
function quitarPopup() {
	$("#_divPopup").fadeOut();	
	$("#_divPopup").remove();
}

$(document).mouseup(function(e) 
{
    var container = $("#_divPopup");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
        quitarPopup();
    }
    
});