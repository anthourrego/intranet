<?php
$max_salida=10; // Previene algun posible ciclo infinito limitando a 10 los ../
$ruta_raiz=$ruta="";
while($max_salida>0){
  if(is_file($ruta.".htaccess")){
    $ruta_raiz=$ruta; //Preserva la ruta superior encontrada
    break;
  }
  $ruta.="../";
  $max_salida--;
}
    

@session_start();
include_once($ruta_raiz.'clases/Session.php');
include_once($ruta_raiz.'lib/tcpdf/tcpdf.php');
set_time_limit(0);

$session=new Session();

class PDF {
    private $orientacion = 'L'; // P-vertical ,L-horizontal
    private $margenes = array(
        "superior" => "5",
        "inferior" => "5",
        "izquierda" => "5",
        "derecha" => "5"
    );
    private $font_size = "12"; // tama�o de la letra
    private $font_family; // tipo de letra
    private $tipo_salida = "I"; // I para mostrar en pantalla, FI para guardar en el servidor y mostrar en pantalla
    private $mostrar_encabezado = 0; // define si se muestra o no el encabezado y el pie de pagina
    private $pdf = ""; // variable que va a contener la instancia de la clase MYPDF
    private $papel; // tipo de papel a usar para la impresion LETTER.LEGAL,A4

    /*
     * constructor de la clase
     *
     */
    public function __construct($tipo_salida='I') {
            $this->tipo_salida = $tipo_salida; // para generarlo de nuevo y guardar la ruta
            $this->mostrar_encabezado = 1;
            $this->orientacion = "P";        
            $this->margenes = array(
              "izquierda" => 14,
              "derecha" => 14,
              "superior" => 35,
              "inferior" => 35
            );
             $this->font_size=12;       
             $this->formato='LETTER';
             $this->pdfa = true;
            //$this->font_family = '';

    }
    
   private function Encabezado() {
   		global $session;
            
        //Si esta en 0 no aplica la marca de agua, 1 si aplica
        $marca_agua = 1;
        if(@$session->get('marca_agua')){
           $marca_agua=@$session->get('marca_agua');
        }
       
            
        $this->pdf->set_header(@$session->get('encabezado'), $marca_agua);
 		$this->pdf->SetHeaderMargin(5);
        $this->pdf->setHeaderFont(Array(
            $this->font_family,
            '',
            $this->font_size
         ));

	}
    
	private function Pie(){
		global $session;
		
		$this->pdf->set_footer(@$session->get('pie'), 1);
        $this->pdf->SetFooterMargin($this->margenes["inferior"]);

        $this->pdf->setFooterFont(Array(
        $this->font_family,
            '',
            $this->font_size
        ));		
    }


	public function imprimir_inicio(){
		global $session;
		
		@ob_start();
  		error_reporting(E_ALL & ~E_NOTICE);
  		ini_set('display_errors', 0);
  		ini_set('log_errors', 1);	
		
		$titulo_proyecto=$session->get('autor');
			
        $this->pdf =new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
        $this->pdf->SetMargins($this->margenes["izquierda"], $this->margenes["superior"], $this->margenes["derecha"], 1);
        $this->pdf->AddFont($this->font_family);
        $this->pdf->SetFont($this->font_family, '', $this->font_size);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->pdf->SetAutoPageBreak(TRUE, $this->margenes["inferior"]);
        $this->pdf->SetTitle($titulo_proyecto); 
        $this->pdf->SetCreator($titulo_proyecto);
       
        $this->pdf->SetAuthor($titulo_proyecto);
        $tags='pdf,documento,'.$titulo_proyecto;
        $this->pdf->SetKeywords($tags);///tags PDF
        $this->pdf->SetSubject($titulo_proyecto);///AQUI DEBE IR EL ASUNTO DEL DOCUMENTO

        if($this->mostrar_encabezado) {
            $this->Encabezado();
			$this->Pie();
        } else {
            $this->pdf->setPrintHeader(false);
            $this->pdf->setPrintFooter(false);
        }
		
	}
    
