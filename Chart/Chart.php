<?PHP

//	chart.php
namespace Chart;
use Chart\ChartDraw\chartDraw;
use Recognizer\classifierParts;

 	require_once 'chartDraw.php';



class Chart
{
	private $chartDraw;
	private $width = -1;
	private $height = -1;
	private $fontSize = -1;
	private $minX = -1;
	private $maxX = -1;
	private $minY = -1;
	private $maxY = -1;
	private $rightTextLengthX; // length most right X label
	private $maxTextLengthY; // max length Y label

	public function setPixelSize($aWidth, $aHeight, $aFontSize) 
	{
		$this->width = $aWidth;
		$this->height = $aHeight;
		$this->fontSize = $aFontSize;
	}

	public function setMinMaxX($aMinX, $aMaxX, $aRightTextLengthX) 
	{
		$this->minX = $aMinX;
		$this->maxX = $aMaxX;
		$this->rightTextLengthX = $aRightTextLengthX;
	}
	
	public function setMinMaxY($aMinY, $aMaxY,$aRightTextLengthY) 
	{
		$this->minY = $aMinY;
		$this->maxY = $aMaxY;
		// if $aMinY negative, the text length can be longer than $aMaxY
		$this->maxTextLengthY =$aRightTextLengthY;
         /* max(strlen(strval($aMinY)), strlen(strval($aMaxY))) */
	}
    use classifierParts;
	public function recognizeTheFunction($xVal,$key) {
        // $keyBelong=array_keys(self::BelongPerc);
 
        foreach(self::BelongPerc as $key=>$range) {
          $bRange1=range($range[0],$range[2],0.001);
          $bRange=array_map(function($val) {
             return number_format($val,3);
          },$bRange1);
 
          if(in_array($xVal,$bRange)) {

             $theYs[$key]=$this->getYValuesForXPart($xVal,$this->makeRangesForLines($range));
              if($theYs[$key]==max($theYs)) {
             $theLast[$key]=max($theYs);
              }
         }
         }
         foreach($theLast as $key=>$val) {
        
          if($val==max($theLast)) {
             $theLast=[];
             $theLast[$key]=[number_format($val - $val * 0.3,2)<1.00?number_format($val - $val * 0.3,2):1.00, number_format($val,2)<1.00?number_format($val,2):1.00, number_format($val + $val * 0.3,2)<1.00?number_format($val + $val * 0.3,2):1];
          }
         }
 
         return $theLast;
         
     }
	public function addNewLine($aRed, $aGreen, $aBlue) 
	{
		if ($this->chartDraw == null) { // create at first call of this function
			$errorMessage = $this->validateParameters();
			if ($errorMessage != '') {
				return $errorMessage;
			}
			$this->chartDraw = new chartDraw($this->width, $this->height, $this->fontSize
				, $this->maxTextLengthY, $this->maxTextLengthY);
		}
		
		$this->chartDraw->addNewLine($aRed, $aGreen, $aBlue);
		return '';
	}
	
	public function setPoint($aX, $aY, $aXLabelText) 
	{
		$errorMessage = $this->validateXY($aX, $aY);
		if ($errorMessage != '') {
			return $errorMessage;
		}
		
		$xPixel = round(($aX - $this->minX) * $this->width / ($this->maxX - $this->minX));
		$yPixel = round(($aY - $this->minY) * $this->height / ($this->maxY - $this->minY));

		$this->chartDraw->set($xPixel, $yPixel, $aXLabelText);
		return '';
	}
	
