<?php

namespace Lib;
use App\Config;
use Lib\Session;
use Backoffice;

class Router{

 private $request;
 private $controller;
 private $params = array();
 private $config;
 private $routes;

 public function __construct($autoRoute = false){
  $this->config = Config::getInstance();
  $this->routes = $this->config->getRoutesConfig();
  $this->request = new Request;
  if($autoRoute)
    $this->findRequestedRoute();
 }

 public function setController($key){
  $this->controller = $key;
  return $this;
 }

public function setParams($array = array()){
  $this->params = $array;
  return $this;
}

 public function getController(){
  return $this->controller;
 }

public function getParams(){
  return $this->params;
}

  public function run($routeName, $options = NULL){
   	if(empty($this->routes))
   		$this->routes = $this->config->getRoutesConfig();

   	if(isset($this->routes[$routeName])){
      $route = explode(":", $this->routes[$routeName]['defaults']['controller']);

      if ($options != NULL && is_array($options)){
  			$options = implode($options, ', ');
      }
      $controllerName = 'Backoffice\Controller\\'.$route[0].'Controller';
      $action = $route[1] . 'Action';
      $controller = new $controllerName;
      $controller->$action($options);
    }
    else{
      $controller = new Backoffice\Controller\defaultController;
      $controller->noRouteAction();
    }
  }

  public function getRoute($key, $options = NULL){
    $this->routes = $this->config->getRoutesConfig();
    if(isset($this->routes[$key]['url'])){
      $route = $this->routes[$key]['url'];
      if ($options != NULL && is_array($options)){
				foreach ($options as $option => $value) {
					$route = str_replace("{". $option."}", $value , $route);
				}
      }
    }
    else {
    	$route = '/404';	
    }
    return SITEURL . $route;
  }

  public function getRouteLink($route, $content = null, $classes = null, $title = null){
    $text = 'Cliquez ici';
    if(is_string($content)){
      $text = $content;
    }
    if(is_string($route)){
      return "<a title=\"$title\" href=\"" .$this->getRoute($route) . '" class="'. $classes .'">' . $text . '</a>';
    }
    elseif(is_array($route)){
      $routeName = $route[0];
      unset($route[0]);
      $options = $route;
      return "<a title=\"$title\" href=\"" .$this->getRoute($routeName, $options) . '" class="'. $routeName .'">' . $text . '</a>';
    }
    else return false;
  }

  // Front name of getRoute()
  public function route($key, $options = NULL){
    echo $this->getRoute($key, $options);
  }

  public function findRequestedRoute(){
    $url            = parse_url($this->request->requestURI());
    $req            = explode('/', strtolower($url['path']));
    if (empty($req[0]))   array_shift($req);
    $end = end($req);
    if (empty($end)) array_pop($req);
    $req = array_slice($req, CONTROLLER_POS); // sub-folder correction (defined in index.php)
    $index = 0;

    if (empty($req)) {
      $this->controller = 'home';
    }
    else{   
      
      if($req[0] == 'admin'){
        $session = Session::getInstance();
        if(!$session->is_admin()){
            $session->addError('Vous n\'avez pas accès à cette partie du site');
            $this->redirect('home');
        }
        if (sizeof($req) == 1) {
          $this->controller = 'admin_home';
          break;
        }
        $index++;
      }
      
      $controllerHint = $req[$index];
      $params = array_slice($req, $index+1);

      foreach($this->routes as $key => $value){
        $url = explode('/', $value['url']);
        if (empty($url[0])){  array_shift($url);}
        if (!array_key_exists($index, $url)) continue;

        if( $controllerHint == $url[$index] && empty($params) && $index+1 == sizeof($url) ){
          $this->controller = $key;
          break;
        }
        elseif($url[$index] == $controllerHint &&  count($params)+$index+1 == sizeof($url)){
          $this->controller = $key;
          $this->params     = $params;
          break;
        }

      }
    }
  }
  public function redirect($route){
    header('Location: '.$this->getRoute($route));
    exit;
  }
}