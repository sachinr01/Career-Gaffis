<?php

namespace App\Http\Controllers\Admin;
 use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\AdminBaseController;

class CampaignStatsController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('menu.campaignStats');
        $this->pageIcon = 'ti-bar-chart';
    }


public function stats(Request $request)
{
    try {
        abort_if(!$this->user->cans('view_campaign_stats'), 403);

        // AJAX request for DataTables
        if ($request->ajax()) {
            $start  = $request->start;
            $length = $request->length;
            $filter = $request->filter;

            $query = DB::table('campaign_emails as ce')
                ->join('job_applications as ja', 'ja.id', '=', 'ce.email_id')
                ->leftJoin('campaign_unsubscribed as cu', 'ja.id', '=', 'cu.candidate_id')
                ->select(
                    'ja.id as candidate_id',
                    'ja.full_name',
                    'ja.email',
                    'ja.phone',
                    'ja.error_or_bounced',
                    DB::raw('CASE WHEN cu.candidate_id IS NOT NULL THEN "Yes" ELSE "No" END as unsubscribed'),
                    DB::raw('COUNT(ce.id) as total_emails_sent')
                )
                ->groupBy('ja.id', 'ja.full_name', 'ja.email', 'ja.phone', 'ja.error_or_bounced', 'cu.candidate_id')
                ->orderBy('ja.id');

            if ($filter === 'unsubscribed') {
                $query->whereNotNull('cu.candidate_id');
            }
            if ($filter === 'error_or_bounced') {
                $query->where('ja.error_or_bounced', '=', 'yes');
            }

            $totalData = $query->count();
            $candidates = $query->skip($start)->take($length)->get();

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $totalData,
                "recordsFiltered" => $totalData,
                "data" => $candidates
            ]);
        }

        // Non-AJAX fallback (optional)
        $query = DB::table('campaign_emails as ce')
            ->join('job_applications as ja', 'ja.id', '=', 'ce.email_id')
            ->leftJoin('campaign_unsubscribed as cu', 'ja.id', '=', 'cu.candidate_id')
            ->select(
                'ja.id as candidate_id',
                'ja.full_name',
                'ja.email',
                'ja.phone',
                'ja.error_or_bounced',
                DB::raw('CASE WHEN cu.candidate_id IS NOT NULL THEN "Yes" ELSE "No" END as unsubscribed'),
                DB::raw('COUNT(ce.id) as total_emails_sent')
            )
            ->groupBy('ja.id', 'ja.full_name', 'ja.email', 'ja.phone', 'ja.error_or_bounced', 'cu.candidate_id')
            ->orderBy('ja.id');

        $candidates = $query->paginate(50);
   
        $this->data['candidates'] = $candidates;

        return view('admin.campaign-status.stats', $this->data);

    } catch (\Exception $e) {
        Log::error('Campaign Stats Error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
        abort(500, 'Something went wrong. Check logs for details.');
    }
}



}
