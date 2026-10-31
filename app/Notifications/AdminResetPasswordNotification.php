<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Jobs\UserWelcomeEmailJob;

class AdminResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        
        $newMail = new MailMessage;
        //-------- old password reset old
        //$newMail->subject('Reset Password');
        // $newMail->action('Reset Password', route('admin-panel.password.reset.token', ['token' => $this->token, 'email' => urlencode($notifiable->email)]));

        //------- overriding the deafult functionality here add anthor token instead of email to verify the user later

        $newMail->action('Reset Admin Password', route('admin-panel.password.reset.token', ['token' => $this->token, 'secure' => get_encrypted_value($notifiable->email,true)]));

        $emailTemplateKeywords['{receiver}'] = $notifiable->first_name;
        $emailTemplateKeywords['{link}'] = $newMail->actionUrl;

        $message = 'Hi {name} <br><br>Welcome to InSpiritInTruth! <br>Please click on this {link} to verify your email address.<br><br><br>Thanks for joining us. <br><br>Yours Truly<br>Team ISIT';
		$templateData = ['subject' => 'ISIT Admin Reset Password','title'=>'ISIT Admin Reset Password'];
		$data = sendEmail($message,$templateData,$emailTemplateKeywords,$notifiable->first_name,$notifiable->email);
        AdminSendEmailPasswordResetToken($newMail->actionUrl,$notifiable->first_name,$notifiable->email);
        // prd($data);
        // $greeting = sendEmail('RESET_ADMIN_PASSWORD',$emailTemplateKeywords,$notifiable->first_name,$notifiable->email);

        //set subject and from
        // $newMail->subject($templateData['subject']);
        //$newMail->from($greeting['from_email'],'SnapMailBox');

        // $newMail->greeting($templateData['title']);

        return $newMail;
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
            //
        ];
    }
}
