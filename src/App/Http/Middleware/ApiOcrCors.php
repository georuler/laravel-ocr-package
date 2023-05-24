<?php
namespace Auth\Ocr\Google\App\Http\Middleware;

use Closure;
use Exception;
use App\Exceptions\CustomException;

class ApiOcrCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request)
          ->header("Access-Control-Allow-Origin", "*")
          ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
          ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization, Api-Ocr-Token");
    }
}