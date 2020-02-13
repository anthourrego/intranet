<?php 

  $max_salida=10; // Previene algun posible ciclo infinito limitando a 10 los ../
  $ruta_raiz=$ruta="";
  while($max_salida>0){
    if(@is_file($ruta.".htaccess")){
      $ruta_raiz=$ruta; //Preserva la ruta superior encontrada
      break;
    }
    $ruta.="../";
    $max_salida--;
  }

  require_once($ruta_raiz . 'clases/define.php');


	class Session{

		private $ambiente;
		
		public function __construct(){
			$this->ambiente= RUTA_RAIZ;
		}	
		
		public function set($nombre,$valor){
			if(isset($_SESSION[$nombre.$this->ambiente])){
				$_SESSION[$nombre.$this->ambiente]='';
				unset($_SESSION[$nombre.$this->ambiente]);
			}
			$_SESSION[$nombre.$this->ambiente]=$valor;
		}
		
		public function get($nombre){
			$retorno=false;
			if(isset($_SESSION[$nombre.$this->ambiente])){
				$retorno=$_SESSION[$nombre.$this->ambiente];
			}
			return($retorno);	
		}
		
		public function destroy($nombre){
			if( isset($_SESSION[$nombre.$this->ambiente]) ){
				$_SESSION[$nombre.$this->ambiente]='';
				unset($_SESSION[$nombre.$this->ambiente]);
			}
		}
		
		public function exist($nombre){
			$retorno=false;
			if( isset($_SESSION[$nombre.$this->ambiente]) ){
				$retorno=true;
			}
			return($retorno);
		}
		
	}
?>