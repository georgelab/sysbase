<?php

$valid_verbs = ['get','post','put','delete'];
if (
    isset($_GET['verb'])
    && in_array(mb_strtolower($_GET['verb']),$valid_verbs)
) {

    $ch     = curl_init();
    $head   = [
        'Content-Type:application/json',
    ];

    if ($_GET['verb'] == 'get') {
        $URL    = 'http://localsysbase:85/api/endpoint/1';
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    } else if ($_GET['verb'] == 'post') {
        $URL    = 'http://localsysbase:85/api/';
        $cdata = ['endpoint'=> 'casa', 'args'=>['cor'=>'verde']];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cdata));
    } else if ($_GET['verb'] == 'put') {
        $URL    = 'http://localsysbase:85/api/';
        $cdata = ['endpoint'=> 'casa', 'args'=>['id'=>'1','cor'=>'verde']];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cdata));

    } else if ($_GET['verb'] == 'delete') {
        $URL    = 'http://localsysbase:85/api/';
        $cdata = ['endpoint'=> 'casa', 'args'=>['id'=>'1']];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cdata));
    }

    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_USERPWD, "87a6std9a6sd8fdf5sf:");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);


    $output = curl_exec($ch);
    curl_close($ch);

    var_dump($output);
}



