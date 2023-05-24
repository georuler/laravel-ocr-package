<?php
namespace Auth\Ocr\Google\App\Http\Middleware;

use Closure;
use Exception;
use App\Exceptions\CustomException;

class ApiOcrToken
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
        try
        {
            //cors 허용
            // header('Access-Control-Allow-Origin: *'); 
            // header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            // header('Access-Control-Allow-Credentials: false'); 
            // header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization, Api-Ocr-Token");

            if(env('APP_ENV') === 'production') {
                if (!$request->header('Api-Ocr-Token')) {
                    throw new Exception("Api-Ocr-Token이 존재하지 않습니다.");
                } else {
                    /**
                     * @todo    - test일때 추가
                     *          - api call count check
                     */ 

                    if($request->header('Api-Ocr-Token') !== 'HHZblQQCkbJzcap56ZccsXvsqlLmPOtsANmRiImXpzjQcluYZ8') {
                        throw new Exception("요청 하신 Api-Ocr-Token 으로 접근 불가능 합니다.");
                    }
                }
            }
        }
        catch (Exception $e)
        {
            throw new CustomException('ApiOcrToken '. $e->getMessage());
        }

        return $next($request);
    }
}