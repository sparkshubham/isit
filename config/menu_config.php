<?php

return [

    /*
    |--------------------------------------------------------------------------
    | menus can be manage here
    |--------------------------------------------------------------------------
    */
    'front_sidebar' => [
        [
            'label' => 'Post-Board',
            'url' => 'post-board',
            'path'=>'front/images/home-icon-active.svg',
            'inactive_path'=>'front/images/home-icon.svg',
            'flag'=>1,
            'active_segments' => ['post-board'],
            'title' => 'Post-Board',

            
        ],
        [
            'label' => 'Explore',
            'url' => 'explore',
            'path'=>'front/images/explore-icon-active.svg',
            'inactive_path'=>'front/images/explore-icon.svg',
            'flag'=>1,
            'active_segments' => ['explore'],
            'title' => 'Explore',
        ],
        [
            'label' => 'Anointed',
            'url' => 'anointed',
            'path'=>'front/images/anointed-icon-active.svg',
            'inactive_path'=>'front/images/anointed-icon.svg',
            'flag'=>1,
            'active_segments' => ['anointed'],
            'title' => 'Anointed',
        ],
        [
            'label' => 'Post',
            'url' => 'post',
            'path'=>'front/images/create-posts-icon.svg',
            'inactive_path'=>'front/images/create-posts-icon.svg',
            'flag'=>2,
            'active_segments' => ['post'],
            'title' => 'Post',
        ],
       
        // [
        //     'label' => 'Message',
        //     'url' => 'front-panel/message',
        //     'path'=>'front/images/messages-icon-active.svg',
        //     'inactive_path'=>'front/images/messages-icon.svg',
        //     'flag'=>1,
        //     'active_segments' => ['message'],
        //     'title' => 'Message',
        // ],
        [
            'label' => 'Notification',
            'url' => 'notification',
            'path'=>'front/images/notifications-icon-active.svg',
            'inactive_path'=>'front/images/notifications-icon.svg',
            'flag'=>1,
            'active_segments' => ['notification'],
            'title' => 'Notification',
        ],
    ],


    'setting_sidebar' => [
        [
            'label' => 'Manage Profile',
            'url' => 'my-profile',
            'path'=>'front/images/terms-of-use-icon.svg',
            'inactive_path'=>'front/images/terms-of-use-icon.svg',
            'active_segments' => ['my-profile'],

        ],
        [
            'label' => 'Blocked Users',
            'url' => 'blocked-users',
            'path'=>'front/images/blocked-users-icon.svg',
            'inactive_path'=>'front/images/blocked-users-icon.svg',
            'active_segments' => ['blocked-users'],
        ],
        [
            'label' => 'Terms of Use',
            'url' => 'terms-of-uses',
            'path'=>'front/images/terms-of-use-icon.svg',
            'inactive_path'=>'front/images/terms-of-use-icon.svg',
            'active_segments' => ['terms-of-use'],
        ],
        [
            'label' => 'Help',
            'url' => 'help',
            'path'=>'front/images/help-icon.svg',
            'inactive_path'=>'front/images/help-icon.svg',
            'active_segments' => ['help'],
        ],
        [
            'label' => 'Logout',
            'url' => 'logout',
            'path'=>'front/images/log-out-icon.svg',
            'inactive_path'=>'front/images/log-out-icon.svg',
        ],
    ],
];
