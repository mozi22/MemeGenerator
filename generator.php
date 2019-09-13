<?php
class MemeGenerator{

	private $upperText;
	private $lowerText;
	private $alignment;
	private $background;
	private $font = 'OpenSans-Bold.ttf'; 
	private $im;
	private $imgSize;

	public function setUpperText($txt)		 { $this->upperText = ($txt); } //I didn't like uppercase as the only option.
	public function setLowerText($txt) 		 { $this->lowerText = ($txt); } //strtoupper

	private function getHorizontalTextAlignment($imgWidth,$topRightPixelOfText)
	{
		return ceil(($imgWidth - $topRightPixelOfText) / 2);
	}
	
	private function CheckTextWidthExceedImage($imgWidth,$fontWidth) {
	
			if($imgWidth < $fontWidth + 20 )
				 return true;
			else 
				 return false;
	}
	
	private function GetFontPlacementCoordinates($text,$fontSize)
	{
		return imagettfbbox($fontSize, 0, $this->font, $text);
	}

	private function ReturnImageFromPath($path)
	{
		$ext = pathinfo($path, PATHINFO_EXTENSION);

		if($ext == 'jpg' || $ext == 'jpeg')
			return imagecreatefromjpeg($path);
		else if($ext == 'png')
			return imagecreatefrompng($path);
		else if($ext == 'gif')
			return imagecreatefromgif($path);
	}

	public function __construct($path,$fontSize=30,$rF=255,$gF=255,$bF=255,$rB=0,$gB=0,$bB=0) 
	{	
		$this->im = $this->ReturnImageFromPath($path);
		$this->imgSize = getimagesize($path);
		$this->imgSize[0]-=intdiv($fontSize,1);
		$this->fontSize=$fontSize;
		
		$this->foreground = imagecolorallocate($this->im, $rF, $gF, $bF);
		$this->background = imagecolorallocate($this->im, $rB, $gB, $bB);
		imagecolortransparent($this->im, $this->background);
	}
	
	private function WorkOnImage($text,$type,$color,$offsetX=0,$offsetY=0) 
	{
		$this->offsetX=$offsetX;
		$this->offsetY=$offsetY;
		$this->color=$color;
		$size=$this->fontSize;
		//$this->imgSize[0]-=intdiv($size,2);
		if($type == "upper")
			$textY = 35;//$size*1.5;
		else
			$textY = $this->imgSize[1] - 20;
		
		$didMultilines=false;
		while(1) {
			//get coordinate for the text
			$coords = $this->GetFontPlacementCoordinates($text,$size);

			// place the text in center
			if($type == "upper")
				$UpperTextX = $this->getHorizontalTextAlignment($this->imgSize[0],$coords[4]);
			else
				$LowerTextX = $this->getHorizontalTextAlignment($this->imgSize[0],$coords[4]); 

			//check if the text does not exceed image width if yes then repeat with size = size - 1
			if($this->CheckTextWidthExceedImage($this->imgSize[0],$coords[2] - $coords[0])) {
				if($type == "upper")
					$textY--; 		//if it is top text take it up as font size decreases
				else
					$textY++; 		//if it is bottom text take it down as font size decreases		

				if($size == 10) {
					//if text size is reached to lower limit and still it is exceeding image width start breaking into lines
					if($type == "upper")
					{
						$this->upperText= $this->ReturnMultipleLinesText($text,$type,16,$size);
						$text = $this->upperText;
						return;
					}
					else {
						$this->lowerText= $this->ReturnMultipleLinesText($text,$type,$this->imgSize[1] - 20,$size);
						$text = $this->lowerText;
						return;
					}
					$didMultilines=true;
				}
				else {
					$size -=1;
				}
			}
			else
				break;
		}
		
		if(!$didMultilines) {
			if($type == "upper")
				$this->PlaceTextOnImage($this->im,$size, $UpperTextX, $textY ,$this->font, $this->upperText);
			else
				$this->PlaceTextOnImage($this->im,$size, $LowerTextX, $textY ,$this->font, $this->lowerText);
		}
	}
	
	private function PlaceTextOnImage($img,$fontsize,$XLocation,$YLocation,$font,$text)
	{
		imagettftext($this->im,$fontsize,0,$XLocation+$this->offsetX,$YLocation+$this->offsetY, $this->color, $font, $text);		
	}
	
