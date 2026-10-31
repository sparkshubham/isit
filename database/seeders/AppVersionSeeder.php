<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppVersion;
class AppVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //iOS
        AppVersion::create([
            'device_type' => '1',
            'current_version' => '1.0',
            'min_version' => '1.0',
            'is_force_update' => '0',
            'msg_force_update' => '',
            'msg_normal_update' => '',
            'is_prompt_update' => 0,
        ]);
        //Android
        AppVersion::create([
            'device_type' => '2',
            'current_version' => '1.0',
            'min_version' => '1.0',
            'is_force_update' => '0',
            'msg_force_update' => '',
            'msg_normal_update' => '',
            'is_prompt_update' => 0,
        ]);
        
    }
}
