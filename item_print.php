<?php
	require_once('auth.php');

	require_once('config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}
	
	require_once('../tcpdf/config/lang/eng.php');
	require_once('../tcpdf/tcpdf.php');

// extend TCPF with custom functions
class MYPDF extends TCPDF {

    // Page footer
    public function Footer() {
		global $f_update_date;
	
		$cur_y = $this->GetY();
		$ormargins = $this->getOriginalMargins();
		$this->SetTextColor(0, 0, 0);
		//set style for cell border
		$line_width = 0.85 / $this->getScaleFactor();
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Set font
		$this->SetFont('helvetica', 'I', 8);
		$update = "SELECT * FROM `update` WHERE member_id=".$_SESSION['SESS_MEMBER_ID'];
		$update_res = mysql_query($update) or die(mysql_error());
		$update_row = mysql_fetch_row($update_res);
        // Page number
		$update_date = date("F j, Y",$update_row[1]);
		$f_update_date = date("M_d_Y",$update_row[1]);
		$this->Cell(0, 10, 'Inventory as of '.$update_date, 'T', 0, 'C');
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'R');
    }
    
    // Colored table
    public function ColoredTable($header,$data) {
		global $total_val;
		
        // Colors, line width and bold font
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(0);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
		if(($_POST['values']) == 1) {
			$w = array(50, 50, 18, 23, 57, 20);
		} else {
			$w = array(60, 60, 28, 43, 67);
		}
        for($i = 0; $i < count($header); $i++)
        $this->Cell($w[$i],0,$header[$i],'B',0,'C');
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
		$fill = 0;
		$dimensions = $this->getPageDimensions();
		$hasBorder = false; //flag for fringe case
		 
		foreach($data as $row) {
			$rowcount = 0;
			
			//work out the number of lines required
			$rowcount = max($this->getNumLines($row[1], $w[0]),$this->getNumLines($row[2], $w[1]),$this->getNumLines($row[3], $w[2]),$this->getNumLines($row[4], $w[3]),$this->getNumLines($row[5], $w[4]));			
			 
			$startY = $this->GetY();
		 
			if (($startY + $rowcount * 5) + $dimensions['bm'] > ($dimensions['hk'])) {
				//this row will cause a page break, draw the bottom border on previous row and give this a top border
				//we could force a page break and rewrite grid headings here
				$this->AddPage('L','LETTER');
				$this->SetFont('', 'B');
				for($i = 0; $i < count($header); $i++)
				$this->Cell($w[$i],0,$header[$i],'B',0,'C');
				$this->Ln();
				$this->SetFont('');
				if ($hasborder) {
					$hasborder = false;
				} else {
					$this->Cell(240,0,'',0); //draw bottom border on previous row
					$this->Ln();
				}
				$borders = 0;
			} elseif ((ceil($startY) + $rowcount * 5) + $dimensions['bm'] == floor($dimensions['hk'])) {
				//fringe case where this cell will just reach the page break
				//draw the cell with a bottom border as we cannot draw it otherwise
				$borders = 0;	
				$hasborder = true; //stops the attempt to draw the bottom border on the next row
			} else {
				//normal cell
				$borders = 0;
			}
		 
			//now draw it
			$this->MultiCell($w[0],$rowcount * 5,stripslashes($row[1]),$borders,'L',$fill,0);
			$this->MultiCell($w[1],$rowcount * 5,stripslashes($row[2]),$borders,'L',$fill,0);
			$this->MultiCell($w[2],$rowcount * 5,stripslashes($row[3]),$borders,'L',$fill,0);
			$this->MultiCell($w[3],$rowcount * 5,stripslashes($row[4]),$borders,'L',$fill,0);
			$this->MultiCell($w[4],$rowcount * 5,stripslashes($row[5]),$borders,'L',$fill,0);
			if(($_POST['values']) == 1) {
				$total_val += $row[6];
				$this->MultiCell($w[6],$rowcount * 5,'$'.stripslashes($row[6]),$borders,'L',$fill,0);
			}
		 
			$this->Ln();
			$fill=!$fill;
		}
		 
		$this->Cell(240,0,'',0);  //last bottom border
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Dariush Molavi');
$pdf->SetTitle('Model Train Inventory');
$pdf->SetSubject('Model Trains');
$pdf->SetKeywords('trains, model railroad, model train, railroad, inventory');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

//set some language-dependent strings
$pdf->setLanguageArray($l); 

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage('L','LETTER');

$total_val = 0;	
$f_update_date = "";
$header = array('Manufacturer', 'Roadname', 'Road No.', 'Type', 'Description');
$heading_string = "Filters used: ".$_POST['sSearch'];
$pdf->SetFont('', 'B',12);
$pdf->MultiCell(0,0,$heading_string,0,'C',0,1);	
$pdf->SetFont('','',10);

$aColumns = array( 'i_index', 'm_name', 'r_roadname', 'i_roadnumber', 't_type', 'i_description', 'i_partnumber','i_value', 's_scale' );

/* Ordering */
if ( isset( $_POST['iSortCol_0'] ) )	{
	$sOrder = "ORDER BY  ";
	for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ ) {
		if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" ) {
			$sOrder .= $aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."
				".mysql_real_escape_string( $_POST['sSortDir_'.$i] ) .", ";
		}
	}
	$sOrder = substr_replace( $sOrder, "", -2 );
	if ( $sOrder == "ORDER BY" ){
		$sOrder = "ORDER BY m.m_name";
	}
}		

