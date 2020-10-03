<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
class LanguageMiddleware
{


    private $activeLanguages;
    private  $Language;


    public function handle($request, Closure $next)
    {
        $this->activeLanguages=array_filter(array_map(fn($arr)=> ($arr['status']=== true)?$arr:null,config('locale')));
        if(Session::has('lang') and in_array(Session::get('lang'),array_keys($this->activeLanguages))){
            $this->Language=Session::get('lang');
        }else{
            $recommended=preg_split("/[,;]/",$request->server('HTTP_ACCEPT_LANGUAGE'));
            if (in_array($recommended,array_keys($this->activeLanguages))){
                $this->Language=$recommended;
            }else{
                $this->Language=config('app.fallback_locale');
            }
        }
        Session::put('lang',$this->Language);
        App::setLocale(Session::get('lang'));
        Carbon::setLocale(App::getLocale());
        return $next($request);
    }
}
