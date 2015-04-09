<?php

namespace Lib;
use Repository\MembreRepository;

class Session{

  private static $instance = null;
  private static $user = null;
  private $sessionStared = false;
  private $objCollection = array();
  public $connected;

  private function __construct(){
    $this->start();
    if($this->exists('user')){
      $repository = new MembreRepository;
      self::$user = $repository->find($this->get('user'));
      $this->connected = true;
    }
    else{
      $this->connected = false;
    }
    if(!$this->exists('success')){
      $this->set('success', array());
    }
  }

  public static function getInstance(){
    if(is_null(self::$instance)) {
      self::$instance = new Session();
    }
    return self::$instance;
  }

  private function start(){
    if($this->sessionStared === false){
      session_start();
    }
    else{
      $this->sessionStared = true;
    }
  }  

  public function destroy(){
    session_unset();
    session_destroy();
  }

  public function exists($name) {
    return(isset($_SESSION[$name])) ? true : false;
  }

  public function set($name, $value) {
    return $_SESSION[$name] = $value;
  }

  public function get($name) {
    if($this->exists($name))
      return $_SESSION[$name];
    else return false;
  }

  public function delete($name) {
    if($this->exists($name)) {
      unset($_SESSION[$name]);
      return true;
    }
    return false;
  }

  public function flash($name, $string = '') {
    if($this->exists($name)) {
      $session = $this->get($name);
      $this->delete($name);
      return $session;
    } 
    else {
      $this->set($name, $string);
    }
  }

  public function setUser($id){
    $this->delete('user');
    $this->set('user', $id);
    $repository = new MembreRepository;
    self::$user = $repository->find($this->get('user'));
  }

  public function getUser(){
    if(is_null(self::$user)) {
      return false;
    }
    else {
      return self::$user;
    }
  }

  public function is_admin(){
    if($this->is_connected() && self::$user->getRole() == 'ROLE_ADMIN'){
      return true;
    }
    return false;
  }
  public function is_connected(){
    if (is_object(self::$user)) {
      return true;
    }
    return false;
  }

  public function display(){ 
  }

  public function front($object){
    $array = $this->objCollection;
    $array[] = $object;
    $this->objCollection = $array;
    return $this;
  }

  public function addSuccess($message){ 
    $succesMessages = $this->exists('success') ? $this->get('success') : array();
    $succesMessages[] = $message;
    $this->set('success', $succesMessages);
    //print_r();
    return $this;
  }

  public function addError($message){
    $errorMessages = $this->get('error');
    $errorMessages[] = $message;
    $this->set('error', $errorMessages);
    return $this;
  }

  public function toHtml(){
    $success = $this->get('success');
    $errors  = $this->get('error');
    $objects = $this->objCollection;

    if(empty($success) && empty($errors) && empty($objects) ){
      return false;
    }

    $html = '<div class="messages">';
    foreach ($objects as $object) {
      $html .= '<pre class="debug">';
      $html .= print_r($object, true);
      $html .= '</pre>';
    }
    if (!empty($success)) {
      $html .= '<ul class="success-list">';
      foreach ( $success as $message) {
        $html .= '<li class="message">';
        $html .= $message;
        $html .= '</li>';
      }
      $html .= '</ul>';
    }
    if (!empty($errors)) {
      $html .= '<ul class="errors-list">';
      foreach ( $errors as $message) {
        $html .= '<li class="message">';
        $html .= $message;
        $html .= '</li>';
      }
      $html .= '</ul>';
    }
    $html .= '</div>';
    $this->delete('success');
    $this->delete('error');
    return $html;
  }

  // front-name of tohtml()
  public function html(){
    echo $this->toHtml();
  }
  
}