/* Filtering */
$sWhere = "";
if ( $_POST['sSearch'] != "" ) {
	$sWhere = " AND (";
	for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
		$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_POST['sSearch'] )."%' OR ";
	}
	$sWhere = substr_replace( $sWhere, "", -3 );
	$sWhere .= ')';
}

/* Individual column filtering */
for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
	if ( $_POST['bSearchable_'.$i] == "true" && $_POST['sSearch_'.$i] != '' )	{
		if ( $sWhere == "" ) {
			$sWhere = " AND ";
		} else {
			$sWhere .= " AND ";
		}
		$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_POST['sSearch_'.$i])."%' ";
	}
}

$sQuery = "SELECT SQL_CALC_FOUND_ROWS i.i_index, m.m_name, r.r_roadname, i.i_roadnumber, t.t_type, i.i_description, i.i_partnumber, i.i_value, s.s_scale FROM item i, manufacturer m, roadnames r, type t, scale s WHERE i.i_ownerid=".$_POST['member_id']." AND i.i_manufacturer = m.m_index AND i.i_roadname = r.r_index AND i.i_type = t.t_index AND i.i_scale = s.s_id ".$sWhere." ".$sOrder;
$rResult = mysql_query( $sQuery ) or die(mysql_error());

$sQuery = "SELECT FOUND_ROWS()";
$rResultFilterTotal = mysql_query( $sQuery ) or die(mysql_error());
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

$sQuery  = "SELECT COUNT('i_index') FROM item WHERE i_ownerid=".$_POST['member_id'];
$rResultTotal = mysql_query( $sQuery ) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];

$output = array( "sEcho" => intval($_POST['sEcho']), "iTotalRecords" => $iTotal,	"iTotalDisplayRecords" => $iFilteredTotal,"aaData" => array());
while($aRow = mysql_fetch_array($rResult)) {
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
		$row[] = $aRow[ $aColumns[$i] ];
	}				
	$output['aaData'][] = $row;
}

// print colored table
$pdf->ColoredTable($header, $output['aaData']);
$pdf->Ln();		
//Close and output PDF document
$filename = $f_update_date.'_inventory.pdf';
$pdf->Output('temp/'.$filename, 'F');		
echo 'temp/'.$filename;
?>		