function DateTime() {
    function getDaySuffix(a) {
        var b = "" + a,
            c = b.length,
            d = parseInt(b.substring(c-2, c-1)),
            e = parseInt(b.substring(c-1));
        if (c == 2 && d == 1) return "th";
        switch(e) {
            case 1:
                return "st";
                break;
            case 2:
                return "nd";
                break;
            case 3:
                return "rd";
                break;
            default:
                return "th";
                break;
        };
    };

    this.init=function(nDate) {
      if (nDate!=undefined)
        this.date = arguments.length == 0 ? new Date() : new Date(arguments);
      else
        this.date=nDate;

      this.getDoY = function(a) {
          var b = new Date(a.getFullYear(),0,1);
      return Math.ceil((a - b) / 86400000);
      }
      this.weekdays = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
      this.months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
      this.daySuf = new Array( "st", "nd", "rd", "th" );

      this.day = {
          index: {
              week: "0" + this.date.getDay(),
              month: (this.date.getDate() < 10) ? "0" + this.date.getDate() : this.date.getDate()
          },
          name: this.weekdays[this.date.getDay()],
          of: {
              week: ((this.date.getDay() < 10) ? "0" + this.date.getDay() : this.date.getDay()) + getDaySuffix(this.date.getDay()),
              month: ((this.date.getDate() < 10) ? "0" + this.date.getDate() : this.date.getDate()) + getDaySuffix(this.date.getDate())
          }
      }

      this.month = {
          index: (this.date.getMonth() + 1) < 10 ? "0" + (this.date.getMonth() + 1) : this.date.getMonth() + 1,
          name: this.months[this.date.getMonth()]
      };

      this.year = this.date.getFullYear();

      this.time = {
          hour: {
              meridiem: (this.date.getHours() > 12) ? (this.date.getHours() - 12) < 10 ? "0" + (this.date.getHours() - 12) : this.date.getHours() - 12 : (this.date.getHours() < 10) ? "0" + this.date.getHours() : this.date.getHours(),
              military: (this.date.getHours() < 10) ? "0" + this.date.getHours() : this.date.getHours(),
              noLeadZero: {
                  meridiem: (this.date.getHours() > 12) ? this.date.getHours() - 12 : this.date.getHours(),
                  military: this.date.getHours()
              }
          },
          minute: (this.date.getMinutes() < 10) ? "0" + this.date.getMinutes() : this.date.getMinutes(),
          seconds: (this.date.getSeconds() < 10) ? "0" + this.date.getSeconds() : this.date.getSeconds(),
          milliseconds: (this.date.getMilliseconds() < 100) ? (this.date.getMilliseconds() < 10) ? "00" + this.date.getMilliseconds() : "0" + this.date.getMilliseconds() : this.date.getMilliseconds(),
          meridiem: (this.date.getHours() > 12) ? "PM" : "AM"
      };

      this.sym = {
          d: {
              d: this.date.getDate(),
              dd: (this.date.getDate() < 10) ? "0" + this.date.getDate() : this.date.getDate(),
              ddd: this.weekdays[this.date.getDay()].substring(0, 3),
              dddd: this.weekdays[this.date.getDay()],
              ddddd: ((this.date.getDate() < 10) ? "0" + this.date.getDate() : this.date.getDate()) + getDaySuffix(this.date.getDate()),
              m: this.date.getMonth() + 1,
              mm: (this.date.getMonth() + 1) < 10 ? "0" + (this.date.getMonth() + 1) : this.date.getMonth() + 1,
              mmm: this.months[this.date.getMonth()].substring(0, 3),
              mmmm: this.months[this.date.getMonth()],
              yy: (""+this.date.getFullYear()).substr(2, 2),
              yyyy: this.date.getFullYear()
          },
          t: {
              h: (this.date.getHours() > 12) ? this.date.getHours() - 12 : this.date.getHours(),
              hh: (this.date.getHours() > 12) ? (this.date.getHours() - 12) < 10 ? "0" + (this.date.getHours() - 12) : this.date.getHours() - 12 : (this.date.getHours() < 10) ? "0" + this.date.getHours() : this.date.getHours(),
              hhh: this.date.getHours(),
              m: this.date.getMinutes(),
              mm: (this.date.getMinutes() < 10) ? "0" + this.date.getMinutes() : this.date.getMinutes(),
              s: this.date.getSeconds(),
              ss: (this.date.getSeconds() < 10) ? "0" + this.date.getSeconds() : this.date.getSeconds(),
              ms: this.date.getMilliseconds(),
              mss: Math.round(this.date.getMilliseconds()/10) < 10 ? "0" + Math.round(this.date.getMilliseconds()/10) : Math.round(this.date.getMilliseconds()/10),
              msss: (this.date.getMilliseconds() < 100) ? (this.date.getMilliseconds() < 10) ? "00" + this.date.getMilliseconds() : "0" + this.date.getMilliseconds() : this.date.getMilliseconds()
          }
      };

      this.formats = {
          compound: {
              commonLogFormat: this.sym.d.dd + "/" + this.sym.d.mmm + "/" + this.sym.d.yyyy + ":" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              exif: this.sym.d.yyyy + ":" + this.sym.d.mm + ":" + this.sym.d.dd + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              esmu: this.sym.d.mm + "/" +this.sym.d.dd + "/" +this.sym.d.yyyy + " " + this.sym.t.hh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              esmuDate: this.sym.d.mm + "/" +this.sym.d.dd + "/" +this.sym.d.yyyy,
              esmuDateSpanish: this.sym.d.dd +"/"+ this.sym.d.mm + "/"  +this.sym.d.yyyy,
              /*iso1: "",
              iso2: "",*/
              mySQL: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd,
              mySQLTime: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              postgreSQL1: this.sym.d.yyyy + "." + this.getDoY(this.date),
              postgreSQL2: this.sym.d.yyyy + "" + this.getDoY(this.date),
              soap: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss + "." + this.sym.t.mss,
              //unix: "",
              xmlrpc: this.sym.d.yyyy + "" + this.sym.d.mm + "" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              xmlrpcCompact: this.sym.d.yyyy + "" + this.sym.d.mm + "" + this.sym.d.dd + "T" + this.sym.t.hhh + "" + this.sym.t.mm + "" + this.sym.t.ss,
              wddx: this.sym.d.yyyy + "-" + this.sym.d.m + "-" + this.sym.d.d + "T" + this.sym.t.h + ":" + this.sym.t.m + ":" + this.sym.t.s
          },
          constants: {
              atom: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              cookie: this.sym.d.dddd + ", " + this.sym.d.dd + "-" + this.sym.d.mmm + "-" + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              iso8601: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              rfc822: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              rfc850: this.sym.d.dddd + ", " + this.sym.d.dd + "-" + this.sym.d.mmm + "-" + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              rfc1036: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              rfc1123: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yyyy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              rfc2822: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yyyy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              rfc3339: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              rss: this.sym.d.ddd + ", " + this.sym.d.dd + " " + this.sym.d.mmm + " " + this.sym.d.yy + " " + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss,
              w3c: this.sym.d.yyyy + "-" + this.sym.d.mm + "-" + this.sym.d.dd + "T" + this.sym.t.hhh + ":" + this.sym.t.mm + ":" + this.sym.t.ss
          },
          pretty: {
              a: this.sym.t.hh + ":" + this.sym.t.mm + "." + this.sym.t.ss + this.time.meridiem + " " + this.sym.d.dddd + " " + this.sym.d.ddddd + " of " + this.sym.d.mmmm + ", " + this.sym.d.yyyy,
              b: this.sym.t.hh + ":" + this.sym.t.mm + " " + this.sym.d.dddd + " " + this.sym.d.ddddd + " of " + this.sym.d.mmmm + ", " + this.sym.d.yyyy
          }
      };
    }
};  

  function Ajax(url, resul) {

    var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  //alert("EN AJAX: "+JSON.parse(resp).nombre);
                  var valor=resp;
                  if (resul != null) {
                    resul.resp=resp;
                  }
                  return valor;
                }
            }
        xmlhttp.open("GET",url,false);
        xmlhttp.send();
        
}

