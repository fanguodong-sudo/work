<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    //排除url
    protected $except = [
        'http://example.com/foo/*',
        'http://example.com/foo/bar',
        'http://127.0.0.1:8000/request/clause1'
    ];
}
