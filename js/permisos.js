function go (user, modulo, args) {
	if (modulo=='' || modulo.length==0) return false;

  var res=false;

  oAjax.request="Acceso?user="+user+"&modulo="+modulo;
  oAjax.async=false;
  oAjax.send(resp)

  function resp(data) {
    if (data.responseText.length<3) {
      alert("error returning user permission"); 
      return false;
    }
    else 
      {
      var Jo=JSON.parse(data.responseText);
      if (Jo[0].respuesta=='0') {
        alert(Jo[0].Accion);
        res=false;
      } else {
        var link =Jo[0].Accion;
        if (args!='') {
          link+='?'+args;
        }
        window.location.href=link;
        res=true;
      }
    }
    
  }

  return res;
}

function checkAccess (user,modulo) {
  if (modulo=='' || modulo.length==0) return false;

  oAjax.request="Acceso?user="+user+"&modulo="+modulo;
  oAjax.async=false;
  var res=false
  oAjax.send(resp);
  return res;

  function resp(data) {
    if (data.responseText.length<3) {
      alert('Sin respuesta del servidor');
      res= false;
    }
    var obj=JSON.parse(data.responseText);

    if (obj[0].respuesta=='1') {
      res= true;
    } else {
      alert(obj[0].Accion);
      res= false;
    }
  }
}