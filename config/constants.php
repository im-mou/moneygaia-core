<?php

return [

    "validate" => [
        "money" => 'regex:/(?=.*?[0-9])(^-?\d+\.\d{1,2}$)|(?=.*?[1-9])(^\d{1,}$)/us'
    ],

    "pagination" => [
        "per_page" => 25
    ],

    "user_info_default_date_format" => 'm/d/Y',

];