function seleccionarCombo(combo, valor) {
  //alert(valor);
  var cbo = document.getElementById(combo);

  for (var i = 0; i < cbo.options.length-1; i++) {
    if (cbo.options[i].value==valor) {
      cbo.selectedIndex=i;
      return;
    }
  };
  return;
}
function valorCombo(combo) {
  var cbo=document.getElementById(combo);
  var valor=cbo.options[cbo.selectedIndex].value;
  return valor;
}

function textoCombo(combo) {
  var cbo=document.getElementById(combo);
  var valor=cbo.options[cbo.selectedIndex].text;
  return valor;
}

function buscarProv(e,load, loja) {//busca un proveedor por su código, devuelve dupla codigo/nombre
  if (e.keyCode==13) {
    var cbotrader=document.getElementById("cboTrader");
    var trader=cboTrader.options[cboTrader.selectedIndex].value;
    var obj=document.getElementById("txtProv");
    if (obj.value=="" || obj.value.length==0) {
      //muestra el cuadro de asistencia
      var sombra=document.getElementById("fondogris");
      var body = document.body,
          html = document.documentElement;
      var height = Math.max( body.scrollHeight, body.offsetHeight, 
          html.clientHeight, html.scrollHeight, html.offsetHeight );
      sombra.style.height=height+'px';      
      sombra.style.display="inline-block";

      document.getElementById("divProv").style.display="inline-block";
      helpBox('helpProveedores', trader);
    } else {
      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="") {document.getElementById("lblProv").value="";} else {
                    if (!load) {
                    document.getElementById("txtClave").value="";
                    document.getElementById("lblClave").value="";
                    var fobl=document.getElementById("txtFOBL");
                    if (!fobl==null) fobl.value="";
                    var fob=document.getElementById("txtFOB");
                    if (!fob==null) fob.value="";
                    

                  }

                  var Jo=JSON.parse(resp);
                  document.getElementById("lblProv").value=Jo.codcorto+' - '+ Jo.nombre;
                  document.getElementById("txtProv").value=Jo.codigo;
                  //cargarPaises();
                  if (!load) {
                    document.getElementById("cboMoneda").selectedIndex=Jo.Moneda-1;
                    document.getElementById("txtCond").value=Jo.condpago;
                    buscarCondicion({keyCode:13});
                  }

                  buscarLojas(Jo.codigo, trader);
                  var cbo=document.getElementById("cboLoja");
                  if (load) {
                    for (var i = 0; i < cbo.options.length; i++) {
                      if (cbo.options[i].value==loja) cbo.options[i].selected=true;
                    };
                  }
                  buscarPuerto(Jo.codigo, trader);
                  
                  
                  
                  }
                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=Proveedores&trader="+trader+"&key="+obj.value,true);
        xmlhttp.send();
    }
  }

}

