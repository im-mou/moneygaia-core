<?php

return [

    "validate" => [
        "money" => 'regex:/(?=.*?[0-9])(^-?\d+\.\d{2}$)|(?=.*?[1-9])(^\d{1,}$)/us'
    ],

];
