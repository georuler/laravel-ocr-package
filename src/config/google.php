<?php
return [
    //구글 단어별 신뢰도 값
    'wordConfidenceMin' => 0.6,
    
    // 카드 번호 길이 체크
    'cardNumberLenth' => 16,

    //이름 최소 길이 체크
    'nameMinLenth' => 3,

    //유효 기간 문자열 길이
    'validityLength' => 5,

    //유효 기간 문자열 길이 체크
    'validityMaxLength' => 30,

    //유효 기간 포함 필터
    'validityFilter' => ['/', '-'],

    //유효기간 정규식
    'validityRegex_before' => '/^(?=.*[0-9]{2})(?=.*[\/-])(?=.*[0-9]{2})/i',

    'validityRegex' => '/[0-9\/-]{3}[0-9]{2}/',

    
    //카드 브랜드 필터
    'cardBrand' => [
        'BC', 'MASTER', 'VISA', 'HANA', 'KEB', 'SUHYUP', 'GMONEY', 'GMONEY TRANS', 
        'WOORICARD', 'WOORI CARD', 'WOORICAR',
        'SAMSUNG CARD', 'SAMSUNGCARD', 
        'SHINHAN CARD', 'SHINHANCARD',
        'HANA CARD', 'HANACARD', 'HANA MEGA', 'HANAMEGA'
    ],

    //문자열 필터
    'strFilter' => [
        'CHECK', 'BANK', 'KOREA', 'AUTHORIZED', 'MEMBER', 'MEMBERS', 'CASHBAG', 'CASHIDAG',
        'AIR ALITHORIZED SIGNATURE', 'AIR ALITHORIZED SIGNATURE',
        'RESIDENCE CARD', //외국인 등록증
        'VALID', 'THRU', /* 'SH', */ 'DIRECT', 'COOKIE', 'PREMIER',
        'ANA CARD', 'HANA EX', 'IDENTITY CARD',
        'MONTHYEAR', 'MONTH YEAR', 'MONTH/YEAR', 'MONTHIYEAR', 'MONTHAYEAR',
        'ACCOUNT NUMBER',
        'YONSEI UNIVERSITY', 'UNIVERSITY', 'YONSEI', 'YONSEL UNIVERSITY',
        'PLATINUM', 'PLATINU',
        'CJONE', 'SHINSEGAE',
        'SKYPASS', 'THAU',
        'DISCOUNT', 'INTERNATIONAL', 'FRIENDS', 'SIGNATURE', 'DOUBLE',
        'CHANG HAN', //우리카드  38363

        //카카오 카드 
        'CHEEZZZ',

        //하나카드
        'VIVAX', 'VIVA X',
    ],

    //베트남 문자열 
    'vietnamString' => 'À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ|È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ|Ì|Í|Ị|Ỉ|Ĩ|Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ|Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ|Ỳ|Ý|Ỵ|Ỷ|Ỹ|Đ',


    //외국인 등록증 관련 설정 값
    'alien' => [
        //외국인 등록 번호 길이 체크
        'alienNumberLenth' => 13,
        'alienNumberMinLenth' => 3,

        //발급일자 길이 체크
        'alienIssueDateMinLenth' => 8,

        //외국인 등록 번호 필터
        'alienNumberFilter' => ['-'],        

        'alienNameMinLenth' => 3,

        //문자열 제외 필터
        'strExceptFilter' => [
            'KOREA', 'KOREA IM', 'KOREA IMMIGRATION', 'KOREA KOR', 'KOREA TOR', 'KOREA IMIGRAT', 'KOREAL', 'KORD', 'NICE KOR', 'NCE KOR',
            'CE KOREA', 'BUICE KOREA', 'KOREA IN', 'RVICE KOREA', 'SERVICE DREA', 'NACE KOREA', 'ICE KOREA',
            'VICE KOR', 'KOREAM KOR', 'KOREA IM GRATI', 'KOR KOR', 'VICE REA', 'E  KOR',
            
            'CHELD CAR', 'CHELD CARD', 'NICE CARD', 'NCE CARD', 
            'KORT', 'GRAT',
            'IMMIGRATION',
            'ALIEN REGISTRATION CARD', 'RESIDENCE CARD', 'ALIEN RECISTRATION CARD', 'ALIEN RECOUNTRATION CARO', 'LIEN REGISTRATION CARD', 'RESIDENCE',
            'A IIGRATION SERVICE',
            'STRATION CARD',
            'SCERVICE KOREA', 'CERVICE KOREA', 'ERVICE KOREA',
            'SERVICE', 'SERVICELKO',

            'ISPLIED', 'UBUSINED', 'CHATURTHENOPANEL',

            'CEO RICAL',
            'ARARASION BETIN', 'DAERVICE', 'TOTALEAWAREAL', 'ONERVICE', 'WHERATSH',
            'IMMIGRAFOL',


            //확인필요
            'IGRAT'
        ],

        'nationalFilter' => [
            //아시아
            'BANGLADESH', 'MYANMAR', 'CAMBODIA', 'VIETNAM', 'SRI LANKA', 'NEPAL', 'INDONESIA', 'THAILAND', 'PHILIPPINES',
            'MONGOLIA', 'UZBEKISTAN', 'PAKISTAN', 'KAZAKHSTAN', 'CHINA', 'JAPAN', 'EAST - TIMOR',

            //남미
            'BRAZIL', 
            
            //북미
            'MEXICO',

            //아프리카
            'GHANA', 'EGYPT',

            //유럽
            'RUSSIA',
        ]
    ]
];

