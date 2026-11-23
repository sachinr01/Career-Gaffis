<style>
table.tbl2 {
    border: 2px solid #343434;
     margin: 10px auto;
    width: 800px;
}
.tbl2 th, .tbl2 td {
    border: 1px solid #343434;
    border-collapse: collapse;
	font-size: 15px;
}
th.job_heading {
    font-size: 18px;
    padding: 10px;
}
</style>

<?php 
$servername	= 	"localhost"; 	//YOUR HOST NAME.
$username 	= 	"gt_recruit"; 	//DATABASE USER NAME.
$password 	= 	"GQ6.2ljQ,sSo";  		//DATABASE PASSWORD.
$dbname 	= 	"gt_recruit"; 	//DATABASE NAME.
$conn 		=	mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
	//echo "Connection failed";
} 
else {
	//echo "Connected";
}
 
//APPLICATIONSTATUS 
$query_status 			=	"SELECT id, status FROM application_status WHERE 1 ";	
$result_status 		=	$conn->query($query_status);
if($result_status->num_rows > 0){			
	while( $row_status	=	$result_status->fetch_assoc()) {	
		$status_data[$row_status['id']]	=	$row_status['status'];
	}
}
//print_r($status_data);

  
//Internship - Digital Marketing, SEO Executive Fresher/Intern, Digital Marketing Expert (Junior)
$query_jobs			=	"SELECT id, title FROM jobs WHERE id in (15, 18, 19, 26, 27 )  limit 0, 30 "; 
$result_jobs 		=	$conn->query($query_jobs);
 
if($result_jobs->num_rows > 0){	
    
    	$counter                    =   1;

	echo "<table class='tbl2' cellpadding='5' cellspacing='0'>";
	
		echo "<tr>
				<th width='10%'>#</th>
				<th width='10%'>ID</th>
				<th width='25%'>Name</th>
				<th width='25%'>Email</th>
				<th width='20%'>Mobile</th>
				<th width='20%'>Job Title</th>
				<th width='20%'>Status</th>
			</tr>";
			

 	while( $jobs	=	$result_jobs->fetch_assoc()) {	
		 
 		$job_id 	=	$jobs['id'];
		$job_title 	=	$jobs['title'];
		
			//echo "<tr><th colspan='5' class='job_heading'>$job_title</th></tr>";
		
 			///////$query_applications			=	"SELECT id, full_name, email, phone, status_id FROM job_applications WHERE id <= 9100 AND job_id = ".$job_id." order by status_id ASC, full_name ASC  limit 0, 2000 ";	
  			
  			//After JAN 2024 and all jobs 
  			/*
  			$query_applications			=	"SELECT id, full_name, email, phone, status_id FROM job_applications WHERE created_at >= '2024-01-01 00:00:00' 
   			AND job_id = ".$job_id." 
  			AND ID <= 9340 
   			AND unsubscribed != 'yes' 
  			AND error_or_bounced != 'yes' 
            AND is_valid = 'yes'  
  			order by status_id ASC, full_name ASC  limit 0, 2000 ";	
  			*/
  			
  			$query_applications			=	"SELECT id, full_name, email, phone, status_id FROM job_applications WHERE 1 
   			AND job_id = ".$job_id." 
  			AND ID <= 9340 
   			AND unsubscribed != 'yes' 
  			AND error_or_bounced != 'yes' 
            AND is_valid = 'yes'  
  			order by status_id ASC, full_name ASC  limit 0, 2000 ";	
  		 
           
           /*
            $query_applications			=	"SELECT id, full_name, email, phone, status_id FROM job_applications WHERE  job_id = ".$job_id." 
  			AND ID <= 9398 
   			AND unsubscribed != 'yes' 
  			AND error_or_bounced != 'yes' 
            AND is_valid = 'yes'  
  			order by status_id ASC, full_name ASC  limit 0, 2000 ";	
  			*/
   			
   			$result_applications 		=	$conn->query($query_applications); 
  			
    	
 			while( $applications	=	$result_applications->fetch_assoc()) {	
			
 				$phone_number = substr($applications['phone'], -10);
				
				echo "<tr>
					<td>".$counter."</td>
					<td>".$applications['id']."</td>
					<td>".$applications['full_name']."</td>
					<td>".$applications['email']."</td>
 					<td>".$phone_number."</td>
						<td>".$job_title."</td>
					<td>".$status_data[$applications['status_id']]."</td>
 				</tr>";
 				
 				$counter++;
			
			}
 
	}
	
	echo "</table>";
 	echo "<div class='space20'></div>";
 	echo "<div class='space20'>Last Candidate ID: 9398</div>";
	
}


/* -----------------------------------------
   GET ONLY DUPLICATE IDs (ONE PER EMAIL REMOVED)
----------------------------------------- */
$query_dup = "
    SELECT GROUP_CONCAT(id ORDER BY id ASC) AS ids
    FROM job_applications
    GROUP BY email
    HAVING COUNT(*) > 1
";

$result_dup = $conn->query($query_dup);

$duplicate_ids = [];

if ($result_dup->num_rows > 0) {
    while ($row = $result_dup->fetch_assoc()) {
        $ids = explode(",", $row['ids']);
        array_shift($ids); // remove the first ID (keep original)
        $duplicate_ids = array_merge($duplicate_ids, $ids);
    }
}

$duplicate_ids_string = implode(",", $duplicate_ids);

echo "<div class='space20'>Duplicate Entries: (Those who applied multiple times)</div>";
echo "<div><strong>Duplicate IDs:</strong> $duplicate_ids_string</div>";


/* -----------------------------------------
   GET DUPLICATE EMAILS (EMAIL COUNT > 1)
----------------------------------------- */

/*
$query_dup_emails = "
    SELECT email 
    FROM job_applications 
    GROUP BY email
    HAVING COUNT(*) > 1
";

$result_dup_emails = $conn->query($query_dup_emails);

$duplicate_emails = [];
if ($result_dup_emails->num_rows > 0) {
    while ($row = $result_dup_emails->fetch_assoc()) {
        $duplicate_emails[] = "'" . $row['email'] . "'";
    }
}

if (empty($duplicate_emails)) {
    echo "<div>No duplicate entries found.</div>";
    return;
}

$duplicate_email_list = implode(",", $duplicate_emails);

// FETCH FULL DETAILS OF DUPLICATE ENTRIES

$query_duplicate_details = "
    SELECT 
        ja.id,
        ja.full_name,
        ja.email,
        ja.phone,
        j.title AS job_title,
        ja.status_id
    FROM job_applications ja
    LEFT JOIN jobs j ON ja.job_id = j.id
    WHERE ja.email IN ($duplicate_email_list)
    ORDER BY ja.email, ja.full_name
";

$result_duplicate_details = $conn->query($query_duplicate_details);

//SHOW DUPLICATES IN TABLE
 
if ($result_duplicate_details->num_rows > 0) {

    echo "<table class='tbl2' cellpadding='5' cellspacing='0'>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Job Title</th>
                <th>Status</th>
            </tr>";

    $dup_counter = 1;

    while ($row = $result_duplicate_details->fetch_assoc()) {

        $mobile = substr($row['phone'], -10);
        $status_name = isset($status_data[$row['status_id']]) ? $status_data[$row['status_id']] : '-';

        echo "<tr style='background-color:#fff3cd;'>
                <td>{$dup_counter}</td>
                <td>{$row['id']}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['email']}</td>
                <td>{$mobile}</td>
                <td>{$row['job_title']}</td>
                <td>{$status_name}</td>
              </tr>";

        $dup_counter++;
    }

    echo "</table>";
}

*/

 


?>