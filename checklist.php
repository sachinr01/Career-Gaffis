<style>
table.tbl2 {
    border: 2px solid #343434;
    margin: 10px auto;
    width: 1000px;
    font-family: Arial, sans-serif;
}
.tbl2 th, .tbl2 td {
    border: 1px solid #343434;
    border-collapse: collapse;
    font-size: 14px;
    padding: 8px;
}
th.job_heading {
    font-size: 18px;
    padding: 10px;
    background-color: #f0f0f0;
}
/* Status Badges */
.badge-valid {
    background-color: #d4edda;
    color: #155724;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 12px;
}
.badge-invalid {
    background-color: #f8d7da;
    color: #721c24;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 12px;
}
</style>

<?php

/* -----------------------------
   ZEROBOUNCE API KEY
----------------------------- */
$ZERObounce_API_KEY = "47fd1db9e4b54542b0b0dfea2af04fa3";

/* -----------------------------
   DATABASE CONNECTION
----------------------------- */
$servername = "localhost";
$username   = "gt_recruit";
$password   = "GQ6.2ljQ,sSo";
$dbname     = "gt_recruit";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* -----------------------------
   EMAIL VALIDATION (ZeroBounce)
----------------------------- */
function emailLooksReal($email, $apiKey)
{
    $email = trim($email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<pre>Invalid format: {$email}</pre>";
        exit;
    }

    $url = "https://api.zerobounce.net/v2/validate?api_key={$apiKey}&email=" . urlencode($email);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "<pre>cURL Error: " . curl_error($ch) . "</pre>";
        exit;
    }

    curl_close($ch);

    // 🔍 PRINT RAW RESPONSE
    echo "<pre>RAW API RESPONSE:\n";
    print_r($response);
    echo "</pre>";

    $data = json_decode($response, true);

    // 🔍 PRINT DECODED RESPONSE
    echo "<pre>DECODED RESPONSE:\n";
    print_r($data);
    echo "</pre>";

}

function freeEmailCheck($email)
{
    $email = trim($email);

    // 1. Syntax check
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    // 2. Domain MX check
    $domain = substr(strrchr($email, "@"), 1);

    if (!$domain) {
        return false;
    }

    return checkdnsrr($domain, "MX");
}



/* -----------------------------
   APPLICATION STATUS
----------------------------- */
$status_data = [];
$query_status = "SELECT id, status FROM application_status";
$result_status = $conn->query($query_status);

if ($result_status && $result_status->num_rows > 0) {
    while ($row_status = $result_status->fetch_assoc()) {
        $status_data[$row_status['id']] = $row_status['status'];
    }
}

/* -----------------------------
   JOBS
----------------------------- */
$query_jobs = "SELECT id, title FROM jobs WHERE id IN (15,18,26)";
$result_jobs = $conn->query($query_jobs);

if ($result_jobs && $result_jobs->num_rows > 0) {

    $counter = 1;

    echo "<table class='tbl2' cellpadding='5' cellspacing='0'>
        <thead>
            <tr style='background-color:#333; color:#fff;'>\
                <th>Email</th>
            </tr>
        </thead>
        <tbody>";

    while ($jobs = $result_jobs->fetch_assoc()) {

        $job_id    = $jobs['id'];
        $job_title = $jobs['title'];

        $query_applications = "
            SELECT id, full_name, email, phone, status_id
            FROM job_applications
            WHERE created_at >= '2024-01-01 00:00:00'
            AND job_id = $job_id
            ORDER BY status_id ASC, full_name ASC
            LIMIT 2000
        ";

        $result_applications = $conn->query($query_applications);

        if ($result_applications) {
            while ($applications = $result_applications->fetch_assoc()) {

                $phone_number = substr($applications['phone'], -10);
                $email        = trim($applications['email']);

                /* ---- VALIDATION ---- */
                // $isValid = emailLooksReal($email, $ZERObounce_API_KEY);
                $isValid = freeEmailCheck($email);

                // if ($isValid) {
                //     $email_display_style = "color:green; font-weight:bold;";
                //     $valid_value = 'yes';
                // } else {
                //     $email_display_style = "color:red; text-decoration:line-through;";
                //     $valid_value = 'no';
                // }

                // /* ---- UPDATE DATABASE ---- */
                // $updateQuery = "
                //     UPDATE job_applications 
                //     SET is_valid = '{$valid_value}'
                //     WHERE id = {$applications['id']}
                // ";
                // $conn->query($updateQuery);
                
                if ($isValid) {
                    $email_display_style = "color:green; font-weight:bold;";
                    $valid_value = 'yes';
                } else {
                    $email_display_style = "color:red; text-decoration:line-through;";
                    $valid_value = 'no';
                }
                
                $updateQuery = "
                    UPDATE job_applications 
                    SET is_valid = '{$valid_value}'
                    WHERE id = {$applications['id']}
                ";
                $conn->query($updateQuery);


                $status_name = isset($status_data[$applications['status_id']])
                    ? $status_data[$applications['status_id']]
                    : '-';

                echo "<tr>
                    <td style='{$email_display_style}'>{$email}</td>
                </tr>";

                $counter++;
            }
        }
    }

    echo "</tbody></table>";
}

?>
