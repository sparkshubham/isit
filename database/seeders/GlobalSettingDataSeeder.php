<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlobalSetting;
class GlobalSettingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        GlobalSetting::create([
            'key' => 'email',
            'value' => 'admin@isit.com'
        ]);
        GlobalSetting::create([
            'key' => 'is_agora_active',
            'value' => '1'
        ]);
        GlobalSetting::create([
            'key' => 'is_1to1_twillio_audio',
            'value' => '1'
        ]);
        GlobalSetting::create([
            'key' => 'is_1to1_twillio_video',
            'value' => '1'
        ]);
        GlobalSetting::create([
            'key' => 'is_group_twillio_video',
            'value' => '1'
        ]);
    }
}
