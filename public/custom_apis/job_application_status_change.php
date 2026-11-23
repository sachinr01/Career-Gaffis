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

 

$id			      	=	$_REQUEST['id'];      
$status_id      	=	$_REQUEST['application_status_id'];      
$application_comment=	$_REQUEST['application_comment'];      
$internal_comment=	$_REQUEST['internal_comment'];      
$application_notify =	$_REQUEST['application_notify'];      
$user_id			=	$_REQUEST['application_admin_id'];     
$updated_at      	=	date("Y-m-d H:i:s"); //2020-03-25 00:00:00
$application_current_status_id      	=	$_REQUEST['application_current_status_id'];   
$application_admin_id      	=	$_REQUEST['application_admin_id'];   
$current_status		=	get_application_status_data($application_current_status_id);  
$new_status			=	get_application_status_data($status_id); 
$status_comment		=	"Status change from $current_status to $new_status";      


$query 		=	"SELECT * FROM job_applications WHERE id = ".$id;	
$result 	=	$conn->query($query);
if($result->num_rows > 0){			
 	while( $row=$result->fetch_assoc()) {	
 		$full_name	=	$row["full_name"];
		$email		=	$row["email"];
		$job_id		=	$row["job_id"];
	}
}

$query_job 			=	"SELECT * FROM jobs WHERE id = ".$job_id;	
$result_job			=	$conn->query($query_job);
if($result_job->num_rows > 0){			
	$row			=	$result_job->fetch_assoc(); 
	$job_title		=	$row["title"];
}



$query1 			= 	" UPDATE job_applications SET status_id = '$status_id', updated_at = '$updated_at' WHERE id = '$id' ";	
$result1			=	$conn->query($query1);			

$query2 			= 	" INSERT INTO applicant_notes (user_id, job_application_id, note_text, created_at, updated_at) 
VALUES ('$user_id', '$id', '$status_comment', '$updated_at', '$updated_at' ) ";
$rs_insert2			=	$conn->query($query2);	

$query3 			= 	" INSERT INTO applicant_notes (user_id, job_application_id, note_text, created_at, updated_at) 
VALUES ('$user_id', '$id', '$application_comment', '$updated_at', '$updated_at' ) ";
$rs_insert3			=	$conn->query($query3);	

if($internal_comment!=""){
	
	$internal_comment_show	=	"Internal Comment: ".$internal_comment;
 	$query4 			= 	" INSERT INTO applicant_notes (user_id, job_application_id, note_text, created_at, updated_at) 
	VALUES ('$user_id', '$id', '$internal_comment_show', '$updated_at', '$updated_at' ) ";
	$rs_insert4			=	$conn->query($query4);	
	
}



if($application_notify=="yes"){
	
	$from_email     = 'Gaffis Technologies Pvt. Ltd. <helpdesk@gaffis.com>';
	$reply_to       = 'helpdesk@gaffis.com';
	$subject        = 'Job application status has been updated.';

	$messageContent = '<html>
	<head>
	<title>Gaffis Technologies Pvt. Ltd. - Job application status has been updated</title>
	</head>
	<body bgcolor="#ffffff" style="font-family: sans-serif;font-size: 18px;margin: 0 auto;width: 940px;">
		<div>
			<table style="font-family: sans-serif; font-size: 16px; color:#343434;" align="center" width="880">
				<tbody>
					<tr bgcolor="#bb273f">
			<td style="padding: 10px; color: rgb(255, 255, 255); background-color: #bb273f;font-size: 17px;" align="center"><strong>Job application status has been updated</strong><br></td>
					</tr>
					<tr>
						<td style="border: 1px solid #cccccc; padding: 18px 30px 10px; font-size: 14px; line-height: 20px;" align="left">
							<table align="left">
								<tr>
									<td colspan="2" style="font-size: 15px;line-height: 32px;">
										Dear '.$full_name.',
										<br/><br/>Your job application status has been updated for job - '.$job_title.'.
										<br/>Now your job application status is - <strong>'.$new_status.'</strong>.
										<br/><br/><strong>Comment from HR team:</strong> '.$application_comment.'
										<br/><strong>Remark:</strong> '.$status_comment.'
 										<br/><br/>Best Regards,
										<br/>HR Team
										<br/>Gaffis Technologies Pvt. Ltd. | helpdesk@gaffis.com
									</td>			
								</tr>		
							</table>	
						</td>		
					</tr>		
					<tr bgcolor="#bb273f">	
					<td style="padding: 10px; color: rgb(255, 255, 255); background-color: #bb273f;" align="center">Copyright 2023 Gaffis Technologies Pvt. Ltd. All Right Reserved.</td>						
					</tr>					
				</tbody>			
			</table>			
		</div>	
	</body>					
	</html>';


	// Headers for attachment 
	$separator = md5(time());
	$eol = PHP_EOL;
	$from       = $from_email;
	// Headers for attachment 
	$headers  = "From: ".$from.$eol;
	$headers .= 'Reply-To: '.$reply_to . "\r\n";
	$headers .= "MIME-Version: 1.0".$eol; 
	$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol; // see below 
	$headers .= "Content-Transfer-Encoding: 7bit".$eol;
	$message = "--".$separator.$eol;
	$message .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
	$message .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
	$message .= $messageContent.$eol.$eol;

	// Send email 
	mail($email, $subject, $message, $headers);

}

$response["code"] 	= 	400;   
$response["status"] = 	"success";  
$response["view"] 	= 	" Success!!! ".$status_comment;   
$response["message"]=	"Success!!! Job application status change is complete.";   

echo json_encode($response);


?>