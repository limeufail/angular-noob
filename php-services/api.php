<?php
 class API {
 	const DB_SERVER = "localhost";
	const DB_USER = "root";
	const DB_PASSWORD = "";
	const DB_NAME = "newsdb";

	private $db = NULL;
	private $mysqli = NULL;
	public function __construct(){
		$this->connect();					
	}

	private function connect(){
		$this->mysqli = new mysqli(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB_NAME);		
	}
		
	private function authenticate(){
		if($_SERVER['REQUEST_METHOD'] != "POST"){
			$this->response('',406);
		}
		$uname = $_REQUEST['username'];		
		$password = $_REQUEST['password'];
		if(!empty($uname) && !empty($password))
		{
		// 	//if(filter_var($email, FILTER_VALIDATE_EMAIL)){

	 		$query="SELECT uid, name, uname FROM users WHERE uname = '$uname' AND passwd = '".md5($password)."' LIMIT 1";
	 		$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
	 		
			if($r->num_rows > 0) {
				$result = $r->fetch_assoc();	
				$this->response(json_encode($result), 200);
			}
			$this->response('', 204);	
		// 	//}
		}
		
		$error = array('status' => "Failed", "msg" => "Invalid Username or Password");
		$this->response(json_encode($error), 400);
	}
	
	private function getCategory()
	{	
		if($_SERVER['REQUEST_METHOD'] != "GET"){
			$this->response('',406);
		}
		
		$where = " order by `category_name` desc ";

		$query = "SELECT `category_id`, `category_name` FROM `category` $where";			
		$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		
		if($r->num_rows > 0){
			$result = array();
			while($row = $r->fetch_assoc()){
				$result[] = $row;
			}
			$this->response(json_encode($result), 200); 
		}
		$this->response('',204);
	}


	private function getAllNews()
	{	
		if($_SERVER['REQUEST_METHOD'] != "GET"){
			$this->response('',406);
		}
		
		$where = " order by n.news_title desc ";

		$query = "SELECT n.news_id, n.news_photo, n.news_title, n.news_date, c.category_name, n.news_content FROM `news` n,`category` c WHERE n.news_category_id = c.category_id $where";			
		
		$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		
		if($r->num_rows > 0){
			$result = array();
			while($row = $r->fetch_assoc()){
				$result[] = $row;
			}
			$this->response(json_encode($result), 200); 
		}
		$this->response('',204);
	}

	private function getNews()
	{	
		if($_SERVER['REQUEST_METHOD'] != "GET"){
			$this->response('',406);
		}
		$id = (int)$_REQUEST['id'];
		
		$where = " AND n.news_id = $id ";

		$query = "SELECT n.news_id, n.news_photo, n.news_title, n.news_date, c.category_name, n.news_content FROM `news` n,`category` c WHERE n.news_category_id = c.category_id $where";			
		
		$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
		
		if($r->num_rows > 0){
			$result = array();
			while($row = $r->fetch_assoc()){
				$result[] = $row;
			}
			$this->response(json_encode($result), 200); 
		}
		$this->response('',204);
	}
	
	private function insertNews(){
			if($_SERVER['REQUEST_METHOD'] != "POST"){
				$this->response('',406);
			}

			$data = json_decode(file_get_contents("php://input"),true);
			$column_names = array('news_title', 'news_date', 'news_category_id', 'news_content');
			$keys = array_keys($data);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ 
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $data[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO news(".trim($columns,',').") VALUES(".trim($values,',').")";
			if(!empty($data)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "News Added Successfully.", "data" => $data);
				$this->response(json_encode($success),200);
			}else
				$this->response('',204);
	}
				
	private function updateNews(){
		if($_SERVER['REQUEST_METHOD'] != "POST"){
			$this->response('',406);
		}
		$news = json_decode(file_get_contents("php://input"),true);
		$id = (int)$news['id'];
		$column_names = array('news_title', 'news_date', 'news_category_id', 'news_content');
		$keys = array_keys($news['news']);
		$columns = '';
		$values = '';
		foreach($column_names as $desired_key){ 
		   if(!in_array($desired_key, $keys)) {
		   		$$desired_key = '';
			}else{
				$$desired_key = $news['news'][$desired_key];
			}
			$columns = $columns.$desired_key."='".$$desired_key."',";
		}
		$query = "UPDATE news SET ".trim($columns,',')." WHERE news_id=$id";
		if(!empty($news)){
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			$success = array('status' => "Success", "msg" => "News ".$id." Updated Successfully.", "data" => $news);
			$this->response(json_encode($success),200);
		}else
			$this->response('',204);
	}

	private function deleteNews(){

		$id = (int)$_REQUEST['id'];
		if($id > 0){				
			$query="DELETE FROM news WHERE news_id = $id";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
			$this->response(json_encode($success),200);
		}else
			$this->response('',204);
	}
		
	private function response($data, $status){
		$code = ($status) ? $status : 200;
		header("HTTP/1.1 ".$code." ".$this->getStatus($code));
		header("Content-Type:application/json");
		echo $data;
		exit;
	}

	private function getStatus($code){
		$status = array(
					200 => 'OK',
					201 => 'Created',  
					204 => 'No Content',  
					404 => 'Not Found',  
					406 => 'Not Acceptable',
					500 => 'Internal Server Error');
		return ($status[$code])?$status[$code]:$status[500];
	}

	public function processRequest(){
		$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
		if((int)method_exists($this,$func) > 0)
			$this->$func();
		else
			$this->response('',404);
	}

 }

$api = new API;
$api->processRequest();
?>
