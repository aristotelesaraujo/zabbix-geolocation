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
**/
?>
<?php
include 'includes/autoload.php';
error_reporting(0);

$hosts = new Grupos();
$hosts->get_hosts($_POST['grupo']);

//Read configuracao de weather
$ponteiro = fopen ("geolocation.conf","r");
while (!feof ($ponteiro)) {
	$linha = fgets($ponteiro,4096);
	$tamLinha = strlen($linha);
	$posIgual = strpos($linha,'=');
	$chave = substr($linha,0,$posIgual);
 	if ( $chave == "weather") {
		$vChaveWeather = substr($linha,$posLinha+1+$posIgual);
		//echo $chave.'='.$vChaveWeather.'<br>';
	}
}//fim while

?>

<script type="text/javascript">

var map;
var myOptions;
var myLatlng;
var kmlLayer;
var ajaxRequest;
var oms;

<?php 
	for ($i = 0; $i < $hosts->qtd_hosts; $i++) {	
		
		if ($hosts->status[$i] == "0") {
?>
			var marker<?=$hosts->hostid[$i]?> = new MarkerWithLabel({
					position: new google.maps.LatLng(<?=$hosts->lat[$i]?>, <?=$hosts->lon[$i]?>),
					map: map,
					title: "<?=mb_strtoupper($hosts->host[$i],'UTF-8')?>",
					labelContent: "",
					labelAnchor: new google.maps.Point(15, 0),
					labelClass: "labels",
					icon: "images/on.png"
			});

			var marker<?=$hosts->hostid[$i]?>Info;
<?php 
		} else {
?>
			var marker<?=$hosts->hostid[$i]?> = new MarkerWithLabel({
				position: new google.maps.LatLng(<?=$hosts->lat[$i]?>, <?=$hosts->lon[$i]?>),
				map: map,
				title: "<?=mb_strtoupper($hosts->host[$i],'UTF-8')?>",
				labelContent: "",
				labelAnchor: new google.maps.Point(15, 0),
				labelClass: "labels",
				icon: "images/off.png"
			});

			var marker<?=$hosts->hostid[$i]?>Info;
<?
		}
	}
?>   

