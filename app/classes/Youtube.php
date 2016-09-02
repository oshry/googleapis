<?php
/**
 * Created by PhpStorm.
 * User: oshry
 * Date: 29/08/2016
 * Time: 4:19 PM
 */

namespace App;
use App\Base\Fetcher;

class Youtube extends Fetcher
{
    protected $client;
    protected $data;
    public $youtube;
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
        $this->provider->setScopes('https://www.googleapis.com/auth/youtube');
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
    //load youtube service with google client
    public function InitializeProvider(){
        $this->youtube = new \Google_Service_YouTube($this->provider);
    }
    //get data from service
    public function FetchData(){
        try{
            $this->data = $this->youtube->search->listSearch('id,snippet', array(
                'q' => $_GET['q'],
                'maxResults' => $_GET['maxResults'],
            ));
        } catch (\Google_Service_Exception $e) {
            $this->data = sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
            die($this->data);
        } catch (\Google_Exception $e) {
            die($this->data);
            $this->data = sprintf('<p>An client error occurred: <code>%s</code></p>',
                htmlspecialchars($e->getMessage()));
        }
    }
    //return data to template engine
    public function DataAsText(){
        $videos = [];
        $channels = [];
        $playlists = [];
        foreach ($this->data['items'] as $searchResult) {
            switch ($searchResult['id']['kind']) {
                case 'youtube#video':
                    $videos[] = ['id' => $searchResult['id']['videoId'], 'title' => $searchResult['snippet']['title']];
                    break;
                case 'youtube#channel':
                    $channels[] = ['id' => $searchResult['id']['channelId'], 'title' => $searchResult['snippet']['title']];
                    break;
                case 'youtube#playlist':
                    $playlists[] = ['id' => $searchResult['id']['playlistId'], 'title' => $searchResult['snippet']['title']];
                    break;
            }
        }
        $results['videos'] = $videos;
        $results['channels'] = $channels;
        $results['playlists'] = $playlists;
        return $results;
    }
}