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
				$final[$value['id']]['subject'] = $value['subject'];
				$final[$value['id']]['t_from'] = $value['t_from'];
				$final[$value['id']]['t_to'] = $value['t_to'];
				$final[$value['id']]['time'] = $value['created_at'];
				$thread_data = $this->get_thread_data($value['id']);
				if($thread_data)
				{
					foreach($thread_data as $t_key => $t_value)
					{
						$temp = array();
						$temp['id'] = $t_value['id'];
						$temp['t_to'] = $t_value['t_to'];
						$temp['t_from'] = $t_value['t_from'];
						$temp['subject'] = $t_value['subject'];
						
						$attach = json_decode($t_value['attach']);
						if(count($attach) > 0)
							$temp['attach'] = $attach;
						else
							$temp['attach'] = array();
						$final[$value['id']]['thread'][] = $temp;
						file_put_contents('body/threads/'.$t_value['id'].'.txt', $t_value['body']);
					}
				}
				$last = $value['id'];
				
				file_put_contents('body/tickets/'.$value['id'].'.txt', json_encode($final));
			}
			echo $last;
		}
	}
	
	function get_tickets_data($last_id= NULL)
	{
		$result = array();
		if($last_id)
		{
			$query = "SELECT * FROM tickets Where id > $last_id LIMIT 1000";
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
		echo $data;
		echo '<pre>';
		print_r(json_decode($data));
	}
	
}
$obj = new CreateTicket();
// $obj->create(1);
$obj->get_t_data(228289);

?>