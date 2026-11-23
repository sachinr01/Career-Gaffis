<?php

namespace App\Http\Controllers;

use App\InterviewSchedule;
use App\JobApplication;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class APIController extends Controller
{
    
    private $propertyId = "321184813"; // Your GA4 Property ID
    private $apiUrl = "https://analyticsdata.googleapis.com/v1beta/properties/";

    public function getAnalytics()
    {
        // 1. JWT Generation and Authentication (No changes needed)
        $jsonKeyFile = storage_path('app/analytics/service-account.json');

        if (!file_exists($jsonKeyFile)) {
            Log::error('Analytics service account file not found.');
            return response()->json(['error' => 'Analytics service account file not found.'], 500);
        }

        $creds = json_decode(file_get_contents($jsonKeyFile), true);

        if (!isset($creds['client_email']) || !isset($creds['private_key'])) {
            Log::error('Invalid service account file structure.');
            return response()->json(['error' => 'Invalid service account file structure.'], 500);
        }

        $jwt_header = base64_encode(json_encode(['alg'=>'RS256','typ'=>'JWT']));
        $now = time();

        $jwt_claims = base64_encode(json_encode([
            'iss' => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signature = '';
        $privateKey = str_replace('\n', "\n", $creds['private_key']);
        
        openssl_sign("$jwt_header.$jwt_claims", $signature, $privateKey, 'sha256');
        $jwt = "$jwt_header.$jwt_claims." . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        // Get Access Token
        $tokenContext = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded",
                'content' => http_build_query([
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                ])
            ]
        ]);
        
        $response = @file_get_contents("https://oauth2.googleapis.com/token", false, $tokenContext);
        
        if ($response === false) {
             Log::error('Failed to retrieve access token from Google.');
             return response()->json(['error' => 'Failed to retrieve access token from Google.'], 500);
        }

        $access_token = json_decode($response, true)['access_token'] ?? null;
        
        if (!$access_token) {
             Log::error('Access token not found in Google response.');
             return response()->json(['error' => 'Access token not found in Google response.'], 500);
        }

        // --- END AUTHENTICATION ---
        
        // 2. API Call 1: Get Property Totals (MUST SHOW METRICS)
        $metrics = [
            ["name" => "activeUsers"],
            ["name" => "newUsers"],
            ["name" => "sessions"],
            ["name" => "engagementRate"],
            ["name" => "bounceRate"]
        ];
        
        $totalsPayload = json_encode([
            "dateRanges" => [
                ["startDate" => "28daysAgo", "endDate" => "today"]
            ],
            "metrics" => $metrics,
            // NO dimensions key here
        ]);
        
        $totalsData = $this->callAnalyticsAPI($access_token, $totalsPayload);

        if (isset($totalsData['error'])) {
            return response()->json(['error' => 'GA4 Totals API Error: ' . $totalsData['error']], 500);
        }
        
        // Extract total metrics from the GA4 API response (usually in the 'totals' array)
        $totalMetrics = $totalsData['totals'][0]['metricValues'] ?? $totalsData['rows'][0]['metricValues'] ?? [];
        
        $finalTotals = [
            'active_users'    => (int) ($totalMetrics[0]['value'] ?? 0),
            'new_users'       => (int) ($totalMetrics[1]['value'] ?? 0),
            'sessions'        => (int) ($totalMetrics[2]['value'] ?? 0),
            'engagement_rate' => round((float) ($totalMetrics[3]['value'] ?? 0), 4),
            'bounce_rate'     => round((float) ($totalMetrics[4]['value'] ?? 0), 4),
        ];

        // 3. API Call 2: Get City Breakdown (MUST SHOW DIMENSION CITY)
        $cityPayload = json_encode([
            "dateRanges" => [
                ["startDate" => "28daysAgo", "endDate" => "today"]
            ],
            "metrics" => [
                ["name" => "activeUsers"] // Only need activeUsers metric to sort/list by
            ],
            // ✅ Include the "city" dimension
            "dimensions" => [
                ["name" => "city"]
            ]
        ]);
        
        $cityData = $this->callAnalyticsAPI($access_token, $cityPayload);

        if (isset($cityData['error'])) {
            Log::warning('GA4 City API Error: ' . $cityData['error']);
        }
        
        $cityRows = $cityData['rows'] ?? [];

        /* ✅ FORMAT CITY LIST */
        $cityList = [];
        foreach ($cityRows as $row) {
            $cityList[] = [
                'city' => $row['dimensionValues'][0]['value'] ?? 'Unknown',
                'users' => (int) ($row['metricValues'][0]['value'] ?? 0)
            ];
        }

        /* ✅ FINAL STRUCTURED RESPONSE */
        return response()->json([
            'totals' => $finalTotals, // Data from Call 1
            'cities' => $cityList     // Data from Call 2
        ])
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /**
     * Helper function to execute the GA4 API request.
     */
    private function callAnalyticsAPI($accessToken, $payload)
    {
        $url = $this->apiUrl . $this->propertyId . ":runReport";

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Authorization: Bearer $accessToken\r\nContent-Type: application/json",
                'content' => $payload,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
             return ['error' => 'Failed to connect to Google Analytics Data API.'];
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['error'])) {
            return ['error' => $data['error']['message'] ?? 'Unknown API error.'];
        }

        return $data;
    }

    
    
   public function index()
   {
       $schedules = InterviewSchedule::select(
            'interview_schedules.id',
             DB::raw("DATE_FORMAT(CONVERT_TZ(interview_schedules.schedule_date, '+00:00', '+05:30'), '%Y-%m-%d %H:%i:%s') as schedule_date"),
            'interview_schedules.interview_type',
            'interview_schedule_employees.user_id as employee_id',
            'interview_schedules.job_application_id',
            'job_applications.full_name as app_full_name',
            'job_applications.email as app_email',
            'job_applications.phone as app_phone',
            'application_status.status',
            'zoom_meetings.created_by',
            'zoom_meetings.start_link',
            'jobs.title as job_title'
        )
        ->leftJoin('interview_schedule_employees', 'interview_schedule_employees.interview_schedule_id', '=', 'interview_schedules.id')
        ->leftJoin('zoom_meetings', 'zoom_meetings.id', '=', 'interview_schedules.meeting_id')
        ->leftJoin('job_applications', 'job_applications.id', '=', 'interview_schedules.job_application_id')
        ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
        ->leftJoin('application_status', 'application_status.id', '=', 'job_applications.status_id')
        ->with('jobApplication')
        ->distinct('interview_schedules.id')
        ->orderBy('interview_schedules.schedule_date', 'desc')
        ->get();
    
        $schedules->transform(function ($item) {
            $item->schedule_date = \Carbon\Carbon::parse($item->schedule_date)
                ->setTimezone('Asia/Kolkata')
                ->format('Y-m-d H:i:s');
            return $item;
        });

    
    
    
    
        return response()->json($schedules)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

   public function jobApplicationList()
   {
       
       $applyList = JobApplication::select(
            'job_applications.id',
            'job_applications.full_name',
            'job_applications.email',
            'job_applications.phone',
            'job_applications.gender',
            'job_applications.created_at',
            'application_status.status',
            'jobs.title as job_title'
        )
        ->leftJoin('jobs', 'jobs.id', '=', 'job_applications.job_id')
        ->leftJoin('application_status', 'application_status.id', '=', 'job_applications.status_id')
        ->orderBy('job_applications.id', 'DESC')
        ->get();
    
    
        return response()->json($applyList)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            
    }

   public function api_login(Request $request)
   {
    
     
       $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
 
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        return response()->json([
        'status'  => true,
        'message' => 'Login successful',
        'user'    => $user
    ])->header('Access-Control-Allow-Origin', '*')
      ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
      ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
  }
  
   public function redirectToLinkedin()
{
    $clientId = env('LINKEDIN_CLIENT_ID');
    $redirectUri = env('LINKEDIN_REDIRECT_URI');

    $scopes = urlencode('r_liteprofile w_member_social');

    $authUrl = "https://www.linkedin.com/oauth/v2/authorization".
               "?response_type=code".
               "&client_id={$clientId}".
               "&redirect_uri={$redirectUri}".
               "&scope={$scopes}";

    return redirect($authUrl);
}


    
    public function getOrganization(Request $request)
{
    $accessToken = $request->access_token;

    $response = Http::withToken($accessToken)
        ->get('https://api.linkedin.com/v2/organizationalEntityAcls?q=roleAssignee&state=APPROVED');

    return $response->json();
}




   public function handleCallback(Request $request)
   {
    //   dd($request);
        if (!$request->has('code')) {
            return response()->json(['error' => 'Authorization code missing'], 400);
        }

        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'redirect_uri' => env('LINKEDIN_REDIRECT_URI'),
            'client_id' => env('LINKEDIN_CLIENT_ID'),
            'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        ]);

        $data = $response->json();

        if (!isset($data['access_token'])) {
            return response()->json(['error' => 'Unable to get access token', 'details' => $data], 400);
        }

        // Return the access token
        return response()->json(['access_token' => $data['access_token']]);
    }

   
  public function postToLinkedin(Request $request)
{
    $accessToken = $request->access_token;
    $message = $request->message;
    $imageUrl = $request->image_url;

    if (!$accessToken) {
        return response()->json(['error' => 'Access token is required'], 400);
    }

    // Always use organization URN
    $urn = env('LINKEDIN_ORG_ID');

    // Step 1: Prepare base post payload
    $payload = [
        'author' => $urn,
        'lifecycleState' => 'PUBLISHED',
        'specificContent' => [
            'com.linkedin.ugc.ShareContent' => [
                'shareCommentary' => ['text' => $message],
                'shareMediaCategory' => $imageUrl ? 'IMAGE' : 'NONE',
            ],
        ],
        'visibility' => [
            'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
        ],
    ];

    // Step 2: If image exists → upload it to LinkedIn
    if ($imageUrl) {
        // Register image upload
        $register = Http::withToken($accessToken)
            ->post('https://api.linkedin.com/v2/assets?action=registerUpload', [
                'registerUploadRequest' => [
                    'owner' => $urn,
                    'recipes' => ['urn:li:digitalmediaRecipe:feedshare-image'],
                    'serviceRelationships' => [
                        [
                            'relationshipType' => 'OWNER',
                            'identifier' => 'urn:li:userGeneratedContent',
                        ],
                    ],
                ],
            ]);

        if ($register->failed()) {
            return response()->json(['error' => 'Failed to register upload'], 400);
        }

        $uploadData = $register->json();
        $uploadUrl = $uploadData['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
        $asset = $uploadData['value']['asset'];

        // Upload actual image bytes
        $imageContent = file_get_contents($imageUrl);
        Http::withBody($imageContent, 'application/octet-stream')->put($uploadUrl);

        // Attach image to post payload
        $payload['specificContent']['com.linkedin.ugc.ShareContent']['media'] = [
            [
                'status' => 'READY',
                'description' => ['text' => 'Image'],
                'media' => $asset,
                'title' => ['text' => 'Uploaded Image'],
            ],
        ];
    }

    // Step 3: Publish the post
    $response = Http::withToken($accessToken)
        ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
        ->post('https://api.linkedin.com/v2/ugcPosts', $payload);

    if ($response->failed()) {
        return response()->json([
            'error' => 'Publishing failed',
            'details' => $response->json(),
        ], 400);
    }

    return response()->json([
        'success' => true,
        'message' => 'LinkedIn Page Post Published',
        'data' => $response->json()
    ]);
}



}
