<?php
/**
 * Created by PhpStorm.
 * User: oshry
 * Date: 29/08/2016
 * Time: 4:19 PM
 */

namespace App;
use App\Base\Fetcher;

class Googleplus extends Fetcher
{
    protected $client;
    protected $data;
    public $plus;
    public $token;
    //load dependencies and google client
    public function __construct (array $properties = []){
        foreach ($properties as $name => $value){
            if (property_exists ($this, $name)){
                $this->$name = $value;
            }
        }
        $this->provider = new \Google_Client();
        $this->provider->setClientId($this->credentials['client_id']);
        $this->provider->setClientSecret($this->credentials['client_secret']);
        $this->provider->setScopes('email');
        if(isset($this->token))
            $this->provider->setAccessToken($this->token);
        $redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
            FILTER_SANITIZE_URL);
        $this->provider->setRedirectUri($redirect);
        return $this;
    }
    //get code from provider with token save the token in a session and redirect main page 
    public function get_code(){
        $this->provider->authenticate($_GET['code']);
        $_SESSION[$this->session_name] = $this->provider->getAccessToken();
        $redirect = $this->url;
        header('Location:'.filter_var($redirect, FILTER_SANITIZE_URL));
    }
    //load google plus service with google client
    public function InitializeProvider(){
        $this->plus = new \Google_Service_Plus($this->provider);
    }
    //get data from service
    public function FetchData(){
        try{
            $this->plus_data = $this->plus->people->get('me');
            $this->data['id'] = $this->plus_data->id;
            $this->data['name'] = $this->plus_data->displayName;
            $this->data['email'] = $this->plus_data->emails[0]['value'];
            $this->data['profile_image_url'] = $this->plus_data->image['url'];
            $this->data['profile_url'] = $this->plus_data->url;

        } catch (\Google_Service_Exception $e) {
            $this->data = sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
            die($this->data);
        } catch (\Google_Exception $e) {
            $this->data = sprintf('<p>An client error occurred: <code>%s</code></p>',
                htmlspecialchars($e->getMessage()));
            die($this->data);
        }
    }
    //return data to template engine
    public function DataAsText(){
        return $this->data;
    }
}