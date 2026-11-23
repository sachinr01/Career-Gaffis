<?php
require 'db.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

function logError($message)
{
    //file_put_contents('error.txt', "[" . date("Y-m-d H:i:s") . "] " . $message . "\n", FILE_APPEND);  //Commented by SR
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if (empty($data['campaign_id'])) {
    $msg = "Campaign ID is required.";
    logError($msg);
    echo json_encode(["code" => 400, "status" => "error", "message" => $msg]);
    exit;
}

$campaignId   = (int)$data['campaign_id'];
$templateId   = !empty($data['template_id']) ? (int)$data['template_id'] : null;
$bcc          = $data['bcc'] ?? null;
$cc           = $data['cc'] ?? null;
$selectedIds  = $data['selected_ids'] ?? [];

if (empty($selectedIds)) {
    $msg = "No candidate IDs selected.";
    logError($msg);
    echo json_encode(["code" => 400, "status" => "error", "message" => $msg]);
    exit;
}

// Convert IDs to safe list
$idsStr = implode(',', array_map('intval', $selectedIds));

// Campaign subject
$subjectRow = $conn->query("
    SELECT subject 
    FROM campaigns 
    WHERE id = {$campaignId} 
      AND template_id = {$templateId}
")->fetch_assoc();
$subject = $subjectRow['subject'] ?? 'Campaign Details Update';

// Email Template
$sqlTemplate = "SELECT html FROM campaign_templates WHERE id = {$templateId}";
$result = $conn->query($sqlTemplate);
if (!$result || $result->num_rows === 0) {
    $msg = "Email template not found for ID: {$templateId}";
    logError($msg);
    echo json_encode(["code" => 404, "status" => "error", "message" => $msg]);
    exit;
}
$templateRow = $result->fetch_assoc();
$getEmailTemplate = $templateRow['html'];

// Fetch candidate data
$sqlCandidates = "
    SELECT
        ce.id AS campaign_email_id,
        ce.email_id,
        ja.id AS applicant_id,
        ja.full_name AS applicant_name,
        ja.email AS applicant_email,
        ja.phone AS applicant_phone,
        ja.gender AS applicant_gender,
        aps.status AS applicant_status,
        DATE_FORMAT(ja.created_at, '%e %M %Y') AS application_date,
        j.id AS job_id,
        j.title AS job_title,
        j.job_description,
        j.job_requirement,
        j.job_highlight,
        ce.email_sent
    FROM campaign_emails ce
    JOIN job_applications ja ON ce.email_id = ja.id
    JOIN application_status aps ON ja.status_id = aps.id
    JOIN jobs j ON j.id = ja.job_id
    WHERE ce.campaign_id = {$campaignId}
      AND ja.unsubscribed != 'yes'
      AND ja.id IN ({$idsStr})
    ORDER BY ja.id DESC
";

$getCandidateData = $conn->query($sqlCandidates);
if (!$getCandidateData || $getCandidateData->num_rows === 0) {
    $msg = "No candidates found for the selected IDs.";
    logError($msg);
    echo json_encode(["code" => 404, "status" => "error", "message" => $msg]);
    exit;
}

$success = [];
$failed  = [];

while ($candidate = $getCandidateData->fetch_assoc()) {
    logError("Candidate Fetched: " . print_r($candidate, true));

    // ⏭ Skip if already sent
    if ($candidate['email_sent'] == 1) {
        logError("⏭ Skipped {$candidate['applicant_email']} (already sent).");
        continue;
    }

    // Build email body with placeholders
    $emailBody = str_replace(
        [
            '[APPLICANT_ID]',
            '[APPLICANT_NAME]',
            '[APPLICANT_EMAIL]',
            '[APPLICANT_PHONE]',
            '[APPLICANT_GENDER]',
            '[APPLICANT_STATUS]',
            '[JOB_ID]',
            '[JOB_TITLE]',
            '[JOB_DESCRIPTION]',
            '[JOB_REQUIREMENT]',
            '[JOB_HIGHLIGHT]',
            '[APPLICATIONDATE]',
            '[CAMPAIGN_EMAIL_ID]'
        ],
        [
            $candidate['applicant_id'],
            $candidate['applicant_name'],
            $candidate['applicant_email'],
            $candidate['applicant_phone'],
            $candidate['applicant_gender'],
            $candidate['applicant_status'],
            $candidate['job_id'],
            $candidate['job_title'],
            $candidate['job_description'],
            $candidate['job_requirement'],
            $candidate['job_highlight'],
            $candidate['application_date'],
            $candidate['campaign_email_id']
        ],
        $getEmailTemplate
    );

    // Tracking pixel
    $trackImg = '<img src="https://careers.gaffis.com/custom_apis/trackMailReadStatus.php?campaign_email_id=' 
                . $candidate['campaign_email_id'] . '" alt="" width="1" height="1">';
    $emailBody .= '<br>' . $trackImg;

    // Queue insert
  $stmt = $conn->prepare("
    INSERT INTO campaign_email_queue (campaign_id, applicant_id, email, subject, body, status)
    VALUES (?, ?, ?, ?, ?, 'pending')
");
$stmt->bind_param("iisss", 
    $campaignId, 
    $candidate['email_id'],   // <-- use email_id here
    $candidate['applicant_email'], 
    $subject, 
    $emailBody
);


    if ($stmt->execute()) {
        // Update campaign_emails
        $update = $conn->prepare("
            UPDATE campaign_emails 
            SET email_sent = 1, updated_at = NOW()
            WHERE campaign_id = ? AND email_id = ?
        ");
        $update->bind_param("ii", $campaignId, $candidate['email_id']);

        if ($update->execute()) {
            $success[] = $candidate;
            logError("✅ Queued {$candidate['applicant_email']} and marked sent.");
        } else {
            $failed[] = $candidate;
            logError("⚠️ Queued {$candidate['applicant_email']} but failed to update campaign_emails.");
        }
    } else {
        $failed[] = $candidate;
        logError("❌ Failed to queue {$candidate['applicant_email']}.");
    }
}



// Response
echo json_encode([
    "code"   => 200,
    "status" => "success",
    "message"=> "Emails queued successfully.",
    "summary"=> [
        "total_selected" => count($selectedIds),
        "queued"         => count($success),
        "failed"         => count($failed),
        "success_data"   => $success,
        "failed_data"    => $failed
    ]
]);
