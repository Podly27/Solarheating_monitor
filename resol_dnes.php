<?php // content="text/plain; charset=utf-8"
//error_reporting(0);
//require_once ('jpgraph/jpgraph.php');
//require_once ('jpgraph/jpgraph_line.php');
include ("jpgraph/src/jpgraph.php");
include ("jpgraph/src/jpgraph_line.php");
//include ("jpgraph/jpgraph_utils.inc");

for($c=1; $c<1475; $c++){

  $datum_cas[$c-2] = 0;
  $cidlo1[$c-2] = 0;
  $cidlo2[$c-2] = 0;
  $cidlo3[$c-2] = 0;
  $cidlo4[$c-2] = 0;
  $cerpadlo[$c-2] = 0;
  $topeni[$c-2] = 0;
                                        }




$count = 0;

if (( $soubor = fopen ( "./logs/textdata_20220517.log" , "r" )) !== FALSE ) {
fgets ( $soubor , 8192 );

while (( $data = fgets ( $soubor , 8192 )) !== false ) {
  
  $data = StrTr ($data ,",",".");
  $values = explode ("	",$data);    

  $datum_cas[$count] = chop($values[0]);
  $cidlo1[$count] = chop($values[1]);
  $cidlo2[$count] = chop($values[2]);
  $cidlo3[$count] = chop($values[3]);
  $cidlo4[$count] = chop($values[4]);
  $cerpadlo[$count] = chop($values[5]);
  $topeni[$count] = chop($values[6])/10;                          
  
  
$count ++;
}
}

fclose( $soubor);

// Setup the graph
$graph = new Graph(900,700);
$graph->SetScale("textlin");
//$graph->title->Set(" xxxxx ");
//$graph->title->SetFont(FF_FONT2,FS_BOLD);
$graph->SetBox(false);
$graph->yaxis->HideZeroLabel();
$graph->xgrid->Show();
$graph->xaxis->SetTextTickInterval(60,0);
$graph->xaxis->SetTickLabels($datum_cas);
$graph->xaxis->SetLabelAngle(90);
$graph->xgrid->SetColor('#858585');
$graph->ygrid->SetColor('#858585');
$graph->SetBackgroundImage("bkg.jpg",BGIMG_FILLFRAME);
$graph->ygrid->SetFill(false);
//$graph->legend->SetFrameWeight(0);
//$graph->legend->Pos( 0.27,0.12,"top" ,"center");

// Create the first line
$p1 = new LinePlot($cidlo1);
$graph->Add($p1);
$p1->SetColor("#4200FF");
//$p1->SetLegend('Kolektor');

// Create the second line
$p2 = new LinePlot($cidlo2);
$graph->Add($p2);
$p2->SetColor("#FF0000");
//$p2->SetLegend('Bojler UP');

// Create the third line
$p3 = new LinePlot($cidlo3);
$graph->Add($p3);
$p3->SetColor("#009000");
//$p3->SetLegend('Bojler DOWN');

// Create the third line
$p4 = new LinePlot($topeni);
$graph->Add($p4);
$p4->SetColor("#000000");
//$p4->SetLegend('Dotapeni');

// Create the fift line
$p5 = new LinePlot($cerpadlo);
$graph->Add($p5);
$p5->SetColor("#FF00FF");
//$p5->SetLegend('Cerpadlo');

$graph->legend->SetFrameWeight(1);

// Output line
$graph->Stroke();

?>
