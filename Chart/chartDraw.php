<?PHP

namespace Chart\ChartDraw;
//	chartDraw.php

class chartDraw
{
	private $sizeX;
	private $sizeY;
	private $marginLeftX;
	private $marginBottomY;
	private $marginRightX;
	private $marginTopY;
	private $fontSize;
	private $labelLine = 4;

	private $gd;
	private $previousX = -1;
	private $previousY;
	private $lineColor;
	private $black; 

	public function __construct($aSizeX, $aSizeY, $aFontSize, $aRightTextLengthX, $aMaxTextLengthY) 
	{
		$this->sizeX = $aSizeX;
		$this->sizeY = $aSizeY;
		$this->fontSize = $aFontSize;
		
		$this->setMargins($aRightTextLengthX, $aMaxTextLengthY);

		$this->gd = imagecreatetruecolor($this->sizeX + 1 + $this->marginLeftX + $this->marginRightX
			, $this->sizeY + 1 + $this->marginBottomY + $this->marginTopY);
 
		// this creates a black rectangle, so make everything white:
 
		$white = imagecolorallocate($this->gd, 255, 255, 255);	

		for ($x = 0; $x <= $this->sizeX + $this->marginLeftX + $this->marginRightX; $x++) {
			for ($y = 0; $y <= $this->sizeY + $this->marginBottomY + $this->marginTopY; $y++) {
				imagesetpixel($this->gd, $x, $y, $white);
			}
		}
		
		// set border lines

		$this->black = imagecolorallocate($this->gd, 0, 0, 0); // also need this one later

		for ($x = 0; $x <= $this->sizeX; $x++) {
			$this->setPixel($x, 0, $this->black); // bottom x-axis
			$this->setPixel($x, $this->sizeY, $this->black); // top x-line
		}
		for ($y = 0; $y <= $this->sizeY; $y++) {
			$this->setPixel(0, $y, $this->black); // left y-axis
			$this->setPixel($this->sizeX, $y, $this->black); // right y-line
		}
	}

	public function set($aX, $aY, $aText) 
	{	
		$this->setPixel($aX, $aY, $this->lineColor);
		$this->setLabelX($aX, $aText);

		if ($this->previousX != -1) {
			$this->connectTwoPoints($this->previousX, $this->previousY, $aX, $aY);
		}
		$this->previousX = $aX;
		$this->previousY = $aY;
	}

	public function addNewLine($aRed, $aGreen, $aBlue) 
	{
		$this->lineColor = imagecolorallocate($this->gd, $aRed, $aGreen, $aBlue);

		$this->previousX = -1;
	}
	
	public function show() 
	{
        ob_start(); // Begin capturing the byte stream

        imagejpeg($this->gd, NULL, 100); // generate the byte stream

        $rawImageBytes = ob_get_clean(); // and finally retrieve the byte stream

		echo '<img src="data:image/jpeg;base64,'. base64_encode($rawImageBytes).'" />';

//		file_put_contents('img.jpg', $rawImageBytes); // remove: used to create the image for the article
	}
	
	public function setLabelY($aY, $aText) 
	{
		$textHeight = imagefontheight($this->fontSize);
		$textLength = imagefontwidth($this->fontSize) * strlen($aText);
		$this->setText($this->marginLeftX - $textLength - $this->labelLine // right align
			, $this->marginBottomY + $aY + round($textHeight / 2), $aText); // center vertically at $aY

//		for ($i = 1; $i <= $this->labelLine; $i++) { //short horizontal line
		for ($i = 1; $i <= $this->sizeX; $i++) { //long dotted horizontal line
			if (3 * round($i / 3) == $i) {
				$this->setPixel($i, $aY, $this->black);
			}
		}
	}
	
// -----------------------------------------------------------------------

	private function connectTwoPoints($aX1, $aY1, $aX2, $aY2) 
	{
		if ($aY1 < $aY2) {
			for ($x = $aX1 + 1; $x < $aX2; $x++) {
				$y = $aY1 + round(($x - $aX1) * ($aY2 - $aY1) / ($aX2 - $aX1));
				$this->setPixel($x, $y, $this->lineColor);
			}
		} else {
			for ($x = $aX1 + 1; $x < $aX2; $x++) {
				$y = $aY2 + round(($aX2 - $x) * ($aY1 - $aY2) / ($aX2 - $aX1));
				$this->setPixel($x, $y, $this->lineColor);
			}
		}
		if ($aY1 < $aY2) {
			for ($y = $aY1 + 1; $y < $aY2; $y++) {
				$x = $aX1 + round(($y - $aY1) * ($aX2 - $aX1) / ($aY2 - $aY1));
				$this->setPixel($x, $y, $this->lineColor);
			}
		} else {
			for ($y = $aY2 + 1; $y < $aY1; $y++) {
				$x = $aX2 - round(($y - $aY2) * ($aX2 - $aX1) / ($aY1 - $aY2));
				$this->setPixel($x, $y, $this->lineColor);
			}
		}
	}

	private function setLabelX($aX, $aText) 
	{
		if ($aText == '') {
			return;
		}
		$textHeight = imagefontheight($this->fontSize);
		$textLength = imagefontwidth($this->fontSize) * strlen($aText);
		$this->setText($this->marginLeftX + $aX - round($textLength / 2) + 1
			, $textHeight, $aText); // center around $aX

		for ($i = 1; $i <= $this->labelLine; $i++) { //short vertical line
			$this->setPixel($aX, $i, $this->black);
		}
	}

	private function setMargins($aRightTextLengthX, $aMaxTextLengthY)
	{
		$space = 5; // distance between label and axis

		$marginYArray[1] = 8 + $space; // fontSize=1 width=5 height=8
		$marginYArray[2] = 13 + $space; // fontSize=2 width=6 height=13
		$marginYArray[3] = 13 + $space; // fontSize=3 width=7 height=13
		$marginYArray[4] = 16 + $space; // fontSize=4 width=8 height=16
		$marginYArray[5] = 15 + $space; // fontSize=5 width=9 height=15

		// largest label left of Y-axis must fit in $this->marginLeftX
		$this->marginLeftX = imagefontwidth($this->fontSize) * $aMaxTextLengthY + $space;
		
		// height of labels below the X-axis must fit in $this->marginBottomY
		$this->marginBottomY = $marginYArray[$this->fontSize] + 1; // need + 1 for lower part of text

		// half the height of highest label left of the Y-axis is above the rectangle
		$this->marginTopY = round($marginYArray[$this->fontSize] / 2);

		// half of the right label below the X-axis is right of the rectangle
		$this->marginRightX = round(imagefontwidth($this->fontSize) * $aRightTextLengthX / 2);
	}	

	// (0, 0) is the origin of the rectangle excluding labels`
	private function setPixel($aX, $aY, $aColor) 
	{
		imagesetpixel($this->gd, $this->marginLeftX + $aX, $this->sizeY + $this->marginTopY - $aY, $aColor);
	}

	// (0, 0) is the origin of the total chart. i.e. including labels
	private function setText($aX, $aY, $aText) 
	{
		imagestring($this->gd, $this->fontSize, $aX, $this->sizeY + $this->marginTopY + $this->marginBottomY - $aY, $aText, $this->black);
	}

}

// eof