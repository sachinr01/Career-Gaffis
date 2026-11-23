<?php 

$servername	= 	"localhost"; 	//YOUR HOST NAME.

$username 	= 	"gt_recruit"; 	//DATABASE USER NAME.

$password 	= 	"J]D8s51ss+%LJJ5N";  		//DATABASE PASSWORD.

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