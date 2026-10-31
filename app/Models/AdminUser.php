<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\AdminResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Messages\MailMessage;

class AdminUser extends Authenticatable
{
    use HasFactory,Notifiable,SoftDeletes,HasRoles;

    protected $table = "admin_user";
    protected $primaryKey = "admin_id";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $newMail = new MailMessage;
        $newMail->action('Reset Admin Password', route('admin-panel.password.reset.token', ['token' => $token, 'secure' => get_encrypted_value($this->email,true)]));
        $emailTemplateKeywords['{receiver}'] = $this->first_name;
        $emailTemplateKeywords['{link}'] = $newMail->actionUrl;
        $message = 'Hi Admin! <br><br>Welcome to InSpiritInTruth! <br>Please click on this {link} to verify your email address.<br><br><br>Thanks for joining us. <br><br>Yours Truly<br>Team ISIT';
		$templateData = ['subject' => 'ISIT Admin Reset Password','title'=>'ISIT Admin Reset Password'];
		$data = sendEmail($message,$templateData,$emailTemplateKeywords,$this->first_name,$this->email);
        AdminSendEmailPasswordResetToken($newMail->actionUrl,'Admin!',$this->email);

        // $this->notify(new AdminResetPasswordNotification($token));
    }
}
