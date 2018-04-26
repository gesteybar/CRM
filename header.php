
<script type="text/javascript">
    var refreshTime = 500000; // every 10 minutes in milliseconds
    window.setInterval( function() {
        $.ajax({
            cache: false,
            type: "GET",
            url: "keepalive.php",
            success: function(data) {
            }
        });
    }, refreshTime );

    function config() {
        if (checkAccess(<?= (isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : 0) ?>, '1'))
            location.href="config.php";
    }
</script>
<script type="text/javascript">
        function logoff() {
            
        oAjax.server="ajaxfunciones.php?consulta=";
        oAjax.request="logoff";
        oAjax.send(resp2);
        function resp2(data) {
            location.href="index.php";
        }
    }
</script>

<div id="divHeader" class=""> 
    <p style="padding:0 10px;display:inline-block;">
        <img src="./imagenes/logotipoGlossy.png" width="70" style="vertical-align:middle;margin-top:-10px">
        <span class="letraLogo logoTipo1">C</span><span class="letraLogo logoTipo2">R</span><span class="letraLogo logoTipo3">M</span>
    </p>
    <? if (isset($_SESSION['idUsuario'])) {?>
    <div id="divUserSession">
        <img src="./imagenes/user.png">
        <h2 id="lblUserSession"><?= $_SESSION['Nombre'] ?></h2>
        <button class="boton btnGris" type="button" onclick="logoff()">Cerrar sesión</button>
    </div>

    <? } ?>
</div>  
    
    <div id="divMenu">
        <h2>Vistas disponibles</h2>
        <ul>
            <li><a href="#"><p class="menuText">Gestiones pendientes</p><p class="menuBullet">3</p></a></li>
            <li><a href="#"><p class="menuText">Todas las gestiones</p><p class="menuBullet">5</p></a></li>
            <li><a href="#"><p class="menuText">Autorizaciones</p><p class="menuBullet">2</p></a></li>
        </ul>
    </div>  
    <? if (isset($_SESSION['idUsuario'])) {?>
    <div id="divLateral">
        <ul>
            <li><a href="javascript:void(0);" onclick="showMenu();"><img src="./imagenes/menuwhite.png" title="Mostrar Menu"></a></li>
            <li><a href="javascript:void(0);" onclick="search();"><img src="./imagenes/lupawhite.png" title="Buscar"></a></li>
            <li><a href="javascript:void(0);" onclick="config();"><img src="./imagenes/gearwhite.png" title="Configuración"></a></li>
        </ul>
    </div>
    <? } ?>