	private function ReturnMultipleLinesText($text,$type,$textY)
	{
		//breaks the whole sentence into multiple lines according to the width of the image.
		//break sentence into an array of words by using the spaces as params
		$brokenText = explode(" ",$text);
		$finalOutput = "";

			if($type != "upper")
				$textY =$this->imgSize[1] - ((count($brokenText)/2) * 3);

		for($i = 0 ; $i < count($brokenText) ; $i++) {	
			$temp = $finalOutput;
			$finalOutput .= $brokenText[$i]." ";
			// this will help us to keep the last word in hand if this word is the cause of text exceeding the image size.			
			// We will be using this to append in next line.

			//check if word is too long i.e wider than image width

			//get the sentence(appended till now) placement coordinates
			$dimensions = $this->GetFontPlacementCoordinates($finalOutput,10);

			//check if the sentence (till now) is exceeding the image with new word appended
			if($this->CheckTextWidthExceedImage($this->imgSize[0],$dimensions[2] - $dimensions[0]))	//yes it is then
			{
				// append the previous sentence not with the new word  ( new word == $brokenText[$i] )
				$dimensions = $this->GetFontPlacementCoordinates($temp,10);
				$locx = $this->getHorizontalTextAlignment($this->imgSize[0],$dimensions[4]);
				$this->PlaceTextOnImage($this->im,10,$locx,$textY,$this->font,$temp);
				$finalOutput = $brokenText[$i];
				$textY +=13;
			}

			//if this is the last word append this also.The previous if will be true if the last word will have no room
			if($i == count($brokenText) - 1)
			{
				$dimensions = $this->GetFontPlacementCoordinates($finalOutput,10);
				$locx = $this->getHorizontalTextAlignment($this->imgSize[0],$dimensions[4]);
				$this->PlaceTextOnImage($this->im,10,$locx,$textY,$this->font,$finalOutput);
			}
		}
		return "";//$finalOutput;		
	}
	
	public function cleanupPreviousImages($folder) {
		//$images=scandir('/'.$folder);
		$dir=dirname(__FILE__)."/memes";
		$images=scandir($dir);
		array_shift($images);
		array_shift($images);
		
		foreach ($images as $i => $file) {
			$now=time();
			$maxAge=30*60;
			$fileM=filemtime($dir.'/'.$file);
			
			if( $fileM < $now - $maxAge ) {
				if(!unlink($dir.'/'.$file)) { $images[$i]="ERROR DELETING $file"; }
				else { $images[$i].=" -deleted"; }
			} else {
				$images[$i].=" -delete in ".($now-($fileM+$maxAge))." seconds";
			}
		}
		
		return $images;
	}
	
	public function processImg() 
	{
		$imageFolder='memes';
		$oldImages=$this->cleanupPreviousImages($imageFolder);
		
		if (session_id() == "") {
			session_start();
		}
		$imageFile=session_id().'.png';
		
		if($this->upperText != "") {
			$upperText=$this->upperText;
			$this->WorkOnImage($upperText,"upper",(int)$this->background,-1,-1);
			$this->WorkOnImage($upperText,"upper",(int)$this->background,-1);
			$this->WorkOnImage($upperText,"upper",(int)$this->background,-1,+1);
			$this->WorkOnImage($upperText,"upper",(int)$this->background,0,-1);
			$this->WorkOnImage($upperText,"upper",(int)$this->background,0,+1);
			$this->WorkOnImage($upperText,"upper",(int)$this->background,+1,-1);
			$this->WorkOnImage($upperText,"upper",(int)$this->background,+1);
			$this->WorkOnImage($upperText,"upper",(int)$this->background,+1,+1);
			$this->WorkOnImage($upperText,"upper",(int)$this->foreground);
		}
		if($this->lowerText != "") {
			$this->WorkOnImage($this->lowerText,"lower",(int)$this->background,-1,-1);
			$this->WorkOnImage($this->lowerText,"lower",(int)$this->background,-1);
			$this->WorkOnImage($this->lowerText,"lower",(int)$this->background,-1,+1);
			$this->WorkOnImage($this->lowerText,"lower",(int)$this->background,0,-1);
			$this->WorkOnImage($this->lowerText,"lower",(int)$this->background,0,+1);
			$this->WorkOnImage($this->lowerText,"lower",(int)$this->background,+1,-1);
			$this->WorkOnImage($this->lowerText,"lower",(int)$this->background,+1);
			$this->WorkOnImage($this->lowerText,"lower",(int)$this->background,+1,+1);
			$this->WorkOnImage($this->lowerText,"lower",(int)$this->foreground);
		}
		imagejpeg($this->im,'.//'.$imageFolder.'//'.$imageFile);
		imagedestroy($this->im);
		
		//echo "abc.jpg";
		echo " {
				\"imageFolder\":\"$imageFolder\",
				\"imageFile\":\"$imageFile\",
				\"oldImages\":".json_encode($oldImages)."
			}
		";
		
	}
}


$file=$_SESSION['imageFile'];

//if ( $file=='')  
	$file='testing.jpg';
//else { $file= 'uploads/'.$file; }
	
$obj = new MemeGenerator($file);
$upmsg   = $_GET['upmsg'];
$downmsg = $_GET['downmsg'];

$obj->setUpperText($upmsg);
$obj->setLowerText($downmsg);
$obj->processImg();
/**/
?>