<?php

class Main extends Auth{
	public $config;
	
	public function __construct(){
		parent::__construct();
		$this->config = parse_ini_file('./data/global_config.cfg');
	}

	public function save($who, $msg, $name='', $email=''){
			$ip = $_SERVER['REMOTE_ADDR'];
			$dt = time();
			switch($who){
				case 'a':
						dataBase::query("INSERT INTO msgs(name,email,msg,ip,datetime) VALUES('{$this->data['admin_name']}','{$this->data['admin_email']}',:msg,:ip,:dt)",array(":msg"=>"$msg",":ip"=>"$ip",":dt"=>"$dt"));
							break;
				case 'g': 	
						if(isset($_COOKIE['UNIQID'])) {
							$uniqid = $this->clear($_COOKIE['UNIQID']);
							dataBase::query("INSERT INTO msgs(name,email,msg,uniqid,ip,datetime) VALUES(:name,:email,:msg,:uniqid,:ip,:dt)",array(":name"=>"$name",":email"=>"$email",":msg"=>"$msg",":uniqid"=>"$uniqid",":ip"=>"$ip",":dt"=>"$dt"));
						} else {
							$uniqid = md5(uniqid());
							setcookie("UNIQID",$uniqid,time()+86400);
							dataBase::query("INSERT INTO msgs(name,email,msg,uniqid,ip,datetime) VALUES(:name,:email,:msg,:uniqid,:ip,:dt)",array(":name"=>"$name",":email"=>"$email",":msg"=>"$msg",":uniqid"=>"$uniqid",":ip"=>"$ip",":dt"=>"$dt"));
						}
						break;
			}
	}

	public function getMessage($start, $perpage){
			if(isset($_SESSION['sort'])){
				switch($_SESSION['sort']){
					case 'ascending': $query = dataBase::query("SELECT id, name, email, msg, uniqid, ip, datetime FROM msgs ORDER BY id ASC LIMIT $start, $perpage",false,true);break;
					case 'descending': $query = dataBase::query("SELECT id, name, email, msg, uniqid, ip, datetime FROM msgs ORDER BY id DESC LIMIT $start, $perpage",false,true);break;
					default: $query = dataBase::query("SELECT id, name, email, msg, uniqid, ip, datetime FROM msgs ORDER BY id DESC LIMIT $start, $perpage",false,true);break;
				}
			} else
				$query = dataBase::query("SELECT id, name, email, msg, uniqid, ip, datetime FROM msgs ORDER BY id DESC LIMIT $start, $perpage",false,true);

			$row = $query->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}
	
	public function deleteMessage($all, $id=''){
			switch($all){
				case 'one': dataBase::query("DELETE FROM msgs WHERE id = {$id}");break;
				case 'all': dataBase::query("DELETE FROM msgs");break;
			}
		}
	
	public function is_uniqid($id){
				$result = dataBase::query("SELECT uniqid FROM msgs WHERE id = {$id}",false,true)->fetch(PDO::FETCH_NUM);
				$uniqid = $_COOKIE["UNIQID"];
				 if($result[0] == $uniqid)
					return true;
				else
					return false;
	}
	
	public function PagCount(){
			$sql = "SELECT COUNT(*) AS count FROM msgs";
			$result = dataBase::query($sql,false,true)->fetch(PDO::FETCH_ASSOC);
		return $result['count'];
	}
	
	public function clear($data){
			$data = trim($data);
			$data = htmlspecialchars($data, ENT_QUOTES);
			if (get_magic_quotes_gpc())
				$data = stripslashes($data);
		return $data;
	}
	
	public function updBans(){
		$data = dataBase::query("SELECT data FROM bans",false,true)->fetch(PDO::FETCH_NUM, true);
		$banip = '';
		foreach($data as $ip){
			$banip .= $ip[0] . "\n";
		}
		file_put_contents($this->config['PATH_TO_FILE_BANS'],$banip);
	}
}
?>