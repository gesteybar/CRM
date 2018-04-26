<div class="fondonegro" id="divLgBack" style="background-color:transparent">
	<div id="divLogin">
		<h1>Ingreso al sistema</h1>
		<table>
			<thead></thead>
			<tbody>
				<tr id="trPais">
					<td>País</td>
					<td><select id="cboLgPais" onchange="cargarEmpresas();">
						<option>Argentina</option>
						<option>Brasil</option>
						<option>Chile</option>
						<option>Colombia</option>
						<option>Mexico</option>
						<option>Perú</option>
						<option>Uruguay</option>
						<option>Venezuela</option>
					</select></td>
				</tr>
				<tr id="trEmpresa">
					<td>Empresa</td>
					<td><select id="cboLgEmpresa"></select></td>
				</tr>
				<tr id="trUsuario">
					<td>Usuario</td>
					<td><select id="cboLgUsuario"></select><input type="text" id="txtLgUsuario"></td>
				</tr>
				<tr id="trPass">
					<td>Contraseña</td>
					<td><input type="password" id="txtLgPass"></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" align="center">
						<button type="button" class="boton btnAzul" onclick="login()"><img src="./imagenes/loginwhite.png">Ingresar</button>
						<button type="button" class="boton btnNaranja" onclick="cerrarLogin()"><img src="./imagenes/cancelwhite.png">Cerrar</button>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<script type="text/javascript">
	function cerrarLogin() {
		$("#divLogin").parent().hide();
		$("#divLogin").hide();
	}
</script>