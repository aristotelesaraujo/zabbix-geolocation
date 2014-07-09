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
** Colaborações:
** Jacó Ramos <j4c0r4m0s@gmail.com>
**
**/
?>
<?php

class Grupos extends Conexao {
	

	//Metodo construtor
	function __construct(){}
        	

	function get_grupos() {
		
		$this->conectar();

		$query = "SELECT groupid, name FROM groups ORDER BY groupid ASC";
		if ($this->zdbtype=='MYSQL') {
			$res = mysql_query($query);
			while ($dados = mysql_fetch_array($res)) {
				$groupid[] = strtoupper($dados["groupid"]);
				$name[] = strtoupper($dados["name"]);
			}

		} elseif ($this->zdbtype=='POSTGRESQL') {
			$res = pg_query($query);
			while ($dados = pg_fetch_array($res)) {    
			$groupid[] = strtoupper($dados["groupid"]);
			$name[] = strtoupper($dados["name"]);
			}
		}
	
		$grupos = array();
		
		for ($i = 0; $i < count($groupid); $i++) {
			$grupos[$groupid[$i]] = $name[$i];
		}
		
		$this->desconectar();
		
		return $grupos;
	}
	
	function get_hosts($grupo) {
		
		$this->conectar();
		
		$arquivo = 'geolocation.conf';
		$handle = fopen($arquivo,'r');
		$conteudo = fread($handle, filesize ($arquivo));
		fclose ($handle);

		$texto = explode("\n",$conteudo);
		
		for ($i = 0; $i < count($texto); $i++) {
			$informacao[$i] = rtrim(substr($texto[$i], strpos($texto[$i],'=') + 1));
		}
		
		$query = "SELECT DISTINCT HG.hostgroupid, HG.hostid, HG.groupid, H.host, I.ip, I.dns, HI.location_lat, HI.location_lon 
				  FROM hosts_groups as HG , hosts as H, interface as I, host_inventory as HI
				  WHERE HG.groupid = ".$grupo." 
				  AND HG.hostid = H.hostid 
				  AND I.hostid = H.hostid
				  AND HI.hostid = H.hostid
				  AND HI.location_lat != ''
          		  	  AND HI.location_lon != ''
				  ORDER BY H.host ASC";

		if ($this->zdbtype=='MYSQL') {
			$res = mysql_query($query);
			$dbtype_func = "mysql_fetch_array"; //0.3.5
		} elseif ($this->zdbtype=='POSTGRESQL') { 
			$res = pg_query($query);
			$dbtype_func = "pg_fetch_array"; //0.3.5
		}
		
		while ($dados = $dbtype_func($res)) { //0.3.5
			$hostid[]	=	$dados["hostid"];		
			$host[]		=	$dados["host"];
			$ip[]		=	$dados["ip"];
			$dns[]          =       $dados["dns"];
			$lat[]		=	$dados["location_lat"];
			$lon[]		=	$dados["location_lon"];
		}
		
		$ponto_lat 	=  array_sum($lat)/count($host);
		$ponto_lon =  array_sum($lon)/count($host);
		
		if (count($host) <= 0) {
			return print "<script type=\"text/javascript\">
							alert('Nenhum host encontrado para esse grupo.');
						</script>";
		}
		
		$this->hostid		=	$hostid;
		$this->host		=	$host;
		$this->ip		=	$ip;
		$this->dns		=	$dns;
		$this->lat		=	$lat;
		$this->ponto_lat	=	$ponto_lat;
		$this->lon		=	$lon;
		$this->ponto_lon	=	$ponto_lon;
		$this->qtd_hosts	=	count($host);
		
		for ($i = 0; $i < $this->qtd_hosts; $i++) {
			
			if ($this->ip[$i] <> "") {
				$this->cmd[$i] = 'fping -r 1 -b '.$informacao['5'].' -t '.$informacao['6'].' ' . $this->ip[$i];
                        } else {
				$this->cmd[$i] = 'fping -r 1 -b '.$informacao['5'].' -t '.$informacao['6'].' ' . $this->dns[$i];
                        }
			
			//$this->cmd[$i] = 'fping -r 1 -b '.$informacao['5'].' -t '.$informacao['6'].' ' . $this->ip[$i];
			//echo $this->cmd[$i] . "<br>";
			exec($this->cmd[$i],$this->saida[$i],$this->retorno);
			$this->status[$i] = $this->retorno;
			
			//string exec ( string $command [, array &$output [, int &$return_var ]] )

			
		}
		
		$this->desconectar();
	}
	
}

?>
