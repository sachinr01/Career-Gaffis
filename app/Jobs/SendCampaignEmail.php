<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\CampaignEmail;
use App\Models\JobApplication;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaignEmailId;
    protected $templateHtml;
    protected $subject;
    protected $bcc;
    protected $cc;

    public function __construct($campaignEmailId, $templateHtml, $subject, $bcc = null, $cc = null)
    {
        $this->campaignEmailId = $campaignEmailId;
        $this->templateHtml = $templateHtml;
        $this->subject = $subject;
        $this->bcc = $bcc;
        $this->cc = $cc;
    }

    public function handle()
    {
        $campaignEmail = CampaignEmail::find($this->campaignEmailId);
        if (!$campaignEmail || $campaignEmail->email_sent) return;

        $applicant = JobApplication::find($campaignEmail->email_id);
        if (!$applicant || $applicant->unsubscribed === 'yes') return;

        // Prepare email body
        $emailBody = str_replace(
            ['[APPLICANT_NAME]', '[APPLICANT_EMAIL]', '[CAMPAIGN_EMAIL_ID]'],
            [$applicant->full_name, $applicant->email, $campaignEmail->id],
            $this->templateHtml
        );

        $trackImg = '<img src="' . url("/custom_apis/trackMailReadStatus.php?campaign_email_id={$campaignEmail->id}") . '" width="1" height="1" />';
        $emailBody .= $trackImg;

        try {
            Mail::send([], [], function($message) use ($applicant, $emailBody) {
                $message->to($applicant->email)
                        ->subject($this->subject)
                        ->setBody($emailBody, 'text/html')
                        ->from('helpdesk@gaffis.com', 'Helpdesk @ Gaffis Tech');

                if($this->bcc) $message->bcc($this->bcc);
                if($this->cc) $message->cc($this->cc);
            });

            $campaignEmail->email_sent = 1;
            $campaignEmail->save();
        } catch (\Exception $e) {
            // You can log error
            \Log::error("Failed sending email to {$applicant->email}: ".$e->getMessage());
        }
    }
}