function buscarLojas(prov, trader) {//busca un proveedor por su código, devuelve dupla codigo/nombre
      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="") 
                    {document.getElementById("cboLoja").options.length=0;} 
                  else 
                    {
                    var Jo=JSON.parse(resp);
                    var cbo=document.getElementById("cboLoja");
                    cbo.options.length=0;
                    for (var i=0; i<Jo.length; i++) {
                      var o=document.createElement("option");
                      o.innerHTML=Jo[i].loja+"-"+Jo[i].descrip;
                      o.value=Jo[i].loja;
                      cbo.add(o,i);
                    }
                  }
                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=getLojas&trader="+trader+"&key="+prov,false);
        xmlhttp.send();
 }

function buscarPuerto(prov, trader) {//busca un proveedor por su código, devuelve dupla codigo/nombre
      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="") 
                    {document.getElementById("cboPuerto").options.length=0;} 
                  else 
                    {
                    var Jo=JSON.parse(resp);
                    var cbo=document.getElementById("cboPuerto");
                    cbo.options.length=0;
                    for (var i=0; i<Jo.length; i++) {
                      cbo.options.add(new Option(Jo[i].puerto,i))
                    }
                  }
                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=getPuertosXProv&trader="+trader+"&key="+prov,true);
        xmlhttp.send();
 }

