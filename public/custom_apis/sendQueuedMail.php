<?php
require 'db.php';

function logError($message)
{
    ////file_put_contents('mail_sender_log.txt', "[" . date("Y-m-d H:i:s") . "] " . $message . "\n", FILE_APPEND); //Commented by SR 
}

// Pick 20 pending emails
$sql = "SELECT * FROM campaign_email_queue WHERE status = 'pending' ORDER BY id ASC LIMIT 5";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id          = $row['id'];
        $campaignId  = $row['campaign_id'];
        $applicantId = $row['applicant_id'];
       $to          = $row['email'];   // Already stored
        $subject     = $row['subject']; // Already stored
        $body        = $row['body'];    // Already stored HTML body

        $from_email = "helpdesk@gaffis.com";
        $from_name  = "Helpdesk @ Gaffis Tech";

        // Build headers
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: $from_name <$from_email>\r\n";
        $headers .= "Reply-To: $from_name <$from_email>\r\n";

        // For testing override:
    //  $to = "yashdhokane890@gmail.com";
              //   $to = "sachinraut404@gmail.com";


        if (mail($to, $subject, $body, $headers, "-f $from_email")) {
            // ✅ Mark queue as sent
            $conn->query("UPDATE campaign_email_queue SET status = 'sent' WHERE id = $id");

            // ✅ Mark campaign_emails as sent
            $conn->query("UPDATE campaign_emails 
                          SET email_sent = 1 
                          WHERE campaign_id = $campaignId AND email_id = $applicantId");

            logError("✅ Sent email to $to (Queue ID: $id)");
        } else {
            
               $conn->query("UPDATE campaign_emails 
                          SET email_sent = 2 
                          WHERE campaign_id = $campaignId AND email_id = $applicantId");
            // ❌ Failed
            $conn->query("UPDATE campaign_email_queue SET status = 'failed' WHERE id = $id");
            logError("❌ Failed to send email to $to (Queue ID: $id)");
        }
    }
} else {
    logError("ℹ️ No pending emails found");
}
