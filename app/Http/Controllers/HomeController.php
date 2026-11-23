<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     
       public function __construct()
    {
        $this->middleware('auth');

        parent::__construct();

        $this->pageTitle = __('menu.team');

        $this->pageIcon = 'icon-people';

        view()->share('calling_codes', $this->getCallingCodes());

    }
    public function __construct1()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }




    public function fetchCandidates(Request $request)
    {
        $jobId = $request->input('job_id');
        $statusId = $request->input('status_id');

        // Fetch job applications using DB query builder
        $applications = DB::table('job_applications')
            ->whereIn('job_id', $jobId)
            ->whereIn('status_id', $statusId)
            ->get();

        // Prepare response data
        if ($applications->isNotEmpty()) {
            $data = $applications->map(function ($application) {
                return [
                    'id' => $application->id,
                    'full_name' => $application->full_name,
                    'email' => $application->email,
                    'phone' => $application->phone,
                    'gender' => $application->gender,
                    'dob' => $application->dob,
                ];
            });

            return response()->json([
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'data' => [],
                'view' => '<tr><td colspan="7">No job applications found for the specified job and status.</td></tr>',
            ]);
        }
    }



    public function sendEmails1(Request $request)
    {
        // Retrieve emails and campaign data from the request
        $emails = $request->input('emails');
        $campaignId = $request->input('campaign_id');
        $description = $request->input('description');
        $bcc = $request->input('bcc');
        $cc = $request->input('cc');

        // Validate input data
        if (empty($emails)) {
            Log::error('No emails provided.');
            return response()->json(['success' => false, 'message' => 'No emails provided.']);
        }

        if (empty($campaignId)) {
            Log::error('Campaign details not found.');
            return response()->json(['success' => false, 'message' => 'Campaign details not found.']);
        }

        // Clean up the email addresses
        $cleanEmails = array_map(function ($email) {
            // Remove square brackets and double quotes
            return trim($email, '[]"');
        }, $emails);

        // Construct HTML content for the email
        $htmlContent = '
    <html>
        <head>
            <title>Campaign Details</title>
        </head>
        <body>
            <h2>Campaign Details</h2>
            <p><strong>Campaign Name:</strong> ' . htmlspecialchars($campaignId) . '</p>
            <p><strong>Description:</strong> ' . htmlspecialchars($description) . '</p>
            <p><strong>BCC:</strong> ' . htmlspecialchars($bcc) . '</p>
            <p><strong>CC:</strong> ' . htmlspecialchars($cc) . '</p>
            <footer>
                <p>&copy; ' . date('Y') . ' Your Company Name. All Rights Reserved.</p>
            </footer>
        </body>
    </html>';

        // Email headers
        $separator = md5(time());
        $eol = PHP_EOL;
        $fromEmail = 'yashudhokane@gmail.com'; // Replace with your email address
        $replyTo = 'yashudhokane@gmail.com';   // Replace with your reply-to address

        $headers = "From: " . $fromEmail . $eol;
        $headers .= "Reply-To: " . $replyTo . $eol;
        $headers .= "MIME-Version: 1.0" . $eol;
        $headers .= "Content-Type: text/html; charset=\"UTF-8\"" . $eol;

        // Add CC and BCC if provided
        if (!empty($cc)) {
            $headers .= "CC: " . $cc . $eol;
        }
        if (!empty($bcc)) {
            $headers .= "BCC: " . $bcc . $eol;
        }

        // Loop through each cleaned email and send the message 
        foreach ($cleanEmails as $email) {
            try {
                //Log::info('Attempting to send email to: ' . $email);  //Commented by SR
                //mail($email, 'Campaign Details', $htmlContent, $headers);  //Commented by SR
                //Log::info('Email sent successfully to: ' . $email);  //Commented by SR
            } catch (\Exception $e) {
                //Log::error('Failed to send email to: ' . $email . ' Error: ' . $e->getMessage());  //Commented by SR
                //return response()->json(['success' => false, 'message' => 'Failed to send email to ' . $email]);  //Commented by SR
            }
        }

        return response()->json(['success' => true, 'message' => 'Emails sent successfully.']);
    }


    public function sendEmails(Request $request)
    {

        // Retrieve data from the request
        $emails = $request->input('emails');
        $campaignId = $request->input('campaign_id');
        $description = $request->input('description');
        $bcc = $request->input('bcc');
        $cc = $request->input('cc');

        // Validate input data
        if (empty($emails)) {
            Log::error('No emails provided.');
            return response()->json(['success' => false, 'message' => 'No emails provided.']);
        }

        if (empty($campaignId)) {
            Log::error('Campaign details not found.');
            return response()->json(['success' => false, 'message' => 'Campaign details not found.']);
        }

        // Clean the emails array
        $cleanEmails = array_map(function ($email) {
            return trim($email, '[]"');
        }, $emails);

        // Construct HTML content for the email
        $htmlContent = '
    <html>
        <head>
            <title>Campaign Details</title>
        </head>
        <body>
            <h2>Campaign Details</h2>
            <p><strong>Campaign Name:</strong> ' . htmlspecialchars($campaignId) . '</p>
            <p><strong>Description:</strong> ' . htmlspecialchars($description) . '</p>
            <p><strong>BCC:</strong> ' . htmlspecialchars($bcc) . '</p>
            <p><strong>CC:</strong> ' . htmlspecialchars($cc) . '</p>
            <footer>
                <p>&copy; ' . date('Y') . ' Your Company Name. All Rights Reserved.</p>
            </footer>
        </body>
    </html>';

        // Loop through each cleaned email and send the message
        foreach ($cleanEmails as $email) {
            try {
                Log::info('Attempting to send email to: ' . $email);

                // Send email using Mail facade
                Mail::raw([], function ($message) use ($email, $htmlContent, $bcc, $cc) {
                    $message->to($email)
                        ->subject('Campaign Details')
                        ->setBody($htmlContent, 'text/html');  // HTML email body

                    if (!empty($bcc)) {
                        $message->bcc($bcc);  // Add BCC if provided
                    }

                    if (!empty($cc)) {
                        $message->cc($cc);  // Add CC if provided
                    }
                });

                Log::info('Email sent successfully to: ' . $email);
            } catch (\Exception $e) {
                Log::error('Failed to send email to: ' . $email . ' Error: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to send email to ' . $email]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Emails sent successfully.']);
    }


    public function storeCampaign(Request $request)
    {
        // dd($request->all());
        // Validate the incoming request
        $request->validate([
            // 'campaignName' => 'required|string|max:255',
            // 'description' => 'nullable|string',
            // 'campaignCC' => 'nullable|string|max:255',
            // 'activeStatus' => 'required|in:active,inactive',
            // 'emailTemplate' => 'required|integer|exists:email_templates,id',
            // 'email' => 'nullable|array',
            // 'email.*' => 'nullable|integer|exists:emails,id' // Allowing nullable individual email IDs
        ]);

        try {
            // Insert into the campaigns table
            $campaignId = DB::table('campaigns')->insertGetId([
                'name' => $request->campaignName,
                'subject' => $request->subject,
                'description' => $request->description,
                'cc' => $request->campaignCC,
                'status' => $request->activeStatus === 'active' ? 1 : 0,
                'owner_id' => auth()->id(),
                'template_id' => $request->template_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Filter out null values and insert email records if emails are provided
            $validEmails = array_filter($request->email, function ($email) {
                return !is_null($email);
            });

            if (!empty($validEmails)) {
                foreach ($validEmails as $email) {
                    DB::table('campaign_emails')->insert([
                        'campaign_id' => $campaignId,
                        'email_id' => $email,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Return a successful response
            return response()->json([
                'status' => 'success',
                'message' => 'Campaign and emails successfully created!',
                'campaignId' => $campaignId,
                'emails' => $validEmails
            ]);
        } catch (\Exception $e) {
            // Handle any errors and return a failure response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the campaign.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function updateCampaignpreviousapiupdate(Request $request, $id)
{
    // Validate incoming request
    $request->validate([
        'campaignName' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
        'description' => 'nullable|string',
        'campaignCC' => 'nullable|string|max:255',
        'activeStatus' => 'required|in:active,inactive',
      //'template_id' => 'required|integer|exists:email_templates,id',
        'email' => 'nullable|array',
      //'email.*' => 'nullable|integer|exists:emails,id'
    ]);

    try {
        // Update campaign details
        DB::table('campaigns')->where('id', $id)->update([
            'name' => $request->campaignName,
            'subject' => $request->subject,
            'description' => $request->description,
            'cc' => $request->campaignCC,
            'status' => $request->activeStatus === 'active' ? 1 : 0,
            'template_id' => $request->template_id,
            'updated_at' => now(),
        ]);

        // Remove existing email mappings
        DB::table('campaign_emails')->where('campaign_id', $id)->delete();

        // Filter out null values and insert new email mappings
        $validEmails = array_filter($request->email, function ($email) {
            return !is_null($email);
        });

        if (!empty($validEmails)) {
            $bulkInsert = [];
            foreach ($validEmails as $email) {
                $bulkInsert[] = [
                    'campaign_id' => $id,
                    'email_id' => $email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('campaign_emails')->insert($bulkInsert);
        }

        // Return successful response
        return response()->json([
            'status' => 'success',
            'message' => 'Campaign successfully updated!',
            'campaignId' => $id,
            'emails' => $validEmails
        ]);

    } catch (\Exception $e) {
        // Return error response
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the campaign.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function updateCampaign(Request $request, $id)
{
    $request->validate([
        'campaignName' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
        'description' => 'nullable|string',
        'campaignCC' => 'nullable|string|max:255',
        'activeStatus' => 'required|in:active,inactive',
        'email' => 'nullable|array',
    ]);

    try {
        // ✅ Update campaign
        DB::table('campaigns')->where('id', $id)->update([
            'name' => $request->campaignName,
            'subject' => $request->subject,
            'description' => $request->description,
            'cc' => $request->campaignCC,
            'status' => $request->activeStatus === 'active' ? 1 : 0,
            'template_id' => $request->template_id,
            'updated_at' => now(),
        ]);

        // ✅ Get existing email IDs for this campaign
        $existingEmails = DB::table('campaign_emails')
            ->where('campaign_id', $id)
            ->pluck('email_id')
            ->toArray();

        // ✅ Filter only new emails (not already present)
        $validEmails = array_filter($request->email ?? [], fn($email) => !is_null($email));
        $newEmails = array_diff($validEmails, $existingEmails);

        if (!empty($newEmails)) {
            $bulkInsert = [];
            foreach ($newEmails as $email) {
                $bulkInsert[] = [
                    'campaign_id' => $id,
                    'email_id' => $email,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('campaign_emails')->insert($bulkInsert);
        }

        // ✅ Fetch updated email list
        $emails = DB::table('campaign_emails')
            ->join('job_applications', 'campaign_emails.email_id', '=', 'job_applications.id')
            ->select(
                'job_applications.full_name',
                'job_applications.email',
                'campaign_emails.email_sent',
                'campaign_emails.email_read'
            )
            ->where('campaign_emails.campaign_id', $id)
            ->orderBy('job_applications.id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Campaign successfully updated!',
            'campaignId' => $id,
            'emails' => $emails,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the campaign.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function sendEmailstoCandidate(Request $request)
    {

        try {

            $campaignId = $request->input('campaign_id');
            $templateId = $request->input('template_id');
            $bcc = $request->input('bcc');
            $cc = $request->input('cc');

            $from_email = 'helpdesk@gaffis.com';
            $subject = 'Campaign Details Update';

            $getEmailTemplate = DB::table('campaign_templates')->where('id', $templateId)->first()->html;

            $email = 'gauravgorle9197@gmail.com';

            Mail::raw('Test email content', function ($message) use ($email, $from_email, $subject) {
                $message->to($email)
                    ->from($from_email)
                    ->subject($subject);
            });

            // $getCandidateData = DB::table('campaign_emails')
            //     ->select(
            //         'campaign_emails.id as campaign_email_id',
            //         'job_applications.full_name',
            //         'job_applications.email',
            //         'job_applications.created_at as application_date',
            //         'jobs.title as job_name'
            //     )
            //     ->join('job_applications', 'campaign_emails.email_id', '=', 'job_applications.id')
            //     ->join('jobs', 'jobs.id', '=', 'job_applications.job_id')
            //     ->where('campaign_emails.campaign_id', $campaignId)
            //     ->orderBy('job_applications.id', 'desc')
            //     ->get();

            //     dd($getCandidateData);

            // foreach ($getCandidateData as $candidate) {

            //     try {

            //         $emailBody = str_replace(
            //             ['[NAME]', '[JOBAPPLIED]', '[APPLICATIONDATE]'],
            //             [$candidate->full_name, $candidate->job_name, $candidate->application_date],
            //             $getEmailTemplate
            //         );

            //         Mail::send([], [], function ($message) use ($candidate, $emailBody, $bcc, $cc, $from_email, $subject) {
            //             $message->to($candidate->email)
            //                 ->from($from_email)
            //                 ->subject($subject)
            //                 ->setBody($emailBody, 'text/html');

            //             if (!empty($bcc)) {
            //                 $message->bcc($bcc);
            //             }

            //             if (!empty($cc)) {
            //                 $message->cc($cc);
            //             }
            //         });

            //         DB::table('campaign_emails')
            //             ->where('id', $candidate->campaign_email_id)
            //             ->update(['email_sent' => 1]);

            //     } catch (\Exception $e) {
            //         return response()->json(['success' => false, 'message' => 'Failed to send email to ' . $candidate->email, 'error' => $e->getMessage()], 500);
            //     }
            // }

            return response()->json(['status' => 'Emails sent successfully.'], 200);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'Failed to send emails', 'error' => $e->getMessage()], 500);

        }
    }
public function stats(Request $request)
{
    $query = \DB::table('campaign_emails as ce')
        ->join('job_applications as ja', 'ja.id', '=', 'ce.email_id') // ✅ correct column
        ->leftJoin('campaign_unsubscribed as cu', 'ja.id', '=', 'cu.candidate_id') // ✅ correct column
        ->select(
            'ja.id as candidate_id',
            'ja.full_name',
            'ja.email',
            'ja.phone',
            'ja.error_or_bounced',
            \DB::raw('CASE WHEN cu.candidate_id IS NOT NULL THEN "Yes" ELSE "No" END as unsubscribed'),
            \DB::raw('COUNT(ce.id) as total_emails_sent')
        )
        ->groupBy('ja.id', 'ja.full_name', 'ja.email', 'ja.phone', 'ja.error_or_bounced', 'cu.candidate_id');

    // Filters
    if ($request->filter === 'unsubscribed') {
        $query->whereNotNull('cu.candidate_id');
    }
    if ($request->filter === 'error_or_bounced') {
        $query->where('ja.error_or_bounced', '=', 'yes'); // ✅ values are "yes"/"no"
    }

    $candidates = $query->get();
        
    return view('admin.campaign.stats', compact('candidates'));
}



}
