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

$f_update_date = "";

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
    
    // Load table data from file
    public function LoadData($query) {
		$res = mysql_query($query);
		$data = array();
		while($row = mysql_fetch_row($res)) {
			$data[]=$row;
		}
		return $data;
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
			$w = array(50, 20, 50, 18, 23, 57, 20);
		} else {
			$w = array(50, 20, 50, 18, 23, 57);
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
			$rowcount = max($this->getNumLines($row[3], $w[0]),$this->getNumLines($row[0], $w[1]),$this->getNumLines($row[4], $w[2]),$this->getNumLines($row[2], $w[3]),$this->getNumLines($row[5], $w[4]),$this->getNumLines($row[1], $w[5]));			
			 
			$startY = $this->GetY();
		 
			if (($startY + $rowcount * 6) + $dimensions['bm'] > ($dimensions['hk'])) {
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
			} elseif ((ceil($startY) + $rowcount * 6) + $dimensions['bm'] == floor($dimensions['hk'])) {
				//fringe case where this cell will just reach the page break
				//draw the cell with a bottom border as we cannot draw it otherwise
				$borders = 0;	
				$hasborder = true; //stops the attempt to draw the bottom border on the next row
			} else {
				//normal cell
				$borders = 0;
			}
		 
			//now draw it
			$this->MultiCell($w[0],$rowcount * 6,stripslashes($row[3]),$borders,'L',$fill,0);
			$this->MultiCell($w[1],$rowcount * 6,stripslashes($row[0]),$borders,'L',$fill,0);
			$this->MultiCell($w[2],$rowcount * 6,stripslashes($row[4]),$borders,'L',$fill,0);
			$this->MultiCell($w[3],$rowcount * 6,stripslashes($row[2]),$borders,'L',$fill,0);
			$this->MultiCell($w[4],$rowcount * 6,stripslashes($row[5]),$borders,'L',$fill,0);
			$this->MultiCell($w[5],$rowcount * 6,stripslashes($row[1]),$borders,'L',$fill,0);
			if(($_POST['values']) == 1) {
				$total_val += $row[6];
				$this->MultiCell($w[6],$rowcount * 6,'$'.stripslashes($row[6]),$borders,'L',$fill,0);
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

//Column titles
if($_POST['values'] == 1) {
	$header = array('Manufacturer', 'Part No.', 'Roadname', 'Road No.', 'Type', 'Description', 'Value');
} else {
	$header = array('Manufacturer', 'Part No.', 'Roadname', 'Road No.', 'Type', 'Description');
}
if($_POST['complete'] == 1) {
	$s_query = "SELECT DISTINCT i.i_scale, s.s_scale FROM item i, scale s WHERE i.i_scale = s.s_id";
	$s_res = mysql_query($s_query);

	while($row = mysql_fetch_row($s_res)) {

		$complete_query = "SELECT i.i_partnumber, i.i_description, i.i_roadnumber, m.m_name, r.r_roadname, t.t_type, i.i_value FROM item i, manufacturer m, roadnames r, type t WHERE i.i_ownerid=".$_SESSION['SESS_MEMBER_ID']." AND i.i_manufacturer = m.m_index AND i.i_roadname = r.r_index AND i.i_type = t.t_index AND i.i_scale = ".$row[0]." ORDER BY m.m_name, i.i_partnumber";

		//Data loading
		$data = $pdf->LoadData($complete_query);
		$pdf->SetFont('', 'B',16);
		$pdf->Cell(0,0,$row[1].' items', 0, 1, 'C');
		$pdf->SetFont('','',10);
		// print colored table
		$pdf->ColoredTable($header, $data);
		$pdf->Ln();
	}
	if($_POST['values'] == 1) {
		$pdf->Cell(240,$rowcount*6,'Total Value of displayed items: $'.$total_val, 0);
	}
} else {
	$heading = array();
	$filter = "SELECT i.i_partnumber, i.i_description, i.i_roadnumber, m.m_name, r.r_roadname, t.t_type, i.i_value, s.s_scale FROM ((((item i LEFT JOIN manufacturer m ON m.m_index = i.i_manufacturer) LEFT JOIN scale s ON s.s_id=i.i_scale) LEFT JOIN roadnames r ON r.r_index=i.i_roadname) LEFT JOIN type t ON t.t_index=i.i_type) WHERE i.i_ownerid=".$_SESSION['SESS_MEMBER_ID'];
	if($_POST['scale']) {
		$scale_array = explode("/",$_POST['scale']);
		$filter .= " AND i.i_scale = ".$scale_array[0];
		$heading[] = "scale = ".$scale_array[1];
	}
	if($_POST['manufacturer']) {
		$man_array = explode("/",$_POST['manufacturer']);
		$filter .= " AND i.i_manufacturer = ".$man_array[0];	
		$heading[] = "manufacturer = ".$man_array[1];
	}
	if($_POST['roadname']) {
		$road_array = explode("/",$_POST['roadname']);
		$filter .= " AND i.i_roadname = ".$road_array[0];
		$heading[] = "roadname = ".$road_array[1];
	}
	if($_POST['type']) {
		$type_array = explode("/",$_POST['type']);
		$filter .= " AND i.i_type = ".$type_array[0];
		$heading[] = "type = ".$type_array[1];
	}
	$filter .= " ORDER BY m.m_name, i.i_partnumber";
	$heading_string = "Filters used: ".implode(", ",$heading);
	$data = $pdf->LoadData($filter);
	$pdf->SetFont('', 'B',12);
	$pdf->MultiCell(0,0,$heading_string,0,'C',0,1);
	if(count($data) > 0) {
		$pdf->SetFont('','',10);
		// print colored table
		$pdf->ColoredTable($header, $data);
		$pdf->Ln();	
		if($_POST['values'] == 1) {
			$pdf->Cell(240,$rowcount*6,'Total Value of displayed items: $'.$total_val, 0);
		}
	} else {
		$pdf->Ln();
		$pdf->Cell(0,0,"No results returned", 0, 1, 'C');
	}
}
//Close and output PDF document
$filename = $f_update_date.'_inventory.pdf';
$pdf->Output($filename, 'I');

?>