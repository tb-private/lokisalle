<?php

namespace Lib;

class App{

  private static $instance = null;
  public  static $user     = null;
  public  static $request  = null;
  public  static $session  = null;
  public  static $router   = null;

  private function __construct(){
    self::$request = new Request;
    self::$router  = new Router(true);
    self::$session = Session::getInstance();
    self::$user = self::$session->getUser();
  }

  public static function getInstance(){
    if(is_null(self::$instance)) {
      self::$instance = new App();
    }
    return self::$instance;
  }

  public function run(){
    $controller = self::$router->getController();
    $params     = self::$router->getParams();
    self::$router->run($controller, $params);
  }  

  public static function getUser(){
    if(is_null(self::$user)) {
      return false;
    }
    else {
      return self::$user;
    }
  }
  public static function getRequest(){
    if(is_null(self::$request)) {
      return false;
    }
    else {
      return self::$request;
    }
  }
  public static function getSession(){
    if(is_null(self::$session)) {
      return false;
    }
    else {
      return self::$session;
    }
  }  
  public static function getRouter(){
    if(is_null(self::$router)) {
      return false;
    }
    else {
      return self::$router;
    }
  }  
  public static function redirect(){
    if(is_null(self::$router)) {
      return false;
    }
    else {
      return self::$router;
    }
  }




}