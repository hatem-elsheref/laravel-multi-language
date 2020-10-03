<?php

use Illuminate\Support\Facades\Route;


Route::get('/lang',function (){
    return view('Backend.blog.create');
    if (session()->get('lang') === 'en')
        dd('English Is Active Now');
    else
        dd('اللغة العربية تعمل الان');
})->middleware('lang');



Route::get('/lang/{lang}',function ($lang){
    if (session()->has('lang'))
        session()->forget('lang');
    session()->put('lang',$lang);
    return redirect('/lang');
});

// you can make new general controller to exchang the language instead of make the login the the route closure function