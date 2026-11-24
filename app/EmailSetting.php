<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class EmailSetting extends Authenticatable
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $table = 'smtp_settings';


    protected $guarded = ['id'];
    protected $appends = ['set_smtp_message'];

    public function verifySmtp()
    {
        if($this->mail_driver =='smtp'){
            try {
                $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                    $this->mail_host,
                    (int) $this->mail_port,
                    $this->mail_encryption === 'tls'
                );
                $transport->setUsername($this->mail_username);
                $transport->setPassword($this->mail_password);
                $transport->start();

                if($this->verified == 0){
                    $this->verified = 1;
                    $this->save();
                }

                return [
                    'success' => true,
                    'message' => __('messages.smtpSuccess')
                ];


            } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
                $this->verified = 0;
                $this->save();
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];

            } catch (\Exception $e) {
                $this->verified = 0;
                $this->save();
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }else {
            return [
                'success' => true,
                'message' => __('messages.smtpSuccess')
            ];
        }
    }

    public function getSetSmtpMessageAttribute(){
        if ($this->verified === 0 && $this->mail_driver == 'smtp') {
            return ' <div class="alert alert-danger">
                    '.__('messages.smtpNotSet').'
                    <a href="'.route('admin.smtp-settings.index').'" class="btn btn-info btn-small">Visit SMTP Settings <i
                                class="fa fa-arrow-right"></i></a>
                </div>';
        }
        return null;
    }
}