    public function imprimir_fin() {
    	global $session;

        $ruta_pdf=__DIR__.'/'.@$session->get('PDF_SAVE'); //aqui va la ruta de almacenamiento del PDF
        //$paginas_pdf = $this->pdf->getNumPages();  //NUMERO DE PAGINAS DEL PDF
		ob_start();
		error_reporting(E_ALL & ~E_NOTICE);
		ini_set('display_errors', 0);
		ini_set('log_errors', 1);
 		@ob_end_clean();
        $valor=$this->pdf->Output($session->get('nombre_archivo') . ".pdf", $this->tipo_salida);
    }
    
    
    function imprimir_contenido($contenido='') {

            
        $this->pdf->startPageGroup();
        $this->pdf->AddPage($this->orientacion,$this->papel);
        
        
        $contenido = $contenido;
            
 	      // $contenido = str_replace("../../../images", PROTOCOLO_CONEXION . RUTA_PDF_LOCAL . "/../images", $contenido);
        $contenido = str_replace("<pagebreak/>", "<br pagebreak=\"true\"/>", $contenido);
        $contenido = str_replace("<p> </p>", "<p></p>", $contenido);
        $contenido = str_replace("<p>&nbsp;</p>", "<p></p>", $contenido);
        $contenido = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $contenido);
        $contenido = preg_replace('#onclick="(.*?)"#is', '', $contenido);
            
            
        $this->pdf->writeHTML(stripslashes($contenido), true, false, true, false, '');
 
    }
    
    function Anexos() {
       
       $this->pdf->Annotation(10, 5, 5, 5, "Anexos PDF", array(
                'Subtype' => 'FileAttachment',
                'Name' => 'etiqueta',
                'FS' => 'ruta'
       ));
        
    }
    


}
class MYPDF extends TCPDF{
    public $encabezado = "";
    public $pie_pagina = "";
    public $marca_agua = 0;
    
    public function Header() {
        global $session;
        $texto = str_replace("##PAGES##", "    " . $this->total_paginas(), $this->encabezado);
        $texto = str_replace("##PAGE##", $this->pagina_actual(), $texto);
        
       // $margin_top = preg_match('/attr-margin-top: .*;/', $texto, $coincidencias);
       // $margin_top = ( int ) preg_replace('/(attr-margin-top:)(.*);/', "$2", $coincidencias[0]);
        
        //$margin_left = preg_match('/attr-margin-left: .*;/', $texto, $coincidencias);
        //$margin_left = ( int ) preg_replace('/(attr-margin-left:)(.*);/', "$2", $coincidencias[0]);
        
       
            $margin_top = 0;
               
       
            $margin_left = 0;
       
        
        $this->writeHTMLCell(216, 0, $margin_left, $margin_top, stripslashes($texto), "", 1, 0, false, '', true);
        
        // $this->writeHTMLCell(216, 0, 0, 0, stripslashes($texto), "", 1, 0, false, '', true);
        
       
        $img_file =__DIR__. "/" . @$session->get('ruta_marca_agua');
         
        if($this->marca_agua) { // get the current page break margin
            $bMargin = $this->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $this->AutoPageBreak;
            // disable auto-page-break
            $this->SetAutoPageBreak(false, 0);
            // set bacground image
            $this->Image($img_file, 10, 50, 200, 197, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $this->setPageMark();
        }
    }
    
    public function Footer() {
        $texto = str_replace("##PAGES##", $this->total_paginas(), $this->pie_pagina);
        $texto = str_replace("##PAGE##", $this->pagina_actual(), $texto);
        
        $bottom_img = 243;
        $bottom_footer = 250;

        
        // muestra el pie de pagina en el pdf
        $this->writeHTMLCell(0, 0, '', $bottom_footer, stripslashes($texto), "", 1, 0, false, '', true);
    }
    
    function pagina_actual() {
        if(empty($this->pagegroups)) {
            return ($this->getAliasNumPage());
        } else {
            return ($this->getPageNumGroupAlias());
        }
    }
    
    function total_paginas() {
        if(empty($this->pagegroups)) {
            return ($this->getAliasNbPages());
        } else {
            return ($this->getPageGroupAlias());
        }
    }
    
    public function set_footer($texto) {
        $this->pie_pagina = $texto;
    }
    
    public function set_header($texto, $marca_agua) {
        $texto = str_replace("<p> </p>", "<p></p>", $texto);
        $texto = str_replace("<p>&nbsp;</p>", "<p></p>", $texto);
        $this->encabezado = $texto;
        $this->marca_agua = $marca_agua;
    }
}

if($session->get('imprimir')){
	
	$tipo_salida='I';
	if(@$session->get('tipo_salida')){
	   $tipo_salida=@$session->get('tipo_salida');
	}
	
	$pdf=new PDF($tipo_salida);
	$pdf->imprimir_inicio();
	if(is_array($session->get('html'))){
		$vector_contenido=$session->get('html');
		for($i=0;$i<count($vector_contenido);$i++){
			$pdf->imprimir_contenido($vector_contenido[$i]);
		}
	}else{
		$pdf->imprimir_contenido($session->get('html'));
	}
	

	$pdf->imprimir_fin();	
    
    $session->destroy('encabezado');
    $session->destroy('pie');
    $session->destroy('html');
    $session->destroy('autor');
    $session->destroy('imprimir');
    $session->destroy('marca_agua');
    $session->destroy('ruta_marca_agua');
    $session->destroy('nombre_archivo');
}else{
    echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
}
?>