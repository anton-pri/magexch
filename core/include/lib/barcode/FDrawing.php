<?php
if (!defined('APP_START')) die('Access denied');

/**
 * Holds the drawing $im and can also holds all forms and texts
 * You must call init().
 * You can use get_im() to add other kind of form not held into these classes.
 */
class FDrawing {
	private $w, $h;		// int
	private $color;		// Fcolor
	private $filename;	// char *
	private $im;		// {object}
	private $barcode = array();// BarCode *

	public function __construct($w = 1524, $h = 1024, $filename = '', $color = array(255, 255, 255)) {
		$this->w = $w;
		$this->h = $h;
		$this->filename = $filename;
		$this->color = new Fcolor ($color[0], $color[1], $color[2]);
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		$this->destroy();
	}

	/**
	 * Init Image and color background
	 */
	public function init(){
		$this->im = imagecreate($this->w, $this->h)
		or die('Can\'t Initialize the GD Libraty');
		imagecolorallocate($this->im,$this->color->r(),$this->color->g(),$this->color->b());
	}

	/**
	 * @return ressource
	 */
	public function get_im() {
		return $this->im;
	}
	public function set_im($im) {
		$this->im = $im;
	}

	/**
	 * Add barcode into the drawing array (for future drawing)
	 *
	 * @param BarCode $barcode
	 */
	public function add_barcode(BarCode $barcode) {
		$this->barcode[] = $barcode;
	}

	/**
	 * Draw first all forms and after all texts on $im
	 */
	public function draw_all() {
		for($i=0;$i<count($this->barcode);$i++)
			$this->barcode[$i]->draw($this->im);
	}

	/**
	 * Save $im into the file (many format available)
	 *
	 * @param int $image_style
	 * @param int $quality
	 */
	public function finish($image_style=IMG_FORMAT_PNG,$quality=100) {
		if($image_style==constant('IMG_FORMAT_PNG')){
			if(empty($this->filename))
				imagepng($this->im);
			else
				imagepng($this->im,$this->filename);
		}
		elseif($image_style==constant('IMG_FORMAT_JPEG'))
			imagejpeg($this->im,$this->filename,$quality);
        elseif($image_style==constant('IMG_FORMAT_GIF')) {
            $this->filename?imagegif($this->im, $this->filename):imagegif($this->im);
        }
	}

	/**
	 * Free the memory of PHP (called also by destructor)
	 */
	public function destroy() {
		imagedestroy($this->im);
	}
};
?>