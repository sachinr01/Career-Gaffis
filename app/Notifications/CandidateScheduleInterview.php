<?php

namespace App\Notifications;

use App\InterviewSchedule;
use App\JobApplication;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use App\ZoomMeeting;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
class CandidateScheduleInterview extends Notification
{
    use Queueable, SmtpSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobApplication $jobApplication,InterviewSchedule $interviewSchedule , $meetings)
    {
        $this->jobApplication = $jobApplication;
        $this->interviewSchedule = $interviewSchedule;
        $this->meetings = $meetings;

        $this->setMailConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $scheculedDate = Carbon::parse($this->interviewSchedule->schedule_date)->format('M d, Y ');
        $scheduleDateOld = Carbon::parse($this->interviewSchedule->schedule_date)->format('h:i A');
        $scheduleDateNew = Carbon::parse($this->interviewSchedule->schedule_date)->addHours(2)->format('h:i A');
        
        $finalDate = $scheculedDate . "(" . $scheduleDateOld . " To " . $scheduleDateNew . ")";



        
     $emailContent = (new MailMessage)
        ->subject(strip_tags(__('email.interviewSchedule.subject'))) // subject without html
      ->line(
        '<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px;  color: #333;">'
        . __('email.hello') . ' ' . ucwords($notifiable->full_name) . '!</p>'
    )
    ->line(
        '<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">'
        . __('email.your') . ' ' . __('email.interviewSchedule.text') . ' '
        . '- <span style=" color: #333333;">' . ucwords($this->jobApplication->job?->title) . '</span>'
        . '</p>'
    )
        ->line('<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">'.__('email.on').' - '.$finalDate.'</p>')
        ->line(__('email.interviewSchedule.note'))
        ->line('<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">'.__('email.thankyouNote').'</p>');

    return $emailContent;
return $emailContent;

            
            // ->line(__('modules.zoommeeting.meetingPassword') . ' - ' . ucwords($this->meetings->password))
            // ->action(__('modules.zoommeeting.joinUrl'), url($this->meetings->join_link))
            // ->line(__('email.thankyouNote'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'data' => $this->jobApplication->toArray()
        ];
    }

}