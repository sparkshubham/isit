<?php

namespace App\Http\Controllers;

use App\Models\FrontUser;
use App\Models\SendEmail;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\FrontUserFormRequest;
use DB;
use Illuminate\Support\Facades\Log;

class UiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('guest')->except('logout');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function page2()
    {
        return view('front.ui.page2');
    }

    public function page1()
    {
        return view('frontend.ui.page1');
    
    }
    public function page3()
    {
        return view('front.ui.page3');
    }
    public function page4()
    {
        return view('front.ui.page4');
    }
    public function page5()
    {
        return view('front.ui.page5');
    }
    public function page6()
    {
        return view('front.ui.page6');
    }
    public function page7()
    {
        return view('front.ui.page7');
    }
    public function page8()
    {
        return view('front.ui.page8');
    }
    public function page9()
    {
        return view('front.ui.page9');
    }
    public function page10()
    {
        return view('front.ui.page10');
    }
    public function page11()
    {
        return view('front.ui.page11');
    }
    public function page12()
    {
        return view('front.ui.page12');
    }
    public function page13()
    {
        return view('front.ui.page13');
    }
    public function page14()
    {
        return view('front.ui.page14');
    }
}
