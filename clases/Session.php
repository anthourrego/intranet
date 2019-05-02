<?php  
	class Session{

		private $ambiente;
		
		public function __construct(){
			$this->ambiente="intranet";
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