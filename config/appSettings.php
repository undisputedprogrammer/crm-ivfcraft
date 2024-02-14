<?php

return [
    // 'phone_number_ids' => [
    //     1 => '123563487508047',
    //     2 => '123563487508047',
    //     3 => '123563487508047',
    //     4 => '123563487508047'
    // ],
    'webhook_token' => '$2y$10$QRNxTRTx4z2cmklbS6HzZuohvtt9mNwJ8UFeOnXQqyMlPwNRD74tW',

    // 'whatsapp_api' => [
    //     1 => [//craft
    //         'authkey' => '405736ABdKIenjmHR6501a01aP1',
    //         'bearer_token' => 'EAAPbFwsZCgIYBO9aafe6IzEGO5eamkI39OeoDqEfqdhcdjnt5ZBb7KU8RkbITbcL29OAC383UwVIdUOBDwpLb8Xkf0hrMSaKjOelHZAoEbZB24vdbSozsegkAPWAJduCLzZAq0mJQU7H1RO0LUqudlaUBIP2zQH0I4hq7eCYzccdk9LFtQID3JuQh4ztI8yr0hiZCe5TQx3qYvp661'
    //     ],
    //     2 => [
    //         'authkey' => '405736ABdKIenjmHR6501a01aP1',
    //         'bearer_token' => 'EAAJMkAJJSNoBOzVcLRAeseZC2rB5e1ERDarJVkohVeqMzUUBiZA20vbEwTkhZCXJ4R3gnbOXmgJbpeX9EZCsgotXjzaJJdJi3JIYpARO4lCwDgvkgAtyAqIUf5wtm9Cok8PI5bFQ3lSrdNe1xeKppRAcpDMpxisC6Tu6gxqCVNjTfvrcpLiEvbkOUteXb2Fc4vonZBIGBKV1ZAVQvh'
    //     ]
    // ],

    'lead_statuses' => [
        'Created',
        'Follow-up Started',
        'At Least Follow-up Started',
        'Appointment Fixed',
        'At Least Appointment Fixed',
        'Consulted',
        'At Least Consulted',
        'Continuing Medication',
        'Discontinued Medication',
        'Undecided On Medication',
        'Procedure Scheduled',
        'At Least Procedure Scheduled',
        'Completed',
        'Closed'
    ],

    'call_statuses' => ['Responsive', 'Not Responsive'],

    'lead_segments' => ['hot','cold','warm'],

    'forms' => ['New Lead','Internal Reference']
];

?>

