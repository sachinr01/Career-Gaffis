<?php
require 'db.php';

/**
 * Path to single log file (same directory as this script)
 */
define('LOG_FILE', __DIR__ . '/mail_sender_log.txt');

function logError($message)
{
    // Use LOCK_EX to avoid clobbering from concurrent processes
    $line = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL;
    file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);
}

/**
 * Pretty print JSON safely
 */
function prettyJson($data) {
    $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) return json_encode($data); // fallback
    $pretty = json_encode(json_decode($json), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return $pretty !== false ? $pretty : $json;
}

// Pick 5 pending emails
$sql = "SELECT * FROM campaign_email_queue WHERE status = 'pending' ORDER BY id ASC LIMIT 5";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $id          = $row['id'];
        $campaignId  = $row['campaign_id'];
        $applicantId = $row['applicant_id'];
        $to          = $row['email'];
        $subject     = $row['subject'];
        $body        = $row['body'];

        // SocketLabs API details
        $serverId = "24400";
        $apiKey   = "q3CDy9a5L2PrTt4b7YKe";
        $endpoint = "https://inject.socketlabs.com/api/v1/email";

        $payload = [
            "ServerId" => $serverId,
            "ApiKey"   => $apiKey,
            "Messages" => [
                [
                    "To"       => [["EmailAddress" => $to]],
                    "From"     => ["EmailAddress" => "helpdesk@gaffis.com", "FriendlyName" => "Helpdesk @ Gaffis Tech"],
                    "Subject"  => $subject,
                    "HtmlBody" => $body
                ]
            ]
        ];

        // init curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        $curlErrNo = curl_errno($ch);
        $curlErrMsg = $curlErrNo ? curl_error($ch) : '';
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Build debug log block
        $logBlock  = "=== Mail Attempt ===" . PHP_EOL;
        $logBlock .= "Queue ID: $id | Campaign ID: $campaignId | Applicant ID: $applicantId" . PHP_EOL;
        $logBlock .= "To: $to" . PHP_EOL;
        $logBlock .= "Subject: " . ($subject ?: '(empty)') . PHP_EOL;
        $logBlock .= "HTTP Code: " . ($httpCode !== null ? $httpCode : 'N/A') . PHP_EOL;
        $logBlock .= "cURL ErrNo: " . ($curlErrNo ?: '0') . PHP_EOL;
        $logBlock .= "cURL ErrMsg: " . ($curlErrMsg ?: '(none)') . PHP_EOL;
        $logBlock .= "SocketLabs Response:" . PHP_EOL;
        $logBlock .= ($response !== false ? $response : '(no response)') . PHP_EOL;
        $logBlock .= "Payload Sent (pretty JSON):" . PHP_EOL;
        $logBlock .= prettyJson($payload) . PHP_EOL;
        $logBlock .= "=== End Attempt ===" . PHP_EOL . PHP_EOL;

        // Write debug details to log file
        logError($logBlock);

        // Decide success/failure based on HTTP code & ErrorCode in response
        $isSuccess = false;
        if ($httpCode == 200) {
            // Attempt to parse JSON and check ErrorCode
            $decoded = @json_decode($response, true);
            if (is_array($decoded)) {
                // Many SocketLabs responses include an ErrorCode at top level or per message
                if ((isset($decoded['ErrorCode']) && intval($decoded['ErrorCode']) === 0)
                    || (isset($decoded['Messages'][0]['ErrorCode']) && intval($decoded['Messages'][0]['ErrorCode']) === 0)) {
                    $isSuccess = true;
                }
            } else {
                // If not JSON, just check presence of "ErrorCode":0 string as fallback
                if (strpos($response, '"ErrorCode":0') !== false) {
                    $isSuccess = true;
                }
            }
        }

        if ($isSuccess) {
            // ✅ Mark as sent
            $conn->query("UPDATE campaign_email_queue SET status = 'sent' WHERE id = " . intval($id));
            $conn->query("UPDATE campaign_emails SET email_sent = 1 WHERE campaign_id = " . intval($campaignId) . " AND email_id = " . intval($applicantId));
            logError("SUCCESS: Sent via SocketLabs to $to (Queue ID: $id)");
        } else {
            // ❌ Mark failed
            $conn->query("UPDATE campaign_email_queue SET status = 'failed' WHERE id = " . intval($id));
            $conn->query("UPDATE campaign_emails SET email_sent = 2 WHERE campaign_id = " . intval($campaignId) . " AND email_id = " . intval($applicantId));
            logError("FAILURE: Sending via SocketLabs to $to (Queue ID: $id). See debug block above for details.");
        }
    }
} else {
    logError("ℹ️ No pending emails found");
}
