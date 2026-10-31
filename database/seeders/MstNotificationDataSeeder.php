<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
class MstNotificationDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1
        Notification::create([
            'message' => 'username: liked your Post.',
            'description' => 'Send notification to owner of post like.',
        ]);
        // 2
        Notification::create([
            'message' => 'username: commented your Post.',
            'description' => 'Send notification to owner of post for comment.',
        ]);
        // 3
        Notification::create([
            'message' => 'username: replied your comment.',
            'description' => 'Send notification to owner of comment for replied.',
        ]);
        // 4
        Notification::create([
            'message' => 'username: shared your Post.',
            'description' => 'Send notification to owner of comment for share post.',
        ]);
        // 5
        Notification::create([
            'message' => 'username: started following you.',
            'description' => 'Send notification to user who is followed.',
        ]);
        // 6
        Notification::create([
            'message' => 'eventname: has started Live.',
            'description' => 'Send notification to all user for event live.',
        ]);
        // 7
        Notification::create([
            'message' => 'username: saved your Event.',
            'description' => 'Send notification to owner of Event for save event',
        ]);
        // 8
        Notification::create([
            'message' => 'username: saved your Post.',
            'description' => 'Send notification to owner of Post for save Post',
        ]);
        // 9
        Notification::create([
            'message' => 'username: accepted your follow request.',
            'description' => 'Send notification to user who requested to follow user.',
        ]);
        // 10
        Notification::create([
            'message' => 'username: requested to follow you.',
            'description' => 'Send notification to followed user if publis visibility is OFF.',
        ]);
        // 11
        Notification::create([
            'message' => 'username: replied a comment on your post.',
            'description' => 'Send a notitication to post owner if any one replied on comment of his post.',
        ]);
    }
    
}
