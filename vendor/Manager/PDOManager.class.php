<?php
namespace Manager;
use App\Config;

class PDOManager {
	private static $instance = null;
	protected $pdo;

	// magic functions 
	private function __construct(){
	}

	private function __clone(){  //mettre __clone en privé empeche le clonage
	}

	// Custom functions 
	public static function getInstance(){
		if(is_null(self::$instance)) {
				self::$instance = new self;  // on peut mettre self pour auto-instancier, mais a tester si arguments constructeur ?
			}
		return self::$instance;
	}
	
	public function getPdo(){
		$config = Config::getInstance();  //anti-slash pour retourner dans le namesapce "global"
		$connect = $config->getParametersConnect();
		try {
				$host 	= $connect['host']; 
				$db 	= $connect['dbname'];
				$charset= $connect['charset'];
				$usr 	= $connect['user'];
				$pwd	= $connect['password'];
				$this->pdo = new \PDO(
					"mysql:host=$host; dbname=$db;charset=$charset",
					$usr, 
					$pwd,
					array(\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION)
				);
				return $this->pdo;
		}
		catch(\PDOException $e){
			echo 'echec de connexion : ' . $e->getMessage();
		}
	}
}