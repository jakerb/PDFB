<?php

/* 
 * PFDB: PHP Flat Database
 * Author: @jakebown1
 * Date: 10 Oct. 2014
 * Version: 1.0 
 */

	error_reporting(0);

	class pfdb {

		function __construct($db) {
			$this->instantiate($db);
		}

		function instantiate($db) {
			$this->dbName = $db;
			$this->loadDb($db);
			$this->loadTables();
			$this->loadRecords();
		}

		function loadDb($db) {
			if(!file_exists($db)) {
				$this->throwError("Cannot find database.");
			}
			else {
				$this->db_file = file_get_contents($db);
			}
		}

		function loadTables() {
			$data = $this->db_file;
			$s = strpos($data, "@tables");
			$e = strpos($data, "}");
			$o = substr($data, $s, $e+1);
			$r = str_replace(" ",null,str_replace("{",null,str_replace("}",null,str_replace("@tables",null,$o))));
			$r = explode(",", $r);
			$this->tables = $r;
			$this->db_file = str_replace($o,null,$this->db_file);
		} 

		function loadRecords() {
			$data = $this->db_file;
			$count = mb_substr_count($this->db_file, "[");
			for ($i=0; $i < $count; $i++) { 
				$s = strpos($data, "[");
				$e = strpos($data, "];");
				$o = substr($data, $s, $e+1);
				$r = str_replace("\"",null,str_replace("];",null,str_replace("[",null,$o)));
				$r = explode(",", $r);
				if(isset($this->record[$r[0]])) {$this->throwError("Two records share the same ID.");}
				$this->record[$r[0]] = $r;
				$this->records[] = array_combine($this->tables, $r);
				$this->db_file = str_replace($o,null,$this->db_file);
				$data = str_replace($o,null,$data);

			}
		}	

		function find($q) {
			$q = explode(" ", strtolower($q));
			$a = $q[0];
			$b = $q[1];
			$c = $q[2];
			$d = $q[3];
			$e = $q[4];
			if($a == "*") {$f = 1;} else {$f = 0;}
			foreach($this->records as $record) {
				if($record[$c] === $e) {
					$g = 1; if($f == 1 ) {return $record;} else {return $record[$a];}
				} else {$g = 0;}
			}
			if($g == 0) {
				$this->throwError("Zero results found");
			}

		}

		function insert($q) {
			$this->loadRecords();
			$q = explode(",", $q);
			if(count($q) == count($this->tables)) {
				if($q[0] == "*") { //Autoincrement
					$last = end($this->records);
					$last = $last[$this->tables[0]];
					$open = file_get_contents($this->dbName);
					if(!isset($this->increment)) {
						$this->increment = intval($last) + 1;
					}
					else {
						$this->increment++;
					}
					$open .= "\n[".$this->increment.",";
					foreach($q as $x) {
						if($x != "*") {
							$open .= "\"$x\",";
						}
					}
					$open .= "];";
					file_put_contents($this->dbName, str_replace(",];", "];", $open));
					
				}
			}
			else {
				$this->throwError("Your query contains " . count($q) ." values and the database has " . count($this->tables) . " tables.");
			}
		}

		function throwError($error) {
			die("<strong><span style=\"color:red;\">PFDB Error</span>: $error</strong>");
		}

	}
?>