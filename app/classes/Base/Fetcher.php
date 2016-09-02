<?php
namespace App\Base;

abstract class Fetcher
{
  /** @var array Used for storing provider credentials, ie: username/password, token, etc.. */
  protected $credentials;

  /** @var int If provider query supports time range, this is the starting time */
  protected $from_unixtime;

  /** @var int If provider query supports time range, this is the end time */
  protected $to_unixtime;
  
  /** @var array Includes the query parameters, such as metric and dimension */
  protected $query;
  
  /** @var mixed The data object as returned by the provider */
  protected $data;

  public $provider;
  protected $url;
  public $session_name;
  


  /**
   * Execute the fetching sequence.
   */
  public function Execute ()
  {
    $this->InitializeProvider ();
    $this->FetchData ();
    return $this->DataAsText ();
  }
  public function logout(){
    unset($_SESSION[$this->session_name]);
    $redirect = $this->url;
    header('Location:'.filter_var($redirect, FILTER_SANITIZE_URL));
  }
  /**
   * Initialize provider object, set provider properties, etc.
   */
  abstract function InitializeProvider ();
  
  /**
   * Fetch data from the provider, and stores it in $this->data.
   */
  abstract protected function FetchData ();

  /**
   * Returns fetched data as text.
   * 
   * @return string
   */
  abstract protected function DataAsText ();
}

?>