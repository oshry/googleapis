<?php
require_once('./env.php');
//load dependencies
$url = 'http://localhost:81/youtube.php';
$session_name = 'access_token';
$dependencies = [];
$data = [];
if(isset($_SESSION[$session_name])){
    $dependencies['token'] = $_SESSION[$session_name];
}
$dependency['client_id'] = '265470608538-ku33l8vg6jkqpoet0auvb94hurf8fd08.apps.googleusercontent.com';
$dependency['client_secret'] = 'bpYcDfRz2rnsUyBGgq70E5je';
$dependencies['credentials'] = $dependency;//protected
$dependencies['url'] = $url;//public
$dependencies['session_name'] = $session_name;//public
//Load Youtube Object with all iu's dependencies
$client = new \App\Youtube($dependencies);
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
    //search
    if (isset($_GET['q']) && isset($_GET['maxResults'])) {
        $data['results'] = $client->Execute();
        $data['list'] = true;
    }
    $data['login'] = false; $data['logout'] = true; $data['search'] = true;
}else{
    $authUrl = $client->provider->createAuthUrl();
    $data['login'] = true; $data['logout'] = false; $data['url'] = $authUrl;
}
echo $m->render('layout_youtube', $data);
