<?php
class CreateTicket
{ 
	private $username 			= 'root';
	private $servername 		= 'localhost';
	private $password 			= '';
	private $dbname 			= 'support_new';
	private $conn 				= '';
	
	
	public function __construct()
	{
		date_default_timezone_set('Asia/Calcutta');
		ini_set('max_execution_time', 900);
		$this->conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
		if(!$this->conn)
		{
			die("Connection failed: " . mysqli_connect_error());
		}
	}
	
	public function create($last_id)
	{
		$data = $this->get_tickets_data($last_id);
		if($data)
		{
			
			$temp = array();
			foreach($data as $key => $value)
			{
				$final = array();
				$final['ticket']['tid'] 	= $value['id'];
				$final['ticket']['subject'] = $value['subject'];
				$final['ticket']['t_from'] 	= $value['t_from'];
				$final['ticket']['t_to'] 	= $value['t_to'];
				$final['ticket']['time'] 	= $value['created_at'];
				
				$thread_data = $this->get_thread_data($value['id']);
				$temp = array();
				if($thread_data) 
				{
					foreach($thread_data as $t_key => $t_value)
					{
						$attach = array();
						$temp = array();
						$temp['id'] = $t_value['id'];
						$temp['t_to'] = $t_value['t_to'];
						$temp['t_from'] = $t_value['t_from'];
						$temp['subject'] = $t_value['subject'];
						if($t_value['attach']){
							$attach = json_decode($t_value['attach']);
							$temp['attach'] = $attach;
						}else{
							$temp['attach'] = $attach;
						}
						$final['ticket']['thread'][] = $temp;
						file_put_contents('body/threads/'.$t_value['id'].'.txt', $t_value['body']);
						
					}
				}else{
					$final['ticket']['thread'] = $temp;
				}
				$last = $value['id'];
				
				file_put_contents('body/tickets/'.$value['id'].'.txt', json_encode($final));
			}
			echo $last;
		}
	}
	
	function get_tickets_data($last_id = NULL)
	{
		$result = array();
		if($last_id)
		{
			$query = "SELECT * FROM tickets Where id > $last_id LIMIT 20";
			$res = mysqli_query($this->conn, $query);
			if(mysqli_num_rows($res) > 0){
				while($row = mysqli_fetch_assoc($res))
				{
					$result[] = $row;
				}
				return $result;
			}
		}
		return  0;
	}
	
	function get_thread_data($tid = NULL)
	{
		$result = array();
		if($tid)
		{
			$query = "SELECT * FROM threads Where tid = $tid";
			$res = mysqli_query($this->conn, $query);
			if(mysqli_num_rows($res) > 0){
				while($row = mysqli_fetch_assoc($res))
				{
					$result[] = $row;
				}
				return $result;
			}
		}
		return 0;
	}
	
	function get_t_data($id = NULL)
	{
		$data = file_get_contents("body/tickets/".$id.'.txt');
		$result = (array)json_decode($data, true);
		echo '<pre>';
		// print_r($result);
		print_r($result['ticket']['thread']);
	}
	
}
$obj = new CreateTicket();
$obj->create(1);
// $obj->get_t_data(16);

?>