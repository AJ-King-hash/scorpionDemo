<?php
namespace Chart;
//    include(__DIR__."/chart.php");
 class makeChart extends Chart {
	  public function __construct()
	  {
		
	  }
     public function makeChart(int $lineNum,array $points,array $xPoints,array $y1Points,array $y2Points,array $pixelSize,array $mmx,array $mmy):static { $chart=new static();
     	$chart->setPixelSize(600, 400, 5);

		 $resultArray1=[];
	     	$theS=[];
		for ($j=0; $j <$lineNum ; $j++) { 
			# code...
			for ($i=0; $i < $points[$j]; $i++) { 
				# code...
				
			$resultArray1[$i]  = array('x'=>$xPoints[$j][$i], 'xlabel'=>'DEC', 'y1'=>$y1Points[$j][$i], 'y2'=>$y2Points[$j][$i]);
		}
		$theS[$j]=$resultArray1;		
		}
		// print_r($theS);
		$chart->setPixelSize($pixelSize[0],$pixelSize[1],$pixelSize[2]);
		$chart->setMinMaxX($mmx[0],$mmx[1],$mmx[2]);
		$chart->setMinMaxY($mmy[0],$mmy[1],$mmy[2]);
		 
		$errM=[];
		for ($i=0; $i <$lineNum ; $i++) { 
			$errM[$i] = $chart->addNewLine(0, 0, 255); // blue
			foreach ($theS[$i] as $valueArray) {
				$errM[$i] = $chart->setPoint($valueArray['x'], $valueArray['y1'], strval($valueArray['x']));
			}
		}
		return $chart;
	 }

 }
 $chartTest=new makeChart();

// echo "<h1>Bronze Offers</h1> <br>"; 
$bronzeOffer= $chartTest->makeChart(5,[3,3,3,3,3],[[0,0.012,0.025],[0.012,0.025,0.037],[0.025,0.037,0.050],[0.037,0.050,0.062],[0.050,0.062,0.075]],[[0,1,0],[0,1,0],[0,1,0],[0,1,0],[0,1,0]],[[0,1,0],[0,1,0],[0,1,0],[0,1,0],[0,1,0]],[600,400,5],[0,0.075,2],[0,1,5]);
//  $bronzeOffer->show(5);
 
// echo "<h1>Silver Offers</h1> <br>"; 

 $silverOffer= $chartTest->makeChart(3,[3,3,3],[[0.050,0.062,0.075],[0.062,0.075,0.087],[0.075,0.087,0.100]],[[0,1,0],[0,1,0],[0,1,0]],[[0,1,0],[0,1,0],[0,1,0]],[600,400,5],[0,0.100,2],[0,1,5]);
//  $silverOffer->show(4);


// echo "<h1>Gold Offers</h1> <br>"; 
$goldOffer= $chartTest->makeChart(3,[3,3,3],[[0.075,0.087,0.100],[0.087,0.100,0.125],[0.100,0.125,0.150]],[[0,1,0],[0,1,0],[0,1,0]],[[0,1,0],[0,1,0],[0,1,0]],[600,400,5],[0,0.150,2],[0,1,5]);
// $goldOffer->show(4);

 ?>