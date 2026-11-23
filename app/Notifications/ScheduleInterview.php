<?php

namespace App\Notifications;

use App\JobApplication;
use App\SmsSetting;
use App\Traits\SmsSettings;
use App\Traits\SmtpSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;

class ScheduleInterview extends Notification
{
    use Queueable, SmtpSettings, SmsSettings;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobApplication $jobApplication , $meetings)
    {
        $this->jobApplication = $jobApplication;
        $this->smsSetting = SmsSetting::first();
        $this->meetings = $meetings;

        $this->setMailConfigs();
        $this->setSmsConfigs();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['mail', 'database'];

        if ($this->smsSetting->nexmo_status == 'active' && $notifiable->mobile_verified == 1) {
            array_push($via, 'nexmo');   
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
       $emailContent = (new MailMessage)
        ->subject(strip_tags(__('email.interviewSchedule.subject')))
        ->line('<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">'.__('email.hello').' '.ucwords($notifiable->name).'!</p>')
        ->line('<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">'.ucwords($this->jobApplication->full_name).' '.__('email.interviewSchedule.text').' - '.ucwords($this->jobApplication->job->title).'</p>')
        ->line('<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">'.__('email.interviewSchedule.response').' <a href="'.url('/login').'" style="color:#3490dc; text-decoration:none;">'.__('email.loginDashboard').'</a></p>');

    // Meeting links
    if ($this->meetings != null) {
        if ($notifiable->id == $this->meetings->created_by) {
            $emailContent = $emailContent->line('<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">'.__('modules.zoommeeting.meetingPassword').' - '.$this->meetings->password.'</p>')
                ->action(__('modules.zoommeeting.startUrl'), url($this->meetings->start_link));
        } else {
            $emailContent = $emailContent->line('<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">'.__('modules.zoommeeting.meetingPassword').' - '.$this->meetings->password.'</p>')
                ->action(__('modules.zoommeeting.joinUrl'), url($this->meetings->join_link));
        }
    }

    $emailContent = $emailContent->line('<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">'.__('email.thankyouNote').'</p>');

    return $emailContent;    }

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

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->content(
                        __($this->jobApplication->full_name).' '.__('email.interviewSchedule.text').' - ' . ucwords($this->jobApplication->job->title)
                    )->unicode();
    }
}
