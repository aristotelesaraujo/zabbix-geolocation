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

class Conexao {
		
	private $host;
	private $user;
	private $password;
	private $dbname;
	private $dbtype;

	//Metodo construtor
	function __construct(){}
	
	//Metodo que inicia conexao com o banco de dados
	function conectar() {
		
		include('../../conf/zabbix.conf.php');
		
		$arquivo = 'geolocation.conf';
		$handle = fopen($arquivo,'r');
		$conteudo = fread($handle, filesize ($arquivo));
		fclose ($handle);

		$texto = explode("\n",$conteudo);

		for ($i = 0; $i < count($texto); $i++) {
			$informacao[$i] = rtrim(substr($texto[$i], strpos($texto[$i],'=') + 1));
		}
		
		$this->host = $DB['SERVER'];
		$this->user = $DB['USER'];
		$this->password = $DB['PASSWORD'];
		$this->dbname = $DB['DATABASE'];
		$this->dbtype = $DB['TYPE'];
                $this->zdbtype = $this->dbtype;

		//echo $this->zdbtype;

		if ($this->dbtype=='MYSQL'){
			$this->con = @mysql_connect($this->host,$this->user,$this->password);
			@mysql_select_db($this->dbname,$this->con);
			if ($this->con) {
				//echo "Conexao efetuada com sucesso";
			} else {
				echo "Conexao nao efetuada <br>" . mysql_error();
			}
		} elseif ($this->dbtype=='POSTGRESQL') {
		        $connect = "host=$this->host user=$this->user password=$this->password dbname=$this->dbname";
			$this->con = pg_connect($connect);
			if ($this->con) {
				//echo "Conexao efetuada com sucesso";
			} else {
				echo "Conexao nao efetuada <br>";
			}


		}

		$this->group = $informacao['4'];
	}

	
	//Metodo que fecha a conexao com o banco de dados
	function desconectar() {
	        if ($this->dbtype=='MYSQL') {
			@mysql_close($this->con);
		} elseif ($this->dbtype=='POSTGRESQL') {
			pg_close($this->con);
		}
	}
	
}

?>
