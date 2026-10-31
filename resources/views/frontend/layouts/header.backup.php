@if(Auth::check())
<span class="screen-darken"></span>
<header class="section-header d-lg-none">
    <nav class="navbar navbar-expand-lg fixed-top navbar-light shadow-sm my-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img class="d-none d-lg-block " src="{{ asset('front/images/logo.png') }}" alt=""> <img class="d-block d-lg-none"  src="{{ asset('front/images/logo-mobile.png') }}" alt=""></a>
         <div id="main_nav" class="collapse navbar-collapse">
            <ul class="searchbar ms-auto mb-0">
                <input class="search-bar" type="text" placeholder="Search">
            </ul>
            <ul class="nav nav-pills ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><img  src="{{ asset('front/images/create-posts-icon.svg') }}" alt=""></a>
                    <ul class="dropdown-menu dropdown-menu-end fade-up">
                        <li><a class="dropdown-item" href="#"onclick="CreatePostGeneral()"> General Post</a></li>
                        <li><a class="dropdown-item" href="#" onclick="CreateAnointedPost()"> Anointed Post </a></li>
                      </ul>
                </li>
                <li class="nav-item message_hide"><a class="nav-link" href="#"><img class="inactive"  src="{{ asset('front/images/messages-icon.svg') }}"  alt=""><img class="active" src="{{ asset('front/images/messages-icon-active.svg') }}"  alt=""></a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="#"><img class="inactive" src="images/notifications-icon.svg" alt=""><img class="active" src="images/notifications-icon-active.svg" alt=""></a></li> -->
                <!-- <li class="nav-item mobile-only"><a class="nav-link" href="#"><img src="images/search.svg" alt=""></a></li> -->
                <li class="nav-item">
                    <div class="search-wrapper">
                        <div class="input-holder">
                            <input type="text" class="search-input" placeholder="Type to search" />
                            <button class="search-icon" onclick="searchToggle(this, event);"><img src="images/search.svg" alt=""></button>
                        </div>
                        <span class="close" onclick="searchToggle(this, event);"></span>
                        <div class="following-users scrollbar-outer">
                            <div class="follow-single-user">
                                <div class="follow-username-img">
                                    <div class="followuser-img">
                                        <img src="https://picsum.photos/50/50" alt="">
                                    </div>
                                    <div class="followuser-name">
                                        <a href="">Junita Bell</a>
                                    </div>
                                </div>
                                <div class="follow-btn d-flex">
                                    <button href="" class="btn btn-secondary">Follow</button>
                                </div>
                            </div>
                            <div class="follow-single-user">
                                <div class="follow-username-img">
                                    <div class="followuser-img">
                                        <img src="https://unsplash.it/660/455?image=830" alt="">
                                    </div>
                                    <div class="followuser-name">
                                        <a href="">Jason Todd</a>
                                    </div>
                                </div>
                                <div class="follow-btn d-flex">
                                    <button href="" class="btn btn-secondary">Follow</button>
                                </div>
                            </div>
                            <div class="follow-single-user">
                                <div class="follow-username-img">
                                    <div class="followuser-img">
                                        <img src="https://unsplash.it/660/455?image=831" alt="">
                                    </div>
                                    <div class="followuser-name">
                                        <a href="">Mary Webb</a>
                                    </div>
                                </div>
                                <div class="follow-btn d-flex">
                                    <button href="" class="btn btn-secondary">Follow</button>
                                </div>
                            </div>
                            <div class="follow-single-user">
                                <div class="follow-username-img">
                                    <div class="followuser-img">
                                        <img src="https://unsplash.it/660/455?image=832" alt="">
                                    </div>
                                    <div class="followuser-name">
                                        <a href="">William Smith</a>
                                    </div>
                                </div>
                                <div class="follow-btn d-flex">
                                    <button href="" class="btn">Following</button>
                                </div>
                            </div>
                            <div class="follow-single-user">
                                <div class="follow-username-img">
                                    <div class="followuser-img">
                                        <img src="https://unsplash.it/660/455?image=835" alt="">
                                    </div>
                                    <div class="followuser-name">
                                        <a href="">Jessica Alba</a>
                                    </div>
                                </div>
                                <div class="follow-btn d-flex">
                                    <button href="" class="btn btn-secondary">Follow</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item mobile-only"><a class="nav-link" href="#" data-trigger="navbar_main" type="button"><img  src="{{ asset('front/images/more-icon.svg') }}" alt=""></a></li>
                <!-- <li class="nav-item">
                    <div class="search-wrapper">
                        <div class="input-holder">
                            <input type="text" class="search-input" placeholder="Type to search" />
                            <button class="search-icon" onclick="searchToggle(this, event);"><img src="images/search.svg" alt=""></button>
                        </div>
                        <span class="close" onclick="searchToggle(this, event);"></span>
                    </div>
                </li> -->
            </ul>
         </div> 
        </div> <!-- container-fluid.// -->
       </nav>
</header> <!-- section-header.// -->
@endif