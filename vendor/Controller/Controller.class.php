<?php
namespace Controller;
use Manager\EntityRepository ;
use Manager\PDOManager;
use Lib\Router;
use Lib\Session;
use Lib;


class Controller{

	protected $tableArray = array();
	
	public function getRepository($table) {
		$class = 'Repository\\'. $table . 'Repository'; 
		if(!isset($this->tableArray[$table])){
			$this->tableArray[$table] = new $class;
		}
		return $this->tableArray[$table];	
	}
	
	public function render($layout, $template, $parameters=array()){ 

		extract($parameters, EXTR_SKIP); 
		//extract transforme un tableau en variables
		//la variable EXTR_SKIP empeche la collision de variable.

		$dirViews = __DIR__ . '/../../src/' . str_replace('\\' , '/', get_called_class(). '/../../Views');
		$classNameArray = explode('\\', get_called_class());
		$dirFile = str_replace('Controller', '', end($classNameArray));
		
		// on récupère les dossiers de $template employe et layout
		$__template__ = $dirViews . '/' . $dirFile . '/' . $template;
		$__layout__ = $dirViews . '/' . $layout;
		
		ob_start(); // retiens en cache l'écriture du code suivant, jsuqu'a 'ob_end_flush'
			$s =  Lib\Session::getInstance();
			$r = new Lib\Router();
			require $__template__;
			$content = ob_get_clean(); // le fichier require est maintenant représenté par $content	
		
		ob_start();			 
			$r = Lib\App::getRouter();
			$bodyClasses = $r->getController();
			require $__layout__;
		
		return ob_end_flush();	
	}
	
	public function filterAdmin(){
        $session    = Session::getInstance();
        if (!$session->is_admin()) {
           $router = Lib\App::getRouter();
            $session->addError('Vous n\'avez pas accès connectedà cette partie du site');
            $router->redirect('home');
        }
        return $this;
    }	
	public function filterUser(){
        $session    = Lib\Session::getInstance();
        if (!$session->connected) {
           $router = App::getRouter();
            $session->addError('Vous devez être enregistré pour accèder à cette partie du site.');
            $router->redirect('home');
        }
        return $this;
    }	
    public function filterVisitor(){
        $session    = Session::getInstance();
        if ($session->connected) {
           $router = Lib\App::getRouter();
            $session->addError('Vous déjà connecté.');
            $router->redirect('home');
        }
        return $this;
    }

    public function postFile($fileInputName){
    	$session = Lib\App::getSession();
		$repertoireDestination = SITEURL."/images/rooms/";
		$nomDestination        = $_FILES[$fileInputName]["tmp_name"] ;

		if (is_uploaded_file($_FILES[$fileInputName]["tmp_name"])) {
		    if (rename($_FILES[$fileInputName]["tmp_name"],
		                   $repertoireDestination.$nomDestination)) {
		    	return $nomDestination;
		    } 
			else {
		       $session->addError('Erreur lors de l\'envoi du fichier');
		       return false;
		    }          
		} 
		else {
		     $session->addError('Le fichier n\'a pas été reçu, veuillez véfirier son poid');
		     return false;
		}

	}


}