function buscarClave(e, clave) {//busca un proveedor por su código, devuelve dupla codigo/nombre
  if (e.keyCode==13) {
    
    if (clave==undefined || clave==null)
      clave=document.getElementById("txtClave").value;

    clave=clave.replace('+','||plus||');//reemplaza caracteres que no pueden ser incluídos como URL
    clave=clave.replace('-','||minus||');
    var prov=document.getElementById("txtProv").value;
    var p=document.getElementById("cboPuerto");
    var puerto=p.options[p.selectedIndex].text;
    var trader=getValue('cboTrader');
    
    if (clave=="" || clave.length==0) {
      //muestra el cuadro de asistencia
      document.getElementById("fondogris").style.display="inline-block";
      document.getElementById("divClave").style.display="inline-block";
      helpBox('helpClave',trader);
    } else {
      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="{}") {document.getElementById("lblClave").value="";} else {
                  var Jo=JSON.parse(resp);
                  document.getElementById("lblClave").value=Jo[0].Descripcion;
                  document.getElementById("txtClave").value=Jo[0].ClaveGen;
                  document.getElementById("txtFOBL").focus();
                  document.getElementById("tblCodigo").innerHTML='';
                  cargarCodigos();
                  }
                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=ClaveGen&clave="+clave+"&prov="+prov+"&trader="+trader,false);
        //xmlhttp.open("GET","./ajaxfunciones.php?consulta=ClaveGen&clave="+clave,false);
        xmlhttp.send();
    }
  }

}

function cargarCodigos() {
    var clave=document.getElementById("txtClave").value;
    clave=clave.replace('+','||plus||');//reemplaza caracteres que no pueden ser incluídos como URL
    clave=clave.replace('-','||minus||');

    var prov=document.getElementById("txtProv").value;
    var p=document.getElementById("cboDestino");
    var dest=p.options[p.selectedIndex].value;
    
      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  var Jo=JSON.parse(resp);

                  var data='<tr><th>Code</th><th>Description</th><th>Zone</th><th>Countries</th></tr>';
                  for (var i=0; i<Jo.length; i++) {
                    data=data+'<tr><td><a href="#" onclick="document.getElementById(\'txtCodigo\').value=\''+Jo[i].codigo+'\';buscarPrecio();cancelar();">'+Jo[i].codigo+'</a></td><td>'+Jo[i].descripcion+'</td><td>'+Jo[i].region+'</td><td>'+Jo[i].paises+'</td></tr>';
                  }

                  document.getElementById("tblCodigo").innerHTML=data;

                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=CodigoTotvs&clave="+clave+"&destino="+dest,false);
        xmlhttp.send();

}

function cargarPaises() {//busca un proveedor por su código, devuelve dupla codigo/nombre

      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="") 
                    {document.getElementById("cboDestino").options.length=0;} 
                  else 
                    {
                    var Jo=JSON.parse(resp);

                    var cbo=document.getElementById("cboDestino");
                    cbo.options.length=0;

                    for (var i=0; i<Jo.length; i++) {
                      cbo.options.add(new Option(Jo[i].pais,Jo[i].codigo))
                    }
                  }
                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=Paises",true);
        xmlhttp.send();
 }
 function buscarBranch() {
    var cboPais=document.getElementById("cboDestino");
    var pais=cboPais.options[cboPais.selectedIndex].text;
      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="") 
                    {document.getElementById("cboBranch").options.length=0;} 
                  else 
                    {
                    var Jo=JSON.parse(resp);

                    var cbo=document.getElementById("cboBranch");
                    cbo.options.length=0;

                    for (var i=0; i<Jo.length; i++) {
                      cbo.options.add(new Option(Jo[i].Descripcion,Jo[i].idDestino))
                    }
                  }
                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=Branches&pais="+pais,false);
        xmlhttp.send();  
 }
 function buscarPrecio() {//busca un proveedor por su código, devuelve dupla codigo/nombre
  
      var cbranch=document.getElementById("cboDestino");
      var clave=document.getElementById("txtClave").value;
      var pais=cbranch.options[cbranch.selectedIndex].value;
      var cboTrader=document.getElementById("cboTrader");
      var trader=cboTrader.options[cboTrader.selectedIndex].value;
      var prov=document.getElementById("txtProv").value;

      if (pais=="" || clave=="") {document.getElementById("txtFOB").value="Sin datos";return false;}

      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="{}") 
                    {document.getElementById("txtFOB").value="Sin datos";
                    document.getElementById("txtFOBL").value="Sin datos";} 
                  else 
                    {
                    var Jo=JSON.parse(resp);
                    var precio=document.getElementById("txtFOB");
                    precio.value=Number(Jo[0].precio).toFixed(2);
                    document.getElementById("txtFOBL").value=precio.value;
                  }
                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=buscarPrecio&Codigo="+clave+"&Pais="+pais+"&prov="+prov+"&trader="+trader,false);
        xmlhttp.send();
 }

