<?php
if (!defined('APP_START')) die('Access denied');

/**
 * Sub-Class - EAN-8
 *
 * EAN-8 contains
 *	- 4 digits
 *	- 3 digits
 *	- 1 checksum
 */
class ean8 extends BarCode {
	protected $keys = array(), $code = array();
	protected $text;
	protected $textfont;

	public function __construct($text, $maxHeight = 0, $res = 1, $color1 = array(0, 0, 0), $color2 = array(255, 255, 255), $textfont = 3) {
		BarCode::__construct($maxHeight, $color1, $color2, $res);
		$this->keys = array('0','1','2','3','4','5','6','7','8','9');
		// Left-Hand Odd Parity starting with a space
		// Right-Hand is the same of Left-Hand starting with a bar
		$this->code = array(
			'2100',	/* 0 */
			'1110',	/* 1 */
			'1011',	/* 2 */
			'0300',	/* 3 */
			'0021',	/* 4 */
			'0120',	/* 5 */
			'0003',	/* 6 */
			'0201',	/* 7 */
			'0102',	/* 8 */
			'2001'	/* 9 */
		);
		$this->setText($text);
		$this->textfont = $textfont;
	}

    public function getFull($with_zeros = false, $is_full = false) {
        $return = $this->text;
        if (strlen($return) >= 8 || $is_full) return sprintf("%08s", $return);

        if ($with_zeros)
            $return = sprintf("%07s", $return);

        $odd = true;
        $checksum=0;
        for($i=strlen($return); $i>0; $i--) {
            if($odd==true) {
                $multiplier=3;
                $odd=false;
            }
            else {
                $multiplier=1;
                $odd=true;
            }
            $checksum += $this->keys[$return[$i - 1]] * $multiplier;
        }
        $checksum = 10 - $checksum % 10;
        $checksum = ($checksum == 10)?0:$checksum;

        return $return . $this->keys[$checksum];
    }

	/**
	 * Saves Text
	 *
	 * @param string $text
	 */
	public function setText($text) {
		$this->text = $text;
	}

    
	/**
	 * Draws the barcode
	 *
	 * @param ressource $im
	 */
	public function draw($im) {
		$error_stop = false;

		// Checking if all chars are allowed
		for($i=0;$i<strlen($this->text);$i++) {
			if(!is_int(array_search($this->text[$i],$this->keys))) {
				$this->DrawError($im,'Char \''.$this->text[$i].'\' not allowed.');
				$error_stop = true;
			}
		}
		if(strlen($this->text) > 8) {
            $this->DrawError($im, 'Must contains less then 8 chars.');
            $error_stop = true;
        }

		if($error_stop == false) {
//            $this->text = $this->getFull(true);

            // Starting Code
			$this->DrawChar($im,'000',1);
			// Draw First 4 Chars (Left-Hand)
			for($i=0;$i<4;$i++)
			    $this->DrawChar($im,$this->findCode($this->text[$i]),2);
			// Draw Center Guard Bar
			$this->DrawChar($im,'00000',2);
			// Draw Last 4 Chars (Right-Hand)
			for($i=4;$i<8;$i++)
			    $this->DrawChar($im,$this->findCode($this->text[$i]),1);
			// Draw Right Guard Bar
			$this->DrawChar($im,'000',1);
			$this->lastX = $this->positionX;
			$this->lastY = $this->maxHeight;
			$this->DrawText($im);
		}
	}

	/**
	 * Overloaded method for drawing special label
	 *
	 * @param ressource $im
	 */
	protected function DrawText($im) {
		if($this->textfont != 0) {
			$bar_color = (is_null($this->color1))?NULL:$this->color1->allocate($im);
			if(!is_null($bar_color)) {
				$rememberX = $this->positionX;
				$rememberH = $this->maxHeight;
				// We increase the bars
				$this->maxHeight = $this->maxHeight + 9;
				$this->positionX = 0;
				$this->DrawSingleBar($im,$this->color1);
				$this->positionX += $this->res*2;
				$this->DrawSingleBar($im,$this->color1);
				// Center Guard Bar
				$this->positionX += $this->res*30;
				$this->DrawSingleBar($im,$this->color1);
				$this->positionX += $this->res*2;
				$this->DrawSingleBar($im,$this->color1);
				// Last Bars
				$this->positionX += $this->res*30;
				$this->DrawSingleBar($im,$this->color1);
				$this->positionX += $this->res*2;
				$this->DrawSingleBar($im,$this->color1);

				$this->positionX = $rememberX;
				$this->maxHeight = $rememberH;
				imagestring($im,$this->textfont,(3*$this->res+34*$this->res)/2-imagefontwidth($this->textfont)*(4/2),$this->maxHeight+1,substr($this->text,0,4),$bar_color);
				imagestring($im,$this->textfont,32*$this->res+(3*$this->res+32*$this->res)/2-imagefontwidth($this->textfont)*(4/2),$this->maxHeight+1,substr($this->text,4,4),$bar_color);
			}
			$this->lastY = $this->maxHeight + imagefontheight($this->textfont);
		}
	}
};
?>
