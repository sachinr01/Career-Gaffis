<?php



namespace App\Http\Controllers\Admin;



use App\EmailSetting;

use App\Helper\Files;   
use App\Helper\smtpSetting; 
use App\Helper\Reply;

use App\Http\Requests\StoreTeam;

use App\Http\Requests\UpdateTeam;

use App\Notifications\NewUser;

use App\Role;

use App\RoleUser;

use App\User;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;

use Symfony\Component\HttpKernel\Tests\HttpCache\StoreTest;

use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\DB;



class AdminCampaignController extends AdminBaseController
{

    public function __construct()
    {

        parent::__construct();

        $this->pageTitle = __('menu.team');

        $this->pageIcon = 'icon-people';

        view()->share('calling_codes', $this->getCallingCodes());

    }
public function changeStatus(Request $request, $id)
{
    $campaign = DB::table('campaigns')->where('id', $id)->first();

    if (!$campaign) {
        return response()->json(['status' => 'error', 'message' => 'Campaign not found']);
    }

    DB::table('campaigns')
        ->where('id', $id)
        ->update(['status' => $request->status]);

    return response()->json(['status' => 'success', 'message' => 'Campaign status updated successfully']);
}



    public function index()
    { 

        abort_if(!$this->user->cans('view_team'), 403);



        // Fetch campaigns data using the provided query

        $campaigns = DB::table('campaigns as cp')

            ->select('cp.id as campaign_id', 'cp.name as campaign_name', 'cp.template_id', 'cp.status', 'cpt.title as template_name', DB::raw('count(*) as total_emails'))

            ->join('campaign_templates as cpt', 'cp.template_id', '=', 'cpt.id')

            ->join('campaign_emails as cpe', 'cp.id', '=', 'cpe.campaign_id')

            ->groupBy('cp.id', 'cp.name', 'cp.template_id', 'cpt.title')

            ->orderByDesc('cp.id')

            ->get();



        // Fetch users data

        $users = User::all();



        // Add both campaigns and users to $this->data array

        $this->data['campaigns'] = $campaigns;

        $this->data['users'] = $users;



        return view('admin.campaign.index', $this->data);

    }



    public function fetchCandidates($jobId, $statusId)
    {

        // Fetch job applications using DB query builder

        $applications = DB::table('job_applications')

            ->where('job_id', $jobId)

            ->where('status_id', $statusId)

            ->get();



        // Check if applications are found

        if ($applications->isNotEmpty()) {

            // Start HTML table

            echo '<table border="1" cellpadding="5" cellspacing="0">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Full Name</th>

                        <th>Email</th>

                        <th>Phone</th>

                        <th>Gender</th>

                        <th>Date of Birth</th>

                        <th>Country</th>

                        <th>State</th>

                        <th>City</th>

                        <th>Photo</th>

                        <th>Rating</th>

                        <th>Skills</th>

                        <th>Skype ID</th>

                        <th>Cover Letter</th>

                        <th>Job ID</th>

                        <th>Status ID</th>

                        <th>Column Priority</th>

                        <th>Created At</th>

                        <th>Updated At</th>

                        <th>Deleted At</th>ed

                    </tr>

                </thead>

                <tbody>';



            // Loop through each application and display in table format

            foreach ($applications as $application) {

                echo '<tr>';

                echo '<td>' . e($application->id) . '</td>';

                echo '<td>' . e($application->full_name) . '</td>';

                echo '<td>' . e($application->email) . '</td>';

                echo '<td>' . e($application->phone) . '</td>';

                echo '<td>' . e($application->gender) . '</td>';

                echo '<td>' . e($application->dob) . '</td>';

                echo '<td>' . e($application->country) . '</td>';

                echo '<td>' . e($application->state) . '</td>';

                echo '<td>' . e($application->city) . '</td>';

                echo '<td>' . e($application->photo) . '</td>';

                echo '<td>' . e($application->rating) . '</td>';

                echo '<td>' . e($application->skills) . '</td>';

                echo '<td>' . e($application->skype_id) . '</td>';

                echo '<td>' . e($application->cover_letter) . '</td>';

                echo '<td>' . e($application->job_id) . '</td>';

                echo '<td>' . e($application->status_id) . '</td>';

                echo '<td>' . e($application->column_priority) . '</td>';

                echo '<td>' . e($application->created_at) . '</td>';

                echo '<td>' . e($application->updated_at) . '</td>';

                echo '<td>' . e($application->deleted_at) . '</td>';

                echo '</tr>';

            }



            // End table

            echo '</tbody></table>';

        } else {

            // If no applications are found, display a message

            echo '<p>No job applications found for the specified job and status.</p>';

        }

    }