function buscarVia(e) {//busca un proveedor por su código, devuelve dupla codigo/nombre
  if (e.keyCode==13) {
    var obj=document.getElementById("txtVia");
    if (obj.value=="" || obj.value.length==0) {
      //muestra el cuadro de asistencia
      document.getElementById("fondogris").style.display="inline-block";
      document.getElementById("divVia").style.display="inline-block";
      helpBox('helpVias');
      }
    else {

      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="") 
                    {document.getElementById("txtVia").value="Sin datos";
                    document.getElementById("lblVia").value="";
                    document.getElementById("txtETA").value="";
                    } 
                  else 
                    {
                    var Jo=JSON.parse(resp);
                    document.getElementById("txtVia").value=Jo.codigo;
                    document.getElementById("lblVia").value=Jo.descripcion;
                    document.getElementById("lblDetVia").value=Jo.destino+" - "+Jo.dias+" days";
                    document.getElementById("txtDias").value=Jo.dias;
                    var etdw=document.getElementById("txtETDW");
                    if (etdw.value!="") {
                      var fecha=new Date(etdw.value);
                      console.log(fecha);
                      console.log(fecha.setDate(fecha.getDate()+Jo.dias))
                      document.getElementById("txtETA").value=fecha.setDate(fecha.getDate()+Jo.dias);
                    }
                  }
                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=buscarVia&codigo="+obj.value,false);
        xmlhttp.send();
      }
  }
}

function buscarCondicion(e) {//busca un proveedor por su código, devuelve dupla codigo/nombre
  if (e.keyCode==13) {
    var obj=document.getElementById("txtCond");
    var trader=valorCombo("cboTrader");
    if (obj.value=="" || obj.value.length==0) {
      //muestra el cuadro de asistencia
      document.getElementById("fondogris").style.display="inline-block";
      document.getElementById("divCond").style.display="inline-block";
      helpBox('helpCondiciones', trader);
      }
    else {

      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="") 
                    {document.getElementById("txtCond").value="Sin datos";
                    document.getElementById("lblCond").value="";
                    } 
                  else 
                    {
                    var Jo=JSON.parse(resp);
                    document.getElementById("txtCond").value=Jo.codigo;
                    document.getElementById("lblCond").value=Jo.descripcion;
                    
                  }
                }
            }
        xmlhttp.open("GET","./ajaxfunciones.php?consulta=buscarCondiciones&codigo="+obj.value+"&trader="+trader,false);
        xmlhttp.send();
      }
  }
}

