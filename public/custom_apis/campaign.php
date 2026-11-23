<?php

require 'db.php';

header('Access-Control-Allow-Origin: *');  // Allow all domains or specify your domain like 'https://yourdomain.com'

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

header('Access-Control-Allow-Headers: Content-Type, X-CSRF-TOKEN, Authorization');



// Check if the request is a JSON payload

$data = file_get_contents('php://input');

$data = json_decode($data, true); // Decode the JSON input



if (!$data) {

    // If the data is not JSON, fall back to $_POST

    $data = $_POST;

}


$campaign_id = $data['campaign_id'];

$description = $data['description'];

$bcc = $data['bcc'];

$cc = $data['cc'];

$emails = $data['emails']; 

$campaign_name = $data['campaign_name'];

$html = $data['html'];



$applied_date = $data['applied_date'];



// Convert to DateTime object

$date = new DateTime($applied_date);



// Format the date as desired

$applied_date = $date->format('F j, Y'); // Outputs: October 10, 2023



$candidate_name = $data['candidate_name'];

$jobapplication_status = $data['jobapplication_status'];

$positionapply = $data['positionapply'];



// Validate if the campaign_id is not null or empty

if ($campaign_id != null && !empty($campaign_id)) {

    $from_email = 'Gaffis Technologies Pvt. Ltd. <helpdesk@gaffis.com>';

    $reply_to = 'helpdesk@gaffis.com';

    $subject = 'Campaign Details Update';



    // Prepare the list of emails as a string

    $emails_list = implode(", ", $emails);



    // Check if html content exists and prepare message accordingly

    if (!empty($html)) {

        $messageContent = $html; // Use the provided HTML if available

        

        $messageContent = str_replace("[NAME]", $candidate_name, $messageContent);

$messageContent = str_replace("[JOBAPPLIED]", $positionapply, $messageContent);

$messageContent = str_replace("[APPLICATIONDATE]", $applied_date, $messageContent);

// $messageContent = str_replace("[APPLICATIONSTATUS]", $jobapplication_status, $messageContent);

    } else {

        // Default email content if html is not provided

        $messageContent = '<html>

        <head>

        <title>Campaign Details Update</title>

        </head>

        <body bgcolor="#ffffff" style="font-family: sans-serif;font-size: 18px;margin: 0 auto;width: 940px;">

            <div>

                <table style="font-family: sans-serif; font-size: 16px; color:#343434;" align="center" width="880">

                    <tbody>

                        <tr bgcolor="#bb273f">

                            <td style="padding: 10px; color: rgb(255, 255, 255); background-color: #bb273f;font-size: 17px;" align="center"><strong>Campaign Details</strong><br></td>

                        </tr>

                        <tr>

                            <td style="border: 1px solid #cccccc; padding: 18px 30px 10px; font-size: 14px; line-height: 20px;" align="left">

                                <table align="left">

                                    <tr>

                                        <td colspan="2" style="font-size: 15px;line-height: 32px;">

                                            <strong>Campaign Name:</strong> '.$campaign_name.'<br>

                                            <strong>Description:</strong> '.$description.'<br>

                                            <strong>Emails:</strong> '.$emails_list.'<br>

                                            <strong>BCC:</strong> '.$bcc.'<br>

                                            <strong>CC:</strong> '.$cc.'<br><br/>

                                            Best Regards,<br/>HR Team<br/>Gaffis Technologies Pvt. Ltd. | helpdesk@gaffis.com

                                        </td>            

                                    </tr>        

                                </table>    

                            </td>        

                        </tr>        

                        <tr bgcolor="#bb273f">    

                        <td style="padding: 10px; color: rgb(255, 255, 255); background-color: #bb273f;" align="center">Copyright 2025 Gaffis Technologies Pvt. Ltd. All Rights Reserved.</td>                        

                        </tr>                    

                    </tbody>            

                </table>            

            </div>    

        </body>                    

        </html>';

    }



    // Headers for email

    $separator = md5(time());

    $eol = PHP_EOL;

    $headers = "From: " . $from_email . $eol;

    $headers .= 'Reply-To: ' . $reply_to . "\r\n";

    $headers .= "MIME-Version: 1.0" . $eol;

    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;

    $headers .= "Content-Transfer-Encoding: 7bit" . $eol;

    

    // Construct email message

    $message = "--" . $separator . $eol;

    $message .= "Content-Type: text/html; charset=\"iso-8859-1\"" . $eol;

    $message .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;

    $message .= $messageContent . $eol . $eol;







//sachinraut404

    // Loop through the email addresses

   for ($i = 0; $i < 1; $i++) {

    $recipient = 'sachinraut404@gmail.com';

        // Send email using PHP's mail function

      if (mail($recipient, $subject, $message, $headers)) {

    // Success

    $status = "success";

    $status_message = "Campaign details email sent successfully to $recipient.";

} else {

    // Failure, log the error

    $status = "error";

    $status_message = "Failed to send email to " . $recipient;

    error_log("Failed to send email. Subject: $subject, Message: $message, Headers: $headers");

}



    }

} else {

    // If campaign_id is null or empty, return an error response

    $status = "error";

    $status_message = "Campaign ID is required.";

}



// Prepare response array

$response = array();

$response["code"] = ($status == "success") ? 200 : 400;

$response["status"] = $status;

$response["message"] = $status_message;



// Output the response as JSON

echo json_encode($response);

?>

