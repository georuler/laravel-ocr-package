<?php
namespace Auth\Ocr\CoinShot\App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiTokenCreateController 
{
    public function store(Request $request) 
    {
        Validator::make($request->all(), [
            'companyName' => 'required',
            'companyEmail' => 'required',

        ]);

        return Str::random(50);
    }
}