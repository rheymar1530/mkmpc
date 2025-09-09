<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Auth;
use Session;
use App\MySession as MySession;
use Illuminate\Support\Facades\Route;

class SessionExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $session;
    protected $timeout = 3600; //30 Secs
    
    public function __construct(Store $session){
        $this->session = $session;
    }
    public function handle($request, Closure $next)
    {
        // url()->full()
        $isLoggedIn = MySession::mySystemUserId();

        if(MySession::mySystemUserId() == "" && $request->path() != 'login'){
            return redirect()->route('login', ['redirect_url' => urlencode(url()->full())])->with('message', 'Please login first!');
            // return redirect('login',['redirect_url' => '123232'])->with('message', 'Please login firstx ! !');
            // return redirect('login')->with('message', 'Please login first ! !');
        }

        if(!session('lastActivityTime'))
            $this->session->put('lastActivityTime', time());
        elseif(time() - $this->session->get('lastActivityTime') > $this->timeout){
            $this->session->forget('lastActivityTime');
            Session::flush();
            return redirect('login')->with('message', 'Thank You, See You Later !');
        }

        $isLoggedIn ? $this->session->put('lastActivityTime', time()) : $this->session->forget('lastActivityTime');

        return $next($request);
    }
}