$(document).ready(function(){

	var ajaxRequest;
	
	var myLatlng = new google.maps.LatLng(<?=$hosts->ponto_lat?>,<?=$hosts->ponto_lon?>);
	var myOptions = {
			zoom: 8,
			center: myLatlng,
			scaleControl: true,
			//disableDefaultUI: true,
			//mapTypeControl: true,
			scrollwheel: false,
			//navigationControlOptions: { style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR },
			mapTypeId: google.maps.MapTypeId.ROADMAP
	}

	var map = new google.maps.Map(document.getElementById("pagina-grupo-mapa"), myOptions);

	//ADD_WEATHER - Camadas com a previsao do tempo atual para a area exibida
			<?php
			if (substr($vChaveWeather,0,3)=="yes") {
			?>			
				var weatherLayer = new google.maps.weather.WeatherLayer({
					temperatureUnits: google.maps.weather.TemperatureUnit.CELCIUS
				});
				weatherLayer.setMap(map);
				var cloudLayer = new google.maps.weather.CloudLayer();   
				cloudLayer.setMap(map);					
			<?php
				}
			?>
        //

	var oms = new OverlappingMarkerSpiderfier(map,
		      {markersWontMove: true, markersWontHide: true});

	var bounds = new google.maps.LatLngBounds();

	google.maps.event.addListener(map, "mousemove", function (event){
		var Lat = event.latLng.lat().toFixed(6);
		var Long = event.latLng.lng().toFixed(6);
		document.getElementById("latitude").innerHTML = "Lat: " + Lat;
		document.getElementById("longitude").innerHTML = "Lon: " + Long;
	}); 

	var kmlOptions = {
			suppressInfoWindows: true,
			preserveViewport: true,
			map: map
	};

	kmlLayer = new google.maps.KmlLayer(null);

	<?php
	 	if ( isset($_POST['camada']) ) {
	?>
			kmlLayer = new google.maps.KmlLayer("<?=$_POST['camada']?>",kmlOptions);
	<?
		}
	?>

	$('#camada').live('change', function () {

		var camada = $("#camada").val();
		var kmlUrl = camada;
		var kmlOptions = {
				suppressInfoWindows: true,
				preserveViewport: true,
				map: map
		};
		
		if (camada != "") {
			kmlLayer.setMap(null);
			kmlLayer = new google.maps.KmlLayer(kmlUrl,kmlOptions);
		} else {
			kmlLayer.setMap(null);
		}

	});

	/*
	for(var i in locations) {
	    var ll = new google.maps.LatLng(locations[i].lat, 
	        locations[i].lng);
	    bounds.extend(ll);
	}*/

	<?php 
		for ($i = 0; $i < $hosts->qtd_hosts; $i++) {
		if ($hosts->ip[$i] == "") {
                    $hosts->ip[$i]=gethostbyname($hosts->dns[$i]);
                }

	?>  
			var ll = new google.maps.LatLng(<?=$hosts->lat[$i]?>, <?=$hosts->lon[$i]?>);
			bounds.extend(ll);
			
			marker<?=$hosts->hostid[$i]?>.setMap(map);
			oms.addMarker(marker<?=$hosts->hostid[$i]?>);

			marker<?=$hosts->hostid[$i]?>Info = new google.maps.InfoWindow(
					{ content: "<div id='div'>" +
									"<table id='tabela'>" +
										"<tr>" +
											"<td class='topo'>&nbsp;" +
												"<?=mb_strtoupper($hosts->host[$i],'UTF-8')?>" +
											"</td>" +
										"</tr>" +
										"<tr>" +
											"<td class='ip'>&nbsp;" +
												"<font size='2'>IP:&nbsp;" + "<?=$hosts->ip[$i]?>" + "</font>" +
											"</td>" +
										"</tr>" +
										"<tr>" +
											"<td class='lat'>&nbsp;" +
												"<font size='2'>Latitude:&nbsp;" + "<?=$hosts->lat[$i]?>" + "</font>" +
											"</td>" +
										"</tr>" +
										"<tr>" +
											"<td class='lon'>&nbsp;" +
												"<font size='2'>Longitude:&nbsp;" + "<?=$hosts->lon[$i]?>" + "</font>" +
											"</td>" +
										"</tr>" +
										"<tr>" +
											"<td class='status'>&nbsp;" +
												"<font size='2'>Status:&nbsp;" + "<? if ($hosts->status[$i] == "0") { echo " OK"; } else { echo " INCIDENTE"; } ?>" + "</font>" +
											"</td>" +
										"</tr>" +
									"</table>" +
								"</div>" 
			});
	<?php 
		}
	?>

	var iw = new google.maps.InfoWindow();

	var usualColor = 'eebb22';
  	var spiderfiedColor = 'ffee22';
  	var iconWithColor = function(color) {
    return 'http://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|+|' + color + '|000000|ffff00';}

  	var shadow = new google.maps.MarkerImage(
		'https://www.google.com/intl/en_ALL/mapfiles/shadow50.png',
		new google.maps.Size(37, 34),  // size   - for sprite clipping
  	    new google.maps.Point(0, 0),   // origin - ditto
  	    new google.maps.Point(10, 34)  // anchor - where to meet map location
	);

  	oms.addListener('spiderfy', function(markers) {
        for(var i = 0; i < markers.length; i ++) {
          //markers[i].setIcon(iconWithColor(spiderfiedColor));
          markers[i].setShadow(null);
        } 
        iw.close();
   });

  	
  	oms.addListener('unspiderfy', function(markers) {
        for(var i = 0; i < markers.length; i ++) {
          //markers[i].setIcon(iconWithColor(usualColor));
          markers[i].setShadow(shadow);
        }
    });

  	oms.addListener('click', function(marker) {
  		<?php 
  			for ($i = 0; $i < $hosts->qtd_hosts; $i++) {
  		?> 
				if (marker == marker<?=$hosts->hostid[$i]?>) {
					marker<?=$hosts->hostid[$i]?>Info.open(map, marker<?=$hosts->hostid[$i]?>);
					map.setCenter(new google.maps.LatLng(<?=$hosts->lat[$i]?>,<?=$hosts->lon[$i]?>));
					map.setZoom(17);
				}
  		<?php 
  			}
  		?>
  	});
  	

		
	map.fitBounds(bounds);  

	window.onload = function(){
		carregarGrupoOpcao();
	}

	// for debugging/exploratory use in console
    window.map = map;
    window.oms = oms;
});

function getHost(valor) {

	<?php 
	for ($i = 0; $i < $hosts->qtd_hosts; $i++) {
	?>
		if (valor == "marker<?=$hosts->hostid[$i]?>"){
			marker<?=$hosts->hostid[$i]?>Info.open(map, marker<?=$hosts->hostid[$i]?>);
			map.setCenter(new google.maps.LatLng(<?=$hosts->lat[$i]?>,<?=$hosts->lon[$i]?>));
			map.setZoom(17);
		}
	<?php 
	}
	?>
}


function carregarGrupoOpcao() {

	var grupo = $("#grupo").val();
	var camada = $("#camada").val();
	
	ajaxRequest = $.ajax({
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
			ajaxRequest.abort();
		},

		error: function(txt) {
		}
	});

}

</script>

<div id="pagina-grupo-mapa"></div>

<div id="pagina-grupo-host">

	<?php 
		if ($hosts->qtd_hosts == 0) {
	?>
			<select id="host">
				<option value="" selected="selected">Nenhum host encontrado&nbsp;&nbsp;</option>
			</select>
	<?php 
		} else {
	?>
			<select id="host" onchange="getHost(this.value);">
					<option value="" selected="selected">Escolha o host</option>
	<?php 
			for ($i = 0; $i < $hosts->qtd_hosts; $i++) {
			
				if ($hosts->status[$i] == "0") {	
	?>
					<option value="marker<?=$hosts->hostid[$i]?>"><?=mb_strtoupper($hosts->host[$i],'UTF-8')?></option>
	<?php
				} else {
	?>
					<option class="erro" value="marker<?=$hosts->hostid[$i]?>">
						<?=mb_strtoupper($hosts->host[$i],'UTF-8')?>
					</option>
	<?php 
				}
			}
		}
	?>
</div>

