<?php
try {

    $API_AGORA_URL = 'http://127.0.0.1:8000/api/login';


    $username = $_POST['username'];
    $password = $_POST['password'];


    $data = [
        "username" => $username,
        "password" => $password
    ];

    $curl = curl_init($API_AGORA_URL);
    curl_setopt($curl, CURLOPT_URL, $API_AGORA_URL);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
        "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
   // var_dump($resp);

    $respArray = json_decode($resp,true);

    //var_dump($respArray);

    if ($respArray['status']==1) {
        $response = [
            "status" => true,
            "access_token" =>  $respArray['access_token'],
            "id_user" =>  $respArray['id_user']
        ];
    }else{
        $response = [
            "status" => false
        ];
    }

    echo json_encode($response);

} catch (\Throwable $th) {
    $response = [
        "status" => false
    ];
    echo json_encode($response);
}
