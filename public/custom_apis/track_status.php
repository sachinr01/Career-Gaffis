<?php require 'db.php';

//$data = (!empty($data1)) ? $data1 : $data2; 
//$data =	$data1;
//$data['status_id']	=	'1';    
 
 
function get_application_status_data($data_id)
{
	$conn			= 	$GLOBALS['conn'];
	$query 			=	"SELECT * FROM application_status WHERE id = ".$data_id;	
 	
 	$result 		=	$conn->query($query);
	if($result->num_rows > 0){			
 		while( $row	=	$result->fetch_assoc()) {	
  			$status_data	=	$row['status'];
		}
	}
   
	return $status_data;
}


$job_id      	=	$_REQUEST['job_id'];      
$email			=	$_REQUEST['email'];      
$updated_at     =	date("Y-m-d H:i:s"); //2020-03-25 00:00:00


$query 		=	"SELECT * FROM job_applications WHERE email LIKE '".$email."' AND job_id =".$job_id;	
//$query 			=	"SELECT * FROM job_applications WHERE id = 7328";	
$result 		=	$conn->query($query);
if($result->num_rows > 0){			
 	while( $row=$result->fetch_assoc()) {	
 		$application_id	=	$row["id"];
 		$full_name	=	$row["full_name"];
		$email		=	$row["email"];
		$job_id		=	$row["job_id"];
		$status_id		=	$row["status_id"];
		$updated_at		=	$row["updated_at"];
		$current_status		=	get_application_status_data($status_id);  
 	}
	
	$query2 		=	"SELECT * FROM applicant_notes WHERE note_text not like '%Internal Comment%' AND job_application_id =".$application_id." ORDER BY id ASC";	
	$result2 		=	$conn->query($query2);
	
	if($result2->num_rows > 0){	
		while( $row2=$result2->fetch_assoc()) {	
			
			$applicant_notes	.=	"<ul class='track_activity_cls'>";
			if($row2["note_text"]!=""){
				$applicant_notes	.=	"<li>".$row2["note_text"]." <span>".date("dS F Y",strtotime($row2["updated_at"]))."</span></li>";
			}
			$applicant_notes	.=	"</ul>";
			
		}
	}
	
	$return_html	=	'<div class="track_output">
	<ul>
		<li><label>Application ID:</label> '.$application_id.'</li>
		<li><label>Candidate:</label> '.$full_name.'</li>
		<li><label>Application Status:</label> '.ucfirst($current_status).'</li>
		<li><label>Last Modified:</label> '.date("dS F Y",strtotime($updated_at)).'</li>
		<li><label>Activity/Notes:</label>'.$applicant_notes.'</li>
	</ul>
	</div>';
	
 	$response["code"] 	= 	400;   
	$response["status"] = 	"success";  
	$response["view"] 	= 	$return_html;   
	$response["message"]=	"Success!!! Job Track done.";   

}
else {
	
	$return_html	=	'<div class="track_output_no_resut"><div>Details not available. For more information, please contact <a href="mailto: helpdesk@gaffis.com">helpdesk@gaffis.com</a>.</div></div>';
	

	$response["code"] 	= 	400;   
	$response["status"] = 	"success";  
	$response["view"] 	= 	$return_html;   
	$response["message"]=	"Error!!! Details not available.";   

}





echo json_encode($response);


?>