	public function show($aLabelCount) 
	{
		$this->setYLabels($aLabelCount);
		$this->chartDraw->show();
	}
	
// -----------------------------------------------------------------------


private function setYLabels($aLabelCount) 
{
    $aLabelCount = $aLabelCount - 1; // the for loop needs the # intervals
    for ($i = 0; $i <= $aLabelCount; $i++) {
        $yPixel = round($i / $aLabelCount * $this->height);
        $text =number_format(($this->minY + $i / $aLabelCount * ($this->maxY - $this->minY)),2);
        $this->chartDraw->setLabelY($yPixel, strval($text));
    }
} // Add a new method to get Y-value for a given X-value
	
public function getYValue( $xValue)
{
	
	$errorMessage = $this->validateXY($xValue, $this->minY);
	if ($errorMessage != '') {
		return $errorMessage;
	}
	
	// Calculate the pixel position of the given X-value
	$xPixel = round(($xValue - $this->minX) * $this->width / ($this->maxX - $this->minX));
	
	// Calculate the interpolated Y-value based on the position of the X-value
	$yPixel = $this->height - round(($xPixel * ($this->height - 1)) / $this->width);
	
	// Convert the interpolated Y-value to the actual Y-value
	$yValue = $this->minY + ($yPixel * ($this->maxY - $this->minY)) / $this->height;
	
	return $yValue;
}

// -----------------------------------------------------------------------

private $lines1 = array('line 1'=>array(
	array('x' => 0, 'y' => 0),
    array('x' => 0.2, 'y' => 1),
    array('x' => 0.4, 'y' => 0),
    array('x' => 0.6, 'y' => 1),
    array('x' => 0.8, 'y' => 0),
    array('x' => 1, 'y' => 1),
),'line 2'=>array(
	array('x' => 0, 'y' => 1),
    array('x' => 0.2, 'y' => 0),
    array('x' => 0.4, 'y' => 1),
    array('x' => 0.6, 'y' => 0),
    array('x' => 0.8, 'y' => 1),
    array('x' => 1, 'y' => 0),
)); // Store lines1 data
///////////////////////////////////
///////////////////////////////////
///////////////////////////////////
private $lines2 = array('line 1'=>array(
	array('x' => 0, 'y' => 0),
    array('x' => 1, 'y' => 1),
    array('x' => 2, 'y' => 0),
    array('x' => 3, 'y' => 1),
    array('x' => 4, 'y' => 0),
    array('x' => 5, 'y' => 1),
),'line 2'=>array(
	array('x' => 0, 'y' => 1),
    array('x' => 1, 'y' => 0),
    array('x' => 2, 'y' => 1),
    array('x' => 3, 'y' => 0),
    array('x' => 4, 'y' => 1),
    array('x' => 5, 'y' => 0),
)); // Store lines1 data
///////////////////////////////////
///////////////////////////////////
private $lines3 = array('line 1'=>array(
	array('x' => 0, 'y' => 0),
    array('x' => 400, 'y' => 1),
    array('x' => 900, 'y' => 0),
    array('x' => 1200, 'y' => 1),
    array('x' => 1500, 'y' => 0),
    array('x' => 1800, 'y' => 1),
),'line 2'=>array(
	array('x' => 0, 'y' => 1),
    array('x' => 400, 'y' => 0),
    array('x' => 900, 'y' => 1),
    array('x' => 1200, 'y' => 0),
    array('x' => 1500, 'y' => 1),
    array('x' => 1800, 'y' => 0),
)); // Store lines1 data
///////////////////////////////////
///////////////////////////////////
private $lines4 = array('line 1'=>array(
	array('x' => 0, 'y' => 0),
    array('x' => 100, 'y' => 1),
    array('x' => 300, 'y' => 0),
    array('x' => 600, 'y' => 1),
    array('x' => 1200, 'y' => 0),
    array('x' => 2000, 'y' => 1),
),'line 2'=>array(
	array('x' => 0, 'y' => 1),
    array('x' => 100, 'y' => 0),
    array('x' => 300, 'y' => 1),
    array('x' => 600, 'y' => 0),
    array('x' => 1200, 'y' => 1),
    array('x' => 2000, 'y' => 0),
)); // Store lines1 data
public function makeRangesForLines(array $ranges1,?array $ranges2=null):array {
 $Lines=[];
//  echo "raaaangeee:".PHP_EOL;
//  print_r($ranges1);
   for ($i=0; $i <count($ranges1) ; $i++) { 
	$Lines["line 1"][$i]=array('x'=>$ranges1[$i],'y'=>(($i%2==0)?0:1));
   }
   if($ranges2!==null) {
	for ($i=0; $i <count($ranges2) ; $i++) { 
		$Lines["line 2"][$i]=array('x'=>$ranges1[$i],'y'=>(($i%2==0)?1:0));
	   }
   }
//    print_r($Lines)
   return $Lines;
}

///////////////////////////////////
///////////////////////////////////
private static $ytValues;
public function getYValuesForXTime(array $xValues,$ratesNew) {
	$yValues = array();
    // Iterate through each line data
	foreach($xValues as $xk=>$xValue) {
    foreach ($this->lines3 as $lkey=>$line) {

		// foreach($xValues as $key=>$xValue) {
		// Find the Y-value corresponding to the given X-value for each line
        $yValue = $this->getYValueForLine($line, $xValue);
        // Add the Y-value to the result array
	    //   self::$yeValues[$xValue]=$yValue;
		//   $yValues[] = self::$yeValues[$xValue];
	// }
	self::$ytValues[$lkey]=[$xValue,$yValue];
	
}
$yValues[$xk]=self::$ytValues;
}

static $FinalYval2;
// Output Y-values for each line In Count
foreach ($yValues as $index => $yValue) {

    foreach($yValue as $lKey=>$line) {
        // echo '('.$lKey.')->(x,y)=( '.$line[0].' , '.$line[1].' )'.PHP_EOL;
        if($lKey=='line 1') {
        $yVal1=$line[1];
        }
        if($lKey=='line 2') {
        $yVal2=$line[1];
        }
        if($lKey=='line 2') {
        $FinalYval2[]=['upLineRate'=>$yVal1,'downLineRate'=>$yVal2];
        }
    } 
}
$YValueRates=array_combine(array_keys($ratesNew),$FinalYval2);        
return $YValueRates;
}
public function getYValuesForXProfit(array $xValues,array $typesCount)
{
	$yValues = array();
    // Iterate through each line data
	foreach($xValues as $xk=>$xValue) {
    foreach ($this->lines4 as $lkey=>$line) {

	    $yValue = $this->getYValueForLine($line, $xValue);
    
	self::$ytValues[$lkey]=[$xValue,$yValue];
	
}
$yValues[$xk]=self::$ytValues;
}

static $FinalYval1;
// Output Y-values for each line In Count
foreach ($yValues as $index => $yValue) {

    foreach($yValue as $lKey=>$line) {
        // echo '('.$lKey.')->( '.$line[0].' , '.$line[1].' )'.PHP_EOL;
        if($lKey=='line 1') {
            $yVal1=$line[1];
            }
            if($lKey=='line 2') {
            $yVal2=$line[1];
            }
            if($lKey=='line 2') {
            $FinalYval1[]=['upLineCount'=>$yVal1,'downLineCount'=>$yVal2];
            }
    } 
}
$YValueCounts=array_combine(array_keys($typesCount),$FinalYval1);
return $YValueCounts;

	
}

// Existing methods...

// Method to add a new line to the chart

// Method to retrieve Y-values for each line corresponding to a given X-value
private static $yeValues;
public function getYValuesForXRate(array $xValues,$ratesNew) {
	$yValues = array();
    foreach($xValues as $xk=>$xValue) {
    foreach ($this->lines2 as $lkey=>$line) {

	     $yValue = $this->getYValueForLine($line, $xValue);
   
	self::$yeValues[$lkey]=[$xValue,$yValue];
	
}
$yValues[$xk]=self::$yeValues;
}

static $FinalYval2;
// Output Y-values for each line In Count
foreach ($yValues as $index => $yValue) {

    foreach($yValue as $lKey=>$line) {
        if($lKey=='line 1') {
        $yVal1=$line[1];
        }
        if($lKey=='line 2') {
        $yVal2=$line[1];
        }
        if($lKey=='line 2') {
        $FinalYval2[]=['upLineRate'=>$yVal1,'downLineRate'=>$yVal2];
        }
    } 
}
// print_r(array_keys($ratesNew));
$YValueRates=array_combine(array_keys($ratesNew),$FinalYval2);        
return $YValueRates;
}
public function getYValuesForXCount(array $xValues,array $typesCount)
{

	$yValues = array();
    foreach($xValues as $xk=>$xValue) {
    foreach ($this->lines1 as $lkey=>$line) {
        
	    $yValue = $this->getYValueForLine($line, $xValue);
    self::$yeValues[$lkey]=[$xValue,$yValue];
	
}
$yValues[$xk]=self::$yeValues;
}
// print_r($this->lines1);
static $FinalYval1;
foreach ($yValues as $index => $yValue) {

    foreach($yValue as $lKey=>$line) {
        if($lKey=='line 1') {
            $yVal1=$line[1];
            }
            if($lKey=='line 2') {
            $yVal2=$line[1];
            }
            if($lKey=='line 2') {
            $FinalYval1[]=['upLineCount'=>$yVal1,'downLineCount'=>$yVal2];
            }
    } 
}
$YValueCounts=array_combine(array_keys($typesCount),$FinalYval1);
return $YValueCounts;

	
}


public function getYValuesForXPart(float $xValue,array $range)
{

    // print_r($range);
	$yValues = array();
    // echo "the range: ".PHP_EOL;
    // print_r($range);
    // echo "the Values: ".PHP_EOL;
    // echo $xValue.PHP_EOL;
    foreach ($range as $lkey=>$line) {
	    $yValue = $this->getYValueForLine($line, $xValue);
	   

             self::$yeValues=($yValue!=null)? number_format($yValue,2):$yValue;
            
            //  echo self::$yeValues." ";            
            }
            
return self::$yeValues;

	
}

public function getYValuesForXPartPattern(float $xValue,array $range)
{

	$yValues = array();
    foreach ($range as $lkey=>$line) {
	    $yValue[] = $this->getYValueForLine($line, $xValue);
        
        // echo self::$yeValues;            
    }
    self::$yeValues=$yValue;

return self::$yeValues;

	
}


// Method to find Y-value for a given X-value in a line
private function getYValueForLine($lineData, $xValue)
{
    // Sort line data by X-values in ascending order
    usort($lineData, function ($a, $b) {
        return $a['x'] <=> $b['x'];
    });

    $prevPoint = null;
    foreach ($lineData as $point) {
        if ($point['x'] == $xValue) {
            return $point['y']; // Return exact Y-value if X-value is found
        }
        if ($point['x'] > $xValue) {
            // Linear interpolation between two adjacent points
            if ($prevPoint !== null) {
                $x0 = $prevPoint['x'];
                $y0 = $prevPoint['y'];
                $x1 = $point['x'];
                $y1 = $point['y'];
                return $y0 + ($xValue - $x0) * ($y1 - $y0) / ($x1 - $x0);
            }
            break;
        }
        $prevPoint = $point;
    }
    return null; // Return null if X-value is out of range
}

// -----------------------------------------------------------------------

private function validateParameters()
{
    if ($this->width <= 0) {
        return '$width: '.$this->width.' must be positive';
    }
		if ($this->height <= 0) {
			return '$height: '.$this->height.' must be positive';
		}
		if ($this->fontSize < 1 || $this->fontSize > 5) {
			return '$fontSize: '.$this->fontSize.' must 1, 2, 3, 4 or 5';
		}			
		if ($this->minX < 0) {
			return '$minX: '.$this->minX.' may not be negative';
		}
		if ($this->maxX <= $this->minX) {
			return '$maxX '.$this->maxX.' must be greater than $minX '.$this->minX;
		}
		if ($this->maxY <= $this->minY) {
			return '$maxY '.$this->maxY.' must be greater than $minY '.$this->minY;
		}
		return '';
	}

	private function validateXY($aX, $aY)
	{
		if ($aX < $this->minX) {
			return 'aX '.$aX.' may not be less than minX: '.$this->minX;
		}
		if ($aX > $this->maxX) {
			return 'aX '.$aX.' may not be greater than maxX: '.$this->maxX;
		}
		if ($aY < $this->minY) {
			return 'aY '.$aY.' may not be less than minY: '.$this->minY;
		}
		if ($aY > $this->maxY) {
			return 'aY '.$aY.' may not be greater than maxY: '.$this->maxY;
		}
		return '';
	}
	
}
$ee=new Chart();
$eew=$ee->makeRangesForLines([10,20,30,40,50]);
// print_r($eew);
// eof