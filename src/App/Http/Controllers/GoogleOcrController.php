<?php
namespace Auth\Ocr\Google\App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Vision\Image;
use Illuminate\Support\Facades\DB;
use Google\Cloud\Vision\VisionClient;
use Auth\Ocr\Google\App\Traits\GoogleOcrExtractionTrait;
use Auth\Ocr\Google\App\Models\B2C_AUTH_CARD_RCV;

class GoogleOcrController 
{
    use GoogleOcrExtractionTrait;
    
    public function index(Request $request) : void
    {
        $request->merge(['per_page' => $request->per_page ?? 5]);
        
        $vision = new VisionClient([
            'keyFile' => json_decode(file_get_contents(public_path('google_cloud_auth_key/stellar-aurora.json')), true)
        ]);

        $imagePath = '';

        $image = new Image($imagePath, [ 'DOCUMENT_TEXT_DETECTION' ]);
        $annotation = $vision->annotate($image);
        $document = $annotation->fullText();
    }

}