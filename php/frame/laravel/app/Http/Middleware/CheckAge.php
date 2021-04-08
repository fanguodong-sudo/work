<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

use Closure;

class CheckAge extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */



//    protected function redirectTo($request)
//    {
//
//        $post = $request->post();
//        $get = $request->get('user');
//
//        print_r($post);
//        print_r($get);
//
//    }

//Illuminate\Auth\Middleware\Authenticate::handle

    public function handle($request,Closure $next,$editor=null)
    {
        // Perform action

        echo 'fffffxxxxxx';
        echo $editor;


        return $next($request);
    }


    public function terminate($request, $response)
    {
        echo '1111111111';
    }



}
