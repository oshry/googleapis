<?php
require_once('./env.php');
//load dependencies
$url = 'http://localhost:81/googleplus.php';
$session_name = 'access_token1';
$dependencies = []; 
$data = [];
$dependency['client_id'] = '265470608538-pq800s9li4ntvu417lvj7coa6tlcsrvm.apps.googleusercontent.com';
$dependency['client_secret'] = '9udXn_zTvinBSnLOMn_GTE-P';
if(isset($_SESSION[$session_name])){
    $dependencies['token'] = $_SESSION[$session_name];
}
$dependencies['credentials'] = $dependency;//protected
$dependencies['url']= $url;//public
$dependencies['session_name'] = $session_name;//public
//Load Googleplus Object with all iu's dependencies  
$client = new \App\Googleplus($dependencies);
//logout
if (isset($_GET['logout'])){
    $client->logout();
}
//api request
if(isset($_GET['code'])){
    $client->get_code();
}
//vaild token
if(isset($client->token) && $client->token){
    //start process
    $data['results'] = $client->Execute();
    $data['show'] = true;$data['login'] = false; $data['logout'] = true;
}else{
    $authUrl = $client->provider->createAuthUrl();
    $data['login'] = true; $data['logout'] = false; $data['url'] = $authUrl;
}
echo $m->render('layout_googleplus', $data);
