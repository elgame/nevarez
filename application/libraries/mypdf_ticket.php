<?php

class mypdf_ticket extends FPDF {
    var $limiteY = 0;
    var $titulo1 = 'Fumigaciones Aereas Nevarez';

    var $pag_size = array();

    var $fount_txt = 'helvetica';
    var $fount_num = 'SciFly-Sans';

    private $header_entrar = true;
	
	/**
	 * P:Carta Vertical, L:Carta Horizontal, lP:Legal vertical, lL:Legal Horizontal
	 * @param unknown_type $orientation
	 * @param unknown_type $unit
	 * @param unknown_type $size
	 */
	function __construct($orientation='P', $unit='mm', $size=array(63, 180)){
		parent::__construct($orientation, $unit, $size);
		$this->limiteY = 50;
        $this->pag_size = $size;

        $this->SetMargins(0, 0, 0);
        $this->SetAutoPageBreak(false);
	}
	
    //Page header
    public function Header() {
        $this->AddFont($this->fount_num, '');
        if ($this->header_entrar) {
            // Título
            $this->SetFont($this->fount_txt, 'B', 9);
            $this->SetXY(0, 0);
            $this->MultiCell($this->pag_size[0], 10, $this->titulo1, 0, 'C');

            $this->header_entrar = false;
        }
    }
    
    public function datosTicket($data){
        $this->SetFont($this->fount_txt, '', 9);
        $this->MultiCell($this->pag_size[0], 3, 'Folio: '.$data->folio, 0, 'R');
        $this->MultiCell($this->pag_size[0], 3, 'Fecha: '.$data->fecha, 0, 'R');

        $this->SetFont($this->fount_txt, 'B', 9);
        $this->SetXY(0, 12);
        $this->MultiCell($this->pag_size[0], 5, 'Datos Cliente', 0, 'L');
        
        $this->SetFont($this->fount_txt, '', 9);
        $this->MultiCell($this->pag_size[0], 3, 'Nombre: '.$data->nombre_fiscal, 0, 'L');
        $this->MultiCell($this->pag_size[0], 3, 'RFC: '.$data->rfc, 0, 'L');
        $this->MultiCell($this->pag_size[0], 3, 'Calle: '.str_replace('<br>', "\n", $data->domicilio), 0, 'L');
        if ($data->otros_clientes != NULL)
            $this->MultiCell($this->pag_size[0], 3, 'Otros Clientes: '.str_replace('<br>', ", ", $data->otros_clientes), 0, 'L');
    }

    public function productosTicket($data, $data_info){
        $this->SetWidths(array(8, 29, 14, 16));
        $this->SetAligns(array('L'));

        $this->SetFont($this->fount_txt, 'B', 9);
        $this->MultiCell($this->pag_size[0], 5, 'DETALLES', 0, 'C');

        $this->SetFont($this->fount_txt, '', 9);
        if(is_array($data)){
            foreach ($data as $vuelo){
                $this->SetX(0);
                $this->Row(array(
                    $vuelo->vuelos,
                    $vuelo->nombre.(($vuelo->matricula!='') ? ' | '.$vuelo->matricula : '' ),
                    // $vuelo->fecha,
                    String::formatoNumero($vuelo->precio,2),
                    String::formatoNumero($vuelo->importe,2)
                ), false, false);
            }
        }
        $this->CheckPageBreak(4);
        $this->MultiCell($this->pag_size[0], 3, '---------------------------------------------', 0, 'R');

        $this->SetWidths(array(45, 30));
        $this->SetAligns(array('R'));
        $this->Row(array( 'SubTotal: ', String::formatoNumero($data_info->subtotal,2,'$',false) ), false, false, 3);
        $this->Row(array( 'IVA: ', String::formatoNumero($data_info->iva,2,'$',false) ), false, false, 3);
        $this->Row(array( 'Total: ', String::formatoNumero($data_info->total,2,'$',false) ), false, false);
    }

