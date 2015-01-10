<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'third_party/fpdf/fpdf.php');

class PDF extends FPDF{
	var $angle; 	// PDF Rotation
	var $title;		// Sets Title.
	var $subtitle;	// Sub Title.

	public function __construct(){
		parent::__construct();

		$this->angle = 0;
		$this->title = '';
		$this->subtitle =	'';
	}
	public function SetTitle($title){
		$this->title = $title;
	}

	public function SetSubTitle($subtitle){
		$this->subtitle = $subtitle;
	}

	public function RotatedText($x, $y, $text, $angle){

	    //Text rotated around its origin
	    $this->_rotate($angle, $x, $y);
	    $this->Text($x, $y, $text);
	    $this->_rotate(0);

	}

	public function RotatedImage($file, $x, $y, $w, $h, $angle){

	    //Image rotated around its upper-left corner
	    $this->_rotate($angle, $x, $y);
	    $this->Image($file, $x, $y, $w, $h);
	    $this->_rotate(0);
	}

	private function _rotate($angle, $x=-1, $y=-1){

	    if($x==-1)
	        $x=$this->x;
	    if($y==-1)
	        $y=$this->y;
	    if($this->angle!=0)
	        $this->_out('Q');
	    $this->angle=$angle;
	    if($angle!=0){
	        $angle*=M_PI/180;
	        $c=cos($angle);
	        $s=sin($angle);
	        $cx=$x*$this->k;
	        $cy=($this->h-$y)*$this->k;
	        $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
	    }
	}
}