    public function create()
    {

        abort_if(!$this->user->cans('add_team'), 403);



        // Fetch all roles

        $this->roles = Role::all();



        // Fetch the first email setting

        $this->smtpSetting = EmailSetting::first();



        // Fetch active jobs with ID and title

        $this->jobs = DB::table('jobs')->where('status', 'active')->select('id', 'title')->get();



        // Fetch all application statuses with ID and name

        $this->statuses = DB::table('application_status')->select('id', 'status')->get();



        // Fetch all templates with their IDs and names

        $this->templates = DB::table('campaign_templates')->select('id', 'title')->get();



        // Pass the data to the view

        return view('admin.campaign.create', $this->data);

    }







    public function store(StoreTeam $request)
    {

        dd(1);

        // abort_if(! $this->user->cans('add_team'), 403);



        // Validate the request

        $data = $request->validate([

            'name' => 'required|string|max:255',

            'description' => 'nullable|string',

            'bcc' => 'nullable|string',

            'cc' => 'nullable|string',

            'template_id' => 'nullable|integer',

            'sms_template_id' => 'nullable|integer',

            'smtp_server_id' => 'nullable|integer',

            'sms_server_id' => 'nullable|integer',

            'owner_id' => 'nullable|integer',

            'group_id' => 'nullable|integer',

            'status' => 'required|string',

            'type' => 'nullable|string',

        ]);



        // Insert data using DB raw method

        DB::table('campaigns')->insert([

            'name' => $data['name'],

            'description' => $data['description'],

            'bcc' => $data['bcc'],

            'cc' => $data['cc'],

            'template_id' => $data['template_id'],

            'sms_template_id' => $data['sms_template_id'],

            'smtp_server_id' => $data['smtp_server_id'],

            'sms_server_id' => $data['sms_server_id'],

            'owner_id' => $data['owner_id'],

            'group_id' => $data['group_id'],

            'status' => $data['status'],

            'type' => $data['type'],

            'created_at' => now(),

            'updated_at' => now(),

        ]);



        // Redirect to the index route with a success message

        return redirect()->route('admin.campaign.index')->with('success', __('messages.createdSuccessfully'));

    }



    public function edit1($id)
    {

        abort_if(!$this->user->cans('edit_team'), 403);



        if ($id == $this->user->id) {

            abort(403);

        }



        $this->roles = Role::all();

        $this->team = User::find($id);

        return view('admin.campaign.edit', $this->data);

    }
public function edit($id)
{
    $roles = Role::all();
    $smtpSetting = EmailSetting::first();
    $jobs = DB::table('jobs')->where('status', 'active')->select('id', 'title')->get();
    $statuses = DB::table('application_status')->select('id', 'status')->get();
    $templates = DB::table('campaign_templates')->select('id', 'title')->get();

    // Fetch the campaign details by ID
    $campaign = DB::table('campaigns')->where('id', $id)->first();

    // Fetch related campaign emails
  $selectedEmails = DB::table('campaign_emails')
                    ->where('campaign_id', $id)
                    ->pluck('email_id')
                    ->toArray();

$selectedJobs = DB::table('job_applications')
                  ->whereIn('id', $selectedEmails)
                  ->pluck('job_id')
                  ->toArray();
                  
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



    // Fetch related statuses for this campaign
    $selectedStatuses = DB::table('job_applications')
                           ->whereIn('id', $selectedEmails)
                            ->pluck('status_id')
                            ->toArray();

    // Fetch campaigns list if needed
    $campaigns = DB::table('campaigns')->select('id', 'name')->get();

    // Fetch users
    $users = User::all();

    // Add all to $this->data array
    $this->data['roles'] = $roles;
    $this->data['smtpSetting'] = $smtpSetting;
    $this->data['jobs'] = $jobs;
    $this->data['statuses'] = $statuses;
    $this->data['templates'] = $templates;
    $this->data['campaign'] = $campaign;
    $this->data['selectedEmails'] = $selectedEmails;
     $this->data['selectedJobs'] = $selectedJobs;
    $this->data['selectedStatuses'] = $selectedStatuses;
    $this->data['campaigns'] = $campaigns;
    $this->data['users'] = $users;
     $this->data['emails'] = $emails;

    return view('admin.campaign.edit', $this->data);
}