    public function pieTicket($data){
        $this->SetFont($this->fount_txt, '', 8);
        $this->SetWidths(array($this->pag_size[0]));
        $this->SetAligns(array('L'));
        $this->Row(array( 'Debemos y Pagaré incondicionalmente a la orden de ROBERTO NEVAREZ DOMINGUEZ de este lugar de PISTA AEREA S/N, RANCHITO, MICHOACAN la Cantidad de' ), false, false);
        $this->SetY($this->GetY()-3);
        $this->Row(array( ''.String::formatoNumero($data->total,2,'$',false).' ('.String::num2letras($data->total,false,true).'), valor de la mercancía recibida a mi entera satisfacción. Este pagaré es mercantil y está regido por la Ley General de Títulos Y' ), false, false);
        $this->SetY($this->GetY()-3);
        $this->Row(array( 'Operaciones de Crédito en su artículo 173 parte final y artículos correlativos por no ser pagaré domiciliado. Si no es pagado antes de su vencimiento causara un interés del ____% mensual.' ), false, false);

        $this->SetY($this->GetY()+10);
        $pnew = $this->CheckPageBreak(6);
        if($pnew)
            $this->SetY($this->GetY()+10);
        $this->SetAligns(array('C'));
        $this->Row(array( '______________________________________' ), false, false);
        $this->Row(array( 'FIRMA' ), false, false);
    }

    public function printTicket($data){
        $this->datosTicket($data[1]['cliente_info'][0]);
        $this->productosTicket($data[1]['vuelos_info'], $data[1]['cliente_info'][0]);
        $this->pieTicket($data[1]['cliente_info'][0]);
    }


    var $col=0;
    
    function SetCol($col){
        //Move position to a column
        $this->col=$col;
        $x=10+$col*65;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }
    
    function AcceptPageBreak(){
        if($this->col<2){
            //Go to next column
            $this->SetCol($this->col+1);
            $this->SetY(10);
            return false;
        }else{
            //Regrese a la primera columna y emita un salto de página
            $this->SetCol(0);
            return true;
        }
    }
    
    
    
    
    /*Crear tablas*/
    var $widths;
    var $aligns;
    var $links;
    
    function SetWidths($w){
        $this->widths=$w;
    }
    
    function SetAligns($a){
        $this->aligns=$a;
    }
    
    function SetMyLinks($a){
        $this->links=$a;
    }
    
    function Row($data, $header=false, $bordes=true, $h=NULL){
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
            $h= $h==NULL? $this->FontSize*$nb+3: $h;
            if($header)
                $h += 2;
            $this->CheckPageBreak($h);
            for($i=0;$i<count($data);$i++){
                $w=$this->widths[$i];
                $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                $x=$this->GetX();
                $y=$this->GetY();
                
                if($header && $bordes)
                    $this->Rect($x,$y,$w,$h,'DF');
                elseif($bordes)
                    $this->Rect($x,$y,$w,$h);
                
                if($header)
                    $this->SetXY($x,$y+3);
                else    
                    $this->SetXY($x,$y+2);
                
                if(isset($this->links[$i]{0}) && $header==false){
                    $this->SetTextColor(35, 95, 185);
                    $this->Cell($w, $this->FontSize, $data[$i], 0, strlen($data[$i]), $a, false, $this->links[$i]);
                    $this->SetTextColor(0,0,0);
                }else
                    $this->MultiCell($w,$this->FontSize, $data[$i],0,$a);
                
                $this->SetXY($x+$w,$y);
            }
            $this->Ln($h);
    }
    
    function CheckPageBreak($h, $limit=0){
        $limit = $limit==0? $this->PageBreakTrigger: $limit;
        if($this->GetY()+$h>$limit){
            $this->AddPage($this->CurOrientation);
            return true;
        }
        return false;
    }
    
    function NbLines($w,$txt){
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j)
                        $i++;
                }else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }else
                $i++;
        }
        return $nl;
    }
    


    /**
     * indica si se abre el dialogo de imprecion inmediatamente
     * @param boolean $dialog [description]
     */
    function AutoPrint($dialog=false){
        //Open the print dialog or start printing immediately on the standard printer
        $param=($dialog ? 'true' : 'false');
        $script="print($param);";
        $this->IncludeJS($script);
    }


    /**
     * SOPORTE PARA INTRODUCIR JAVASCRIPT
     */
    var $javascript;
    var $n_js;

    function IncludeJS($script) {
        $this->javascript=$script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_out('<<');
        $this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_out('>>');
        $this->_out('endobj');
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/S /JavaScript');
        $this->_out('/JS '.$this->_textstring($this->javascript));
        $this->_out('>>');
        $this->_out('endobj');
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }
}


?>