function calcularETA(idioma) {
  var etdw=document.getElementById("txtETDW");
  var dias=document.getElementById("txtDias").value;
  
  if (etdw.value!="") {
    if (idioma=="I") {
      var mes=etdw.value.substring(0,2);
      var dia=etdw.value.substring(3,5);
      var df="MM/DD/YYYY";
    } else {
      var mes=etdw.value.substring(3,5);
      var dia=etdw.value.substring(0,2);      
      var df="DD/MM/YYYY";
    }
    var ano=etdw.value.substring(6,10);


    var d1=moment(ano+"-"+mes+"-"+dia, "YYYY-MM-DD").format(df);
    
    d1=moment(ano+"-"+mes+"-"+dia, "YYYY-MM-DD").add(dias, 'd').format(df);
    document.getElementById("txtETA").value=d1;
/*    console.log(dia+mes+ano);
    var fecha=new Date(ano,mes,dia);
    alert(fecha.getDate());
    alert(fecha.setDate(fecha.getDate()+30));
    console.log(fecha.setDate(fecha.getDate()+30));
    document.getElementById("txtETA").value=fecha.setDate(fecha.getDate()+30);*/

  }

}
function setvalue(campo, valor) {
  var c=document.getElementById(campo);
  c.value=valor;
  var e={keyCode:13};//simula un enter sobre el control
  switch (campo) {
    case 'txtProv':
      buscarProv(e);
      toggle('divProv', 'visible', false);
      break;
    case 'txtClave':
      buscarClave(e);
      try {
        buscarGK(valor);
      } catch(err) {}

      toggle('divClave', 'visible', false);
      break;
    case 'txtVia':
      buscarVia(e);
      toggle('divVia', 'visible', false);
      break;
    case 'txtCond':
      buscarCondicion(e);
      toggle('divCond', 'visible', false);
      break;

  }
  
  toggle('fondogris','visible',false);//oculta el fondo
}

function helpBox(tipo, trader) {
    switch (tipo) {
      case 'helpProveedores':
        var url="./ajaxfunciones.php?consulta=helpBox&tipo="+tipo+"&trader="+trader;
        var nomTabla="tblProv";
        var nomCampo="txtProv";
        break;
      case 'helpClave':
        var prov=document.getElementById("txtProv").value;
        var cpuerto=document.getElementById("cboPuerto");
        puerto=cpuerto.options[cpuerto.selectedIndex].text;
        var nomTabla="tblClave";
        var nomCampo="txtClave";
        var url="./ajaxfunciones.php?consulta=helpBox&tipo="+tipo+"&prov="+prov+"&puerto="+puerto+"&trader="+trader;
        break;
      case 'helpVias':
        var cpuerto=document.getElementById("cboPuerto");
        puerto=cpuerto.options[cpuerto.selectedIndex].text;
        var nomTabla="tblVia";
        var nomCampo="txtVia";
        var url="./ajaxfunciones.php?consulta=helpBox&tipo="+tipo+"&puerto="+puerto;
        break;
      case 'helpCondiciones':
        var url="./ajaxfunciones.php?consulta=helpBox&tipo="+tipo+"&trader="+trader;
        var nomTabla="tblCond";
        var nomCampo="txtCond";
        break;


    }
    

      var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  var resp=xmlhttp.responseText;
                  
                  if (resp=="") 
                    {return false;} 
                  else 
                    {
                    var Jo=JSON.parse(resp);

                    var tabla=document.getElementById(nomTabla);

                    while(tabla.rows.length > 1) {
                      tabla.deleteRow(1);
                    }                    

                    for (var i = 0; i < Jo.length; i++) {
                      var row=tabla.insertRow(i+1);

                      var cell0=row.insertCell(0);
                      var cell1=row.insertCell(1);

                      
                      cell0.innerHTML='<a href="#" onclick="setvalue(\''+nomCampo+'\',\''+Jo[i].codigo+'\');">'+Jo[i].codigo+'</a>';
                      cell1.innerHTML='<a href="#" onclick="setvalue(\''+nomCampo+'\',\''+Jo[i].codigo+'\');">'+Jo[i].descripcion+'</a>';

                      if (typeof(Jo[i].dato2)!=='undefined') {
                        var cell2=row.insertCell(2);
                        cell2.innerHTML='<a href="#" onclick="setvalue(\''+nomCampo+'\',\''+Jo[i].codigo+'\');">'+Jo[i].dato2+'</a>';
                      }
                      if (typeof(Jo[i].dato3)!=='undefined') {
                        var cell3=row.insertCell(3);
                        cell3.innerHTML='<a href="#" onclick="setvalue(\''+nomCampo+'\',\''+Jo[i].codigo+'\');">'+Jo[i].dato3+'</a>';
                      } 

                    };

                  }
                }
            }
        xmlhttp.open("GET",url,false);
        xmlhttp.send();
  
}