    public function show($id)
    {
       

        abort_if(!$this->user->cans('edit_team'), 403);

        if ($id == $this->user->id) {

            abort(403);

        }


        // Fetch roles and team data

        $this->roles = Role::all();

        $this->team = User::find($id);


        // Fetch campaign details and join with campaign_templates to get the HTML column

        $campaign = DB::table('campaigns as cp')

            ->leftJoin('campaign_templates as cpt', 'cp.template_id', '=', 'cpt.id')

            ->where('cp.id', $id)

            ->select('cp.*', 'cpt.html as template_html','cpt.title as template_name')

            ->first();

        // Fetch campaign emails and job application details
        $bounceCount = DB::table('campaign_emails')
    ->where('campaign_id', $id)
    ->where('email_sent', 2)
    ->count();


  $emails = DB::table('campaign_emails as ce')
    ->join('job_applications as ja', 'ce.email_id', '=', 'ja.id')
    ->join('jobs as j', 'ja.job_id', '=', 'j.id')
    ->select(
        'ja.id',
        'ja.full_name',
        'ja.email',
         'ce.campaign_id',
        'ce.email_sent',
        'ce.email_read',
        'j.title as job_title',
        'ce.created_at as sent_date' // ✅ only campaign_emails created_at
    )
    ->where('ce.campaign_id', $id)
    ->orderBy('ja.id', 'desc')
    ->get();
$emailsDuplicateOnly = DB::table('campaign_emails as ce')
    ->join('job_applications as ja', 'ce.email_id', '=', 'ja.id')
    ->join('jobs as j', 'ja.job_id', '=', 'j.id')
    ->select(
        'ja.id',
        'ja.full_name',
        'ja.email',
        'j.title as job_title',
        'ce.email_sent',
        'ce.email_read',
        'ce.created_at as sent_date'
    )
    ->where('ce.campaign_id', $id)
    ->whereIn('ja.email', function ($q) use ($id) {
        $q->select('ja2.email')
            ->from('campaign_emails as ce2')
            ->join('job_applications as ja2', 'ce2.email_id', '=', 'ja2.id')
            ->where('ce2.campaign_id', $id)
            ->groupBy('ja2.email')
            ->havingRaw('COUNT(*) > 1'); // 🔥 Only duplicates
    })
    ->orderBy('ja.email')
    ->get();

$duplicateIds = DB::table('campaign_emails as ce')
    ->join('job_applications as ja', 'ce.email_id', '=', 'ja.id')
    ->where('ce.campaign_id', $id)
    ->groupBy('ja.email')
    ->havingRaw('COUNT(*) > 1')   // Only emails that appear more than once
    ->pluck('ja.id')
    ->toArray();
    $duplicateString = implode(',', $duplicateIds);

//dd($duplicateString);
//dd($duplicateIds);

$sentEmails = DB::table('campaign_emails as ce')
    ->join('job_applications as ja', 'ce.email_id', '=', 'ja.id')
    ->select(
        'ce.id',
        'ce.campaign_id',
        'ce.email_id',
        'ce.email_sent',
        'ce.email_read',
        'ja.full_name',
        'ja.email',
        'ce.created_at'
    )
    ->where('ce.campaign_id', $id)
    ->where('ce.email_sent', 1)
    ->orderBy('ce.email_sent', 'asc')
    ->get();
   
    $sentCount = DB::table('campaign_emails')
    ->where('campaign_id', $id)
    ->where('email_sent', 1)
    ->count();
     $unsentCount = DB::table('campaign_emails')
    ->where('campaign_id', $id)
    ->where('email_sent', 0)
    ->count();
    
        $readCount = DB::table('campaign_emails')
    ->where('campaign_id', $id)
    ->where('email_read', 1)
    ->count();
    
    //  $unreadCount = DB::table('campaign_emails')
    // ->where('campaign_id', $id)
    // ->where('email_read', 0)
    // ->count();
$unreadCount = max(0, $sentCount - $readCount);




        // Pass all the necessary data to the view

    return view('admin.campaign.view', array_merge($this->data, compact('bounceCount','emailsDuplicateOnly','campaign', 'emails','sentEmails','unsentCount','sentCount','readCount','unreadCount')));

    }
public function update(Request $request, $id)
{
    // Validate incoming request
    $request->validate([
     //   'campaignName' => 'required|string|max:255',
      //  'subject' => 'required|string|max:255',
      //  'description' => 'nullable|string',
     //   'campaignCC' => 'nullable|string|max:255',
     //   'activeStatus' => 'required|in:active,inactive',
     //  'template_id' => 'required|integer|exists:email_templates,id',
      //  'email' => 'nullable|array',
      //  'email.*' => 'nullable|integer|exists:emails,id'
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

    public function update1(UpdateTeam $request, $id)
    {



        $user = User::find($id);

        $user->name = $request->name;

        $user->email = $request->email;



        if ($request->password != '') {

            $user->password = Hash::make($request->password);

        }



        if (($request->mobile != $user->mobile || $request->calling_code != $user->calling_code) && $user->mobile_verified == 1) {

            $user->mobile_verified = 0;

        }



        $user->mobile = $request->mobile;

        $user->calling_code = $request->calling_code;



        if ($request->hasFile('image')) {

            $user->image = Files::uploadLocalOrS3($request->image, 'profile');

        } else {

            Files::deleteFile($user->image, 'profile');

            $user->image = null;

        }

        $user->save();



        //attach role

        RoleUser::where('user_id', $id)->delete();

        $user->roles()->attach($request->role_id);



        return Reply::redirect(route('admin.campaign.index'), __('menu.team') . ' ' . __('messages.updatedSuccessfully'));

    }





    public function data()
    {



        abort_if(!$this->user->cans('view_team'), 403);



        $users = User::all();

        $roles = Role::all();

        $admin = $users->filter(function ($user) {

            return $user->hasRole('admin');

        })->sortBy('created_at')->first();



        return DataTables::of($users)

            ->addColumn('action', function ($row) use ($admin) {

                $action = '';



                //do not allow user to change own details
    
                if ($row->id == $this->user->id) {

                    return $action;

                }



                if ($row->id != $admin->id && $this->user->cans('edit_team')) {

                    $action .= '<a href="' . route('admin.campaign.edit', [$row->id]) . '" class="btn btn-primary btn-circle"

                      data-toggle="tooltip" onclick="this.blur()" data-original-title="' . __('app.edit') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                }



                if ($row->id != $admin->id && $this->user->cans('delete_team')) {

                    $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"

                      data-toggle="tooltip" onclick="this.blur()" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';

                }

                return $action;

            })

            ->addColumn('role_name', function ($row) use ($roles, $admin) {



                //do not allow user to change own role
    
                if ($row->id == $admin->id) {

                    return $row->role->role->display_name;

                }

                if ($row->id == $this->user->id) {

                    return $row->role->role->display_name;

                }



                if (!$this->user->cans('edit_team')) {

                    return $row->role->role->display_name;

                }



                $roleOption = '<select name="role_id" class="form-control role_id" data-row-id="' . $row->id . '">';

                foreach ($roles as $role) {

                    $roleOption .= '<option ';

                    if ($row->id != $admin->id && $row->role->role->id == $role->id) {



                        $roleOption .= ' selected ';

                    }



                    $roleOption .= 'value="' . $role->id . '">' . ucwords($role->display_name) . '</option>';

                }

                $roleOption .= '</select>';

                return $roleOption;

            })

            ->editColumn('name', function ($row) {

                return '<div class="image-container"><div class="image"><img src=' . $row->profile_image_url . ' /></div>' . ucwords($row->name) . '</div>';

            })

            ->editColumn('email', function ($row) {

                return $row->email;

            })

            ->rawColumns(['name', 'action', 'role_name'])

            ->addIndexColumn()

            ->make(true);

    }



public function destroy($id)
{
    try {
        // Optional: delete related campaign_emails first if needed
        DB::table('campaign_emails')->where('campaign_id', $id)->delete();

        // Delete the campaign itself
        DB::table('campaigns')->where('id', $id)->delete();

        return redirect()->route('admin.campaign.index')->with('success', 'Campaign deleted successfully!');
    } catch (\Exception $e) {
        return redirect()->route('admin.campaign.index')->with('error', 'Failed to delete campaign: ' . $e->getMessage());
    }
}




    public function changeRole(Request $request)
    {

        //attach role

        $user = User::find($request->teamId);



        RoleUser::where('user_id', $request->teamId)->delete();

        $user->roles()->attach($request->roleId);



        return Reply::dataOnly(['status' => 'success']);

    }







}

