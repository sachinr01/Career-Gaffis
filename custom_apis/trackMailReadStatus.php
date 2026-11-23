<?php require 'db.php';

$campaign_email_id = isset($_REQUEST['campaign_email_id']) ? $_REQUEST['campaign_email_id'] : null;

if (!empty($campaign_email_id)) {
	$updateQuery = "UPDATE campaign_emails SET email_read = 1 WHERE id = {$campaign_email_id}";
	$conn->query($updateQuery);
}

return true;

