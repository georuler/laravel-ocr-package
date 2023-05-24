<?php
namespace Auth\Ocr\Google\App\Traits;

use Exception;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * OCR 데이터 추출
 */
trait GoogleOcrExtractionTrait
{
    /**
     * strpos Array Check
     *
     * @param string $haystack
     * @param array $needle
     * @param integer $offset
     * @return boolean
     */
    public function strposArray(string $haystack, array $needle, int $offset=0) : bool
    {
        if(!is_array($needle)) $needle = array($needle);
        foreach($needle as $query) {
            if(strpos($haystack, $query, $offset) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 베트남 문자 영문 변환
     *
     * @param string $str
     * @return string
     */
    public function strVietnamToEnglish(string $str) : string
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
    
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);

        return $str;
    }

    

    /**
     * 구글 ocr 단어별 신뢰도 filtering
     *
     * @param array $params
     * @return array
     */
    public function wordConfidenceFilter(array $params) : array
    {
        $integratedWord = [];
        foreach($params['jsonObj'] as $val => $key) {
            foreach($key['paragraphs'] as $val1 => $key1) {
                $eachWords = [];
                foreach($key1['words'] as $val2 => $key2) {
                    $textArray = [];
                    foreach($key2['symbols'] as $val3 => $key3) {
                        $confidence = (float)sprintf("%.2f", $key3['confidence']);
                        if($confidence < config('google.wordConfidenceMin')) {
                            continue;
                        }
                        array_push($textArray, $key3['text']);
                    }
                    array_push($eachWords, implode("", $textArray));
                }                
                array_push($integratedWord, trim(implode(" ", $eachWords)));
            }
        }

        return array_values(array_filter($integratedWord));
    }

    /**
     * 주민번호 유효성 확인
     *
     * @param string $resno
     * @return boolean
     */
    public function idNumberCheck(string $resno) : bool
    {
        $isResnoCheck = true;

        // 날짜 유효성 검사
        $birthYear = ('2' >= $resno[6]) ? '19' : '20';
        $birthYear .= substr($resno, 0, 2);
        $birthMonth = substr($resno, 2, 2);
        $birthDate = substr($resno, 4, 2);
        if (!checkdate($birthMonth, $birthDate, $birthYear)) {
            $isResnoCheck = false;
        }

        // Checksum 코드의 유효성 검사
        for ($i = 0; $i < 13; $i++) {
            $buf[$i] = (int) $resno[$i];
        };

        $multipliers = [2,3,4,5,6,7,8,9,2,3,4,5];

        for ($i = $sum = 0; $i < 12; $i++) {
            $sum += ($buf[$i] *= $multipliers[$i]);
        }
        if ((11 - ($sum % 11)) % 10 != $buf[12]) {
            $isResnoCheck = false;
        }

        return $isResnoCheck;
    }

    /**
     * 주민번호 만나이 계산
     *
     * @param string $idCardNumber
     * @return int
     */
    public function idCardYearToAge(string $idCardNumber) : int
    {

        if (!$idCardNumber || empty($idCardNumber)) {
            return 0;
        }

        $birth = '';
        $age = 0;
        $yearBifurcation = (int) substr(trim($idCardNumber),6,1);   //년도 분기점
        
        if($yearBifurcation == 1 || $yearBifurcation == 2 || $yearBifurcation == 5 || $yearBifurcation == 6 ) {
            $birth = '19'.substr(trim($idCardNumber),0,2);
        } else if($yearBifurcation == 3 || $yearBifurcation == 4 || $yearBifurcation == 7 || $yearBifurcation == 8) {
            $birth = '20'.substr(trim($idCardNumber),0,2);
        } 

        if (!empty($birth)) {
            $age = (int) date('Y') - (int) $birth;
        }       

        return (int) $age;

    }    

    /**
     * 주민, 외국인 등록증 validation
     *
     * @param string $socailNumber
     * @return boolean
     */
    public function socialNumberValidation(string $socailNumber) : bool
    {
        $sum = 0;        
        $checkNumber = 11;

        $socialNumberArray = [];
        $compare = [2, 3, 4, 5, 6, 7, 8, 9, 2, 3, 4, 5];
        
        // 자리수 체크
        if(strlen($socailNumber) !== 13) {
            return false;
        }

        $socialNumberArray = str_split($socailNumber);

        // 공식: M = (11 - ((2×A + 3×B + 4×C + 5×D + 6×E + 7×F + 8×G + 9×H + 2×I + 3×J + 4×K + 5×L) % 11)) % 10    
        for ($i = 0; $i<12; $i++)
        {
            $sum = $sum + ($socialNumberArray[$i] * $compare[$i]); 
        }

        if(in_array($socialNumberArray[6], [5,6,7,8])) {
            //외국인
            $checkNumber = 13;
        }

        $sum = ($checkNumber - ($sum % 11)) % 10;

        if ($sum != $socialNumberArray[12])
        { 
            return false;
        }
        return true;
    }    

    /**
     * 카드 정보 추출
     *
     * @param array $params
     * @return array
     */
    public function cardInfoExtraction(array $params) : array
    {
        $cardNumberLenth = config('google.cardNumberLenth');
        $nameMinLenth = config('google.nameMinLenth');
        $cardBrandArray = config('google.cardBrand');
        $strFilterArray = config('google.strFilter');

        $validity = '';             //유효기간
        $validityArray = [];        //유효기간 확인 배열
        $validityMonthCheck = true; //유효기간 월 체크
        $validityYearCheck = true;  //유효기간 년 체크

        $cardNumber = '';           //카드번호
        $newCardNumber = '';        //카드번호 개행
        $cardNumberArray = [];

        $cardUserName = '';         //사용자 명
        $cardUserNameArray = [];    //사용자 명 배열

        if($params['google_ocr']) {

            $textToArray = $this->wordConfidenceFilter([
                'jsonObj' => json_decode($params['google_ocr'], true)['pages'][0]['blocks']
            ]);
    
            if(isset($params['debug'])) {
                dump($textToArray);
            }

            //사용자명이 있을경우 배열에 값을 추가함
            if(isset($params['OCR_USER_NAME'])) {
                array_push($cardUserNameArray, $params['OCR_USER_NAME']);
            }
                
            for($i = 0; $i < count($textToArray); $i++) {
    
                //유효기간 체크
                if(preg_match('/[0-9\/-]{3}[0-9]{2}/', $textToArray[$i])) {
                    $validityStrArray = explode(" ", $textToArray[$i]);

                    if($validityStrArray) {
                        for($k = 0; $k < count($validityStrArray); $k++) {

                            //알파벳이 있을 경우 replace 처리
                            $validityStrArray[$k] = preg_replace("/[a-zA-Z]/", "", $validityStrArray[$k]);

                            if(strlen($validityStrArray[$k]) === config('google.validityLength')) {
                                if($this->strposArray($validityStrArray[$k], config('google.validityFilter'))) {
                                    array_push($validityArray, $validityStrArray[$k]);
                                }
                            }
                        }
                    }
                }
    
                //카드번호 체크
                if(strlen(preg_replace('/\s+/', '', $textToArray[$i])) === $cardNumberLenth) {
                    if(is_numeric(preg_replace('/\s+/', '', $textToArray[$i]))) {
                        $cardNumber = $textToArray[$i];
                        //array_push($cardNumberArray, $textToArray[$i]);
                    }
                }

                if(!$cardNumber) {
                    if(is_numeric(preg_replace('/\s+/', '', $textToArray[$i]))) {
                        
                        //개행 처리되는 카드번호 처리
                        if(strlen($textToArray[$i]) < 4) {
                            continue;
                        }

                        $newCardNumber .= $textToArray[$i];
                        
                        if(strlen(preg_replace('/\s+/', '', $newCardNumber)) === $cardNumberLenth) {
                            $cardNumber = $newCardNumber;
                        }
                    }

                    if(!$cardNumber) {
                        //카드번호에 이상한 문자가 있는 경우 처리
                        if(strlen(preg_replace('/\s+/', '', $textToArray[$i])) > $cardNumberLenth) {
                            
                            $cardNumberCheck = preg_replace("/[^0-9]*/s", "", $textToArray[$i]);

                            if(strlen(preg_replace('/\s+/', '', $cardNumberCheck)) === $cardNumberLenth) {
                                $cardNumber = $cardNumberCheck;
                            }
                        }
                    }
                }
                    
                //사용자명 체크
                if(preg_match('/^[A-Z]+$/', preg_replace('/\s+/', '', $textToArray[$i]))) { //대문자 체크
                    
                    if(!in_array($textToArray[$i], $cardBrandArray)) {  //카드 브랜드 제외
    
                        if(!in_array($textToArray[$i], $strFilterArray)) {  //필터 제외
                            
                            if(strlen($textToArray[$i]) > $nameMinLenth) {

                                $filterArrayCheck = $this->strposArray($textToArray[$i], $strFilterArray);
                                
                                if(!$filterArrayCheck) {
                                    array_push($cardUserNameArray, $textToArray[$i]);
                                    $cardUserName = $textToArray[$i];
                                }
                                
                            }
    
                        }
    
                    }
                //베트남 문자열일 경우
                } else if(preg_match("/(".config('google.vietnamString').")/", $textToArray[$i])) {
                    array_push($cardUserNameArray, $this->strVietnamToEnglish($textToArray[$i]));
                }
            }
        }

        //사용자명 배열이 있을경우
        if(count($cardUserNameArray) > 1) {
            //이름으로 간주하는 배열이 많을 경우 이름 초기화 처리
            if(count($cardUserNameArray) > 5) { 
                $cardUserName = 'arrayCheck';
            } else {
                for($k = 0; $k < count($cardUserNameArray); $k++) {

                    if(!preg_match('/^[A-Z]+$/', preg_replace('/\s+/', '', $cardUserNameArray[$k]))) {
                        continue;
                    }

                    if(strlen($cardUserName) < strlen($cardUserNameArray[$k])) {
                        $cardUserName = $cardUserNameArray[$k];
                    }
                }
            }
        }

        if(count($validityArray) > 0) {
            for($k = 0; $k < count($validityArray); $k++) {
                $validityReplace = str_replace(config('google.validityFilter'), '/', $validityArray[$k]);
                
                $validityMonthArray = explode("/", $validityReplace);

                if((int) $validityMonthArray[0] > 12) {
                    continue;
                }                

                if(strpos($validityReplace, '/')) {
                    $validity = $validityReplace;
                }
            }

            //유효기간 월 체크 12를 넘는 것들이 있어서...
            if($validity) {
                $validityMonthArray = explode("/", $validity);

                $chkYear = (int) date('y') + 6;

                //
                if(isset($params['validityMonth'])) {
                    $chkYear = (int) $params['validityMonth'];
                }
                
                //유효기간 년, 월 유효성 체크
                if((int) $validityMonthArray[0] > 12) {
                    $validityMonthCheck = false;
                }

                if((int) $validityMonthArray[1] > $chkYear) {
                    $validityYearCheck = false;
                }
            }
        }

        return [
            'validity' => $validity ?? '',
            'validityMonthCheck' => $validityMonthCheck ?? '',
            'validityYearCheck' => $validityYearCheck ?? '',
            'validity' => $validity ?? '',
            'cardNumber' => $cardNumber ?? '',
            'cardUserName' => $cardUserName ?? '',
        ];
    }


    /**
     * 외국인 등록증 추출
     *
     * @param array $params
     * @return array
     */
    public function alienExtraction(array $params) : array
    {
        $alienNameMinLenth = config('google.alien.alienNumberMinLenth');
        $strFilterArray = config('google.alien.strExceptFilter');

        $alienNumber = '';
        $alienNumberArray = [];

        $alienUserName = '';
        $alienUserNameArray = [];
        
        $nationalName = '';
        $alienIssueDate = '';
        
        $alienNumberValidator = true;

        if($params['google_ocr']) {
            $textToArray = $this->wordConfidenceFilter([
                'jsonObj' => json_decode($params['google_ocr'], true)['pages'][0]['blocks']
            ]);
            
            for($i = 0; $i < count($textToArray); $i++) {
                if(preg_match('/[0-9\/-]{6}[0-9]{7}/', $textToArray[$i])) {
                    $alienNumberCheck = preg_replace("/[^0-9]*/s", "", $textToArray[$i]);
                    
                    if(config('google.alien.alienNumberLenth') === strlen($alienNumberCheck)) {
                        array_push($alienNumberArray, $alienNumberCheck);
                    }
                }

                if(preg_match('/[A-Z\-]$/', $textToArray[$i])) {
                    if(!in_array($textToArray[$i], $strFilterArray)) {
                        if(strlen(preg_replace('/\s+/', '', $textToArray[$i])) > $alienNameMinLenth) {
                            
                            $alphabetCount = preg_match_all('/[A-Z]/', $textToArray[$i], $matches);
                            if($alphabetCount <= 1) {
                                continue;
                            }

                            array_push($alienUserNameArray, $textToArray[$i]);
                        }
                    }
                } else if(preg_match("/(".config('google.vietnamString').")/", $textToArray[$i])) {      

                    array_push($alienUserNameArray, $this->strVietnamToEnglish($textToArray[$i]));

                    if(!$alienUserName) {
                        $alienUserName = $this->strVietnamToEnglish($textToArray[$i]);
                    }
                }

                if(preg_match('/[0-9\/.]{4}[0-9\/.]{2}[0-9]{2}/', $textToArray[$i])) {
                    $alienIssueDateCheck = preg_replace("/[^0-9]*/s", "", $textToArray[$i]);
                    
                    if(config('google.alien.alienIssueDateMinLenth') === strlen($alienIssueDateCheck)) {
                        $alienIssueDate = $alienIssueDateCheck;
                    }
                }
            }
        }

        if(count($alienNumberArray) > 0) {
            $alienNumber = $alienNumberArray[0];
        }

        if(count($alienUserNameArray) > 1) {
            for($k = 0; $k < count($alienUserNameArray); $k++) {
                $nationalFilterCheck = $this->strposArray($alienUserNameArray[$k], config('google.alien.nationalFilter'));
                
                if($nationalFilterCheck) {
                    $nationaleStrArray = explode(" ", $alienUserNameArray[$k]);
                    foreach($nationaleStrArray as $val) {
                        if($this->strposArray($val, config('google.alien.nationalFilter'))) {
                            $nationalName = trim(preg_replace("/[^A-Z]/", " ", $val));
                        }
                    }
                } else {

                    if(!in_array($alienUserNameArray[$k], $strFilterArray)) {

                        if(strpos($alienUserNameArray[$k], '성') !== false || strpos($alienUserNameArray[$k], '명') !== false) {
                            if(strpos($alienUserNameArray[$k], '성별') === false) {
                                $alienUserName = trim(preg_replace("/[^A-Z]/", " ", $alienUserNameArray[$k]));
                            }
                        }
    
                        if(!$alienUserName) {
                            $alienUserName = trim(preg_replace("/[^A-Z]/", " ", $alienUserNameArray[$k]));
    
                            if(strlen($alienUserName) <= $alienNameMinLenth) {
                                $alienUserName = '';
                            }
                        }
                    }
                    
                }
            }
        }
        // 년자리 ==> 18세~ 90세까지만 정상으로...
        // 월자리 ==> 01~12
        // 일자리 ==> 01~30/31
        if($alienNumber) {
            $age = $this->idCardYearToAge($alienNumber);
            $month = (int) substr($alienNumber, 2, 2);
            $day = (int) substr($alienNumber, 4, 2);

            if(!($age >= 18 && $age <= 90)) {
                $alienNumberValidator = false;
            }

            if(!($month > 0 && $month <= 12)) {
                $alienNumberValidator = false;
            }

            if(!($day > 0 && $day <= 31)) {
                $alienNumberValidator = false;
            }
        }

        return [
            'alienNumber' => preg_replace('/\s+/', '', $alienNumber) ?? '',
            'alienUserName' => $alienUserName ?? '',
            'nationalName' => $nationalName ?? '',
            'alienIssueDate' => $alienIssueDate ?? '',
            'alienNumberValidator' => $alienNumberValidator ?? ''
        ];
    }  



    /**
     * Pass card number and it will return brand if found
     * Examples:
     *     get_card_brand('4111111111111111');                    // Output: "visa"
     *     get_card_brand('4111.1111 1111-1111');                 // Output: "visa" function will remove following noises: dot, space and dash
     *     get_card_brand('411111######1111');                    // Output: "visa" function can handle hashed card numbers
     *     get_card_brand('41');                                  // Output: "" because invalid length
     *     get_card_brand('41', false);                           // Output: "visa" because we told function to not validate length
     *     get_card_brand('987', false);                          // Output: "" no match found
     *     get_card_brand('4111 1111 1111 1111 1111 1111');       // Output: "" no match found
     *     get_card_brand('4111 1111 1111 1111 1111 1111', false);// Output: "visa" because we told function to not validate length
     * Implementation Note: This function doesn't use regex, instead it compares digit by digit. 
     *                      Because we're not using regex in this function, it's easier to add/edit/delete new bin series to global constant CARD_NUMBERS
     * Performance Note: This function is extremely fast, less than 0.0001 seconds
     * @param  String|Int $cardNumber     (required) Card number to know its brand. Examples: 4111111111111111 or 4111 1111-1111.1111 or 411111###XXX1111
     * @param  Boolean    $validateLength (optional) If true then will check length of the card which must be correct. If false then will not check length of the card. For example you can pass 41 with $validateLength = false still this function will return "visa" correctly
     * @return String                                returns card brand if valid, otherwise returns empty string
     */
    public function getCardBrand($cardNumber, $validateLength = true) : string
    {
        $cardBrand = [
            'american_express' => [
                '34' => ['15'],
                '37' => ['15'],
            ],
            'diners_club' => [
                '36'      => ['14-19'],
                '300-305' => ['16-19'],
                '3095'    => ['16-19'],
                '38-39'   => ['16-19'],
            ],
            'jcb' => [
                '3528-3589' => ['16-19'],
            ],
            'discover' => [
                '6011'          => ['16-19'],
                '622126-622925' => ['16-19'],
                '624000-626999' => ['16-19'],
                '628200-628899' => ['16-19'],
                '64'            => ['16-19'],
                '65'            => ['16-19'],
            ],
            'dankort' => [
                '5019' => ['16'],
                //'4571' => ['16'],// Co-branded with Visa, so it should appear as Visa
            ],
            'maestro' => [
                '6759'   => ['12-19'],
                '676770' => ['12-19'],
                '676774' => ['12-19'],
                '50'     => ['12-19'],
                '56-69'  => ['12-19'],
            ],
            'mastercard' => [
                '2221-2720' => ['16'],
                '51-55'     => ['16'],
            ],
            'unionpay' => [
                '81' => ['16'],// Discover 네트워크에서 Discover 카드로 취급됨
            ],
            'visa' => [
                '4' => ['13-19'],// 관련/파트너 브랜드 포함: Dankort, Electron 등 참고: 대부분의 Visa 카드는 16자리이며 일부 오래된 Visa 카드에는 13자리가 있을 수 있으며 Visa는 19자리 카드를 도입하고 있습니다.
            ],
        ];

        $foundCardBrand = '';
        
        $cardNumber = (string)$cardNumber;
        $cardNumber = str_replace(['-', ' ', '.'], '', $cardNumber);// 노이즈 제거
        
        if(in_array(substr($cardNumber, 0, 1), ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'])) {
            // "X" 및 "와 같이 숫자가 아닌 모든 값을 0으로 설정
            $cardNumber = preg_replace('/[^0-9]/', '0', $cardNumber);
            // 전달된 $cardNumber가 6자리 미만인 경우 오른쪽에 0을 추가하여 6으로 만듦
            $cardNumber = str_pad($cardNumber, 6, '0', STR_PAD_RIGHT);
            
            $firstSixDigits   = (int)substr($cardNumber, 0, 6);// Get first 6 digits
            $cardNumberLength = strlen($cardNumber);// Total digits of the card
            
            foreach($cardBrand as $brand => $rows) {
                foreach($rows as $prefix => $lengths) {
                    $prefix    = (string)$prefix;
                    $prefixMin = 0;
                    $prefixMax = 0;
                    if(strpos($prefix, '-') !== false) {// If "dash" exist in prefix, then this is a range of prefixes
                        $prefixArray = explode('-', $prefix);
                        $prefixMin = (int)str_pad($prefixArray[0], 6, '0', STR_PAD_RIGHT);
                        $prefixMax = (int)str_pad($prefixArray[1], 6, '9', STR_PAD_RIGHT);
                    } else {// This is fixed prefix
                        $prefixMin = (int)str_pad($prefix, 6, '0', STR_PAD_RIGHT);
                        $prefixMax = (int)str_pad($prefix, 6, '9', STR_PAD_RIGHT);
                    }

                    $isValidPrefix = $firstSixDigits >= $prefixMin && $firstSixDigits <= $prefixMax;// Is string starts with the prefix

                    if($isValidPrefix && !$validateLength) {
                        $foundCardBrand = $brand;
                        break 2;// Break from both loops
                    }
                    if($isValidPrefix && $validateLength) {
                        foreach($lengths as $length) {
                            $isValidLength = false;
                            if(strpos($length, '-') !== false) {// If "dash" exist in length, then this is a range of lengths
                                $lengthArray = explode('-', $length);
                                $minLength = (int)$lengthArray[0];
                                $maxLength = (int)$lengthArray[1];
                                $isValidLength = $cardNumberLength >= $minLength && $cardNumberLength <= $maxLength;
                            } else {// This is fixed length
                                $isValidLength = $cardNumberLength == (int)$length;
                            }
                            if($isValidLength) {
                                $foundCardBrand = $brand;
                                break 3;// Break from all 3 loops
                            }
                        }
                    }
                }
            }
        }
        
        return $foundCardBrand;
    }    
}