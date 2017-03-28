<?php

	class MysqlConnection{
	
		public $conn;
		public $err = false;

		function __construct(){
			$numargs = func_num_args();
			switch($numargs){
				case 1:
					$this->Connect('localhost', 'root', '24522913', func_get_arg(0));
					break;
				case 4:
					$servname = func_get_arg(0);
					$username = func_get_arg(1);
					$password = func_get_arg(2);;
					$dbname = func_get_arg(3);
					$this->Connect($servname, $username, $password, $dbname);	
					break;
				default:
					break;
			}
		}

		public function Connect($servname, $username, $password, $dbname){
			$this->conn = new mysqli($servname, $username, $password, $dbname);
			if($this->conn->connect_error){
				die("Connection failed". $this->conn->connect_error);
				$err = true;
			}
		}	
		
		public function isDataExist($table, $field, $data){
			$result = $this->Query("SELECT * FROM  ".$table." WHERE ".$field."='".$data."'");
			$bool = ($result->num_rows > 0);
			$result->free();
			return $bool;
		}

		public function Insert($table, $assoc_arr){
			$query_field = 'INSERT INTO '.$table.'(';
			$query_data = ') VALUES (';
			foreach($assoc_arr as $key => $value){
				$query_field .= $key.",";
				$query_data .= "'".$value."',";
			}
			$query_field = substr($query_field, 0, -1);
			$query_data = substr($query_data, 0, -1);
			$query = $query_field.$query_data.')';
			return $this->Query($query);
		}

		public function Retrieve(){
			$numargs = func_num_args();
			switch($numargs){
				case 1:
					$table = func_get_arg(0);
					$result = $this->Query("SELECT * FROM ".$table);
					$arr = $result->fetch_all(MYSQLI_ASSOC);
					$result->free();
					return $arr;
				case 2:
					$table = func_get_arg(0);
					$criteria = func_get_arg(1);
					$query = "SELECT * FROM ".$table." WHERE ";
					foreach($criteria as $key => $value){
						$query .= $key."='".$value."' and ";
					}
					$query = substr($query, 0, -4);
					$result = $this->Query($query);
					$arr = $result->fetch_all(MYSQLI_ASSOC);
					$result->free();
					return $arr;
				case 3:
					break;
			}
		}



		public function GetTableFields($table){
			$result = $this->Query("SELECT * FROM ".$table);		
	        $finfo = $result->fetch_fields();
//			$result->free();
			$arr = array();
			foreach($finfo as $val){
				array_push($arr, $val->name);
			}
			return $arr;
		}
	
		public function ListTableFields($table){
			$arr = $this->GetTableFields($table);
			echo "<ul>\n";
			foreach ($arr as $val) {
				printf("<li>%s</li>",   $val);
			}	
			echo "</ul>\n";
		}

		public function Query($string){
			return $this->conn->query($string);
		}

		public function ShowError(){
			if(mysqli_connect_errno()){
				printf("Connect failed: %s\n", mysqli_connect_error());
			}
		}
	}
?>
