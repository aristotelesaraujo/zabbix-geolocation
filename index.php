<?php
/*
** CTIC - SSPDS-CE
** Copyright (C) 2012-2013 CTIC - SSPDS-CE
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**
** Contacts:
** Leandro Alves Machado - Analista de Sistemas - leandro.machado@sspds.ce.gov.br
** Aristoteles Araujo - Analista de Sistemas - aristoteles.araujo@sspds.ce.gov.br
**
**
** Colaboração: 
** Helder Santana - helder.bs.santana@gmail.com
**
**/
?>
<?php 
include 'includes/autoload.php';
error_reporting(0);

$grupo = new Conexao();
$grupo->conectar();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Cache-control" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>Geolocaliza&ccedil;&atilde;o - SSPDS/CE</title>
<script type="text/javascript" src="js/jquery-1.7.2.js"></script>
<!--
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>**/
-->
<script src="https://maps.googleapis.com/maps/api/js?v=3.10&sensor=false&libraries=weather"></script>
<script type="text/javascript" src="js/markerwithlabel.js"></script>
<script type="text/javascript" src="js/markerwithlabel_packed.js"></script>
<script type="text/javascript" src="js/oms.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/geral.css" />
<link rel="stylesheet" type="text/css" href="css/grupo.css" />

<script>

function carregarGrupo() {
	
	var grupo	= $("#grupo").val();
	var camada	= $("#camada").val();
	
	$.ajax({
		type:	"POST",
		url:	"grupo.php",
		cache:  false,
		data:	"grupo="+grupo+"&camada="+camada,
	
		beforeSend: function() {
			$('#mapa').html("<div class='carregando'><div id='carregando_imagem'></div></div>");
		},
		success: function(retorno) {
			$('#mapa').fadeIn(1000);
			$("#mapa").html(retorno);
		},
	
		error: function(txt) {
		}
	});
}

window.onload = function(){
	carregarGrupo();
}

</script>


</head>

<body>

<div id="pagina-principal">

	<table id="pagina-principal-tabela">
	
		<tr>
			<td class="pagina-principal-topo">
				
				<div id="longitude">Long:  0.000000</div>
				
				<div id="latitude">Lat:  0.000000</div>
				
				<div id="grupos">
					<select id="grupo" style="width:300px;" onchange="carregarGrupo();">
					<?php 
						$grupos = new Grupos();
						$grupos = $grupos->get_grupos();
						
						foreach ($grupos as $indice => $grupos_item):
						
								if ($grupo->group == $indice) {
					?>
									<option value="<?=$indice?>" selected><?=$indice?> - <?=$grupos_item?></option>
					<?php 
								} else {
					?>
									<option value="<?=$indice?>"><?=$indice?> - <?=$grupos_item?></option>
					<?php
								}
						endforeach;
					?>
					
					</select>
				</div>

				<div id="camadas">

				<?php
				echo "<select id='camada'>";
				echo "<option value=''>Escolha a camada</option>";
			        $ponteiro = fopen ("layers.conf","r");
       				while (!feof ($ponteiro)) {
             			  $linha = fgets($ponteiro,4096);
             			  $tamLinha = strlen($linha);
	     			  $posIgual = strpos($linha,'=');
             			  echo "<option value='".substr($linha,$posLinha+1+$posIgual)."'>".substr($linha,0,$posIgual)."</option>";
       				}//fim while
				echo "</select>";
				fclose ($ponteiro);
				?>

				</div>
				
			</td>
		</tr>
		
		<tr>
			<td class="pagina-principal-centro">
				<div id="mapa"></div>
			</td>
		</tr>
	
	</table>

</div>

</body>


</html>

