<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = $this->message;
        $users = User::select('users.id','users.device_id','users.device_type')
        ->where(function($query){            
            $query->whereRaw('users.deleted_at IS NULL');
        })->get()
		->toArray();
        // echo "<pre>";print_r($users);exit;
        foreach($users as $key => $value){
            $env = env("APP_ENV");
            if($env == 'dev'){
                $adminUserId = 1;
            }else if($env == 'qa'){
                $adminUserId = 4;
            }else if($env == 'stage'){
                $adminUserId = 1;
            }else{
                $adminUserId = 1;
            }

            sendNotificationWithData($adminUserId,$value['id'],21,['other_data'=>$message,'notification_type'=>21]);
        }
    }
}
