<?

class Thumbnail
{
    private $errmsg = "";
    private $error = false;
    private $format = "";
    private $file = "";
    private $max_width = 0;
    private $max_height = 0;
    private $percent = 0;

    public function setParams($file, $max_width = 0, $max_height = 0, $percent = 0)
    {
    	$image = getimagesize($file);
		
        if (!file_exists($file))
        {
            $this->errmsg = "File doesn't exists";
            $this->error = true;
        }
        else if (!is_readable($file))
        {
            $this->errmsg = "File is not readable";
            $this->error = true;
        }

        if ($image['mime'] == "image/gif") {
            $this->format = "GIF";
        } elseif ($image['mime'] == "image/jpeg") {
            $this->format = "JPEG";
        } elseif ($image['mime'] == "image/png") {
            $this->format = "PNG";
        }
        else
        {
            $this->errmsg = "Unknown file format";
            $this->error = true;
        }

        if ($max_width == 0 && $max_height == 0 && $percent == 0)
        {
            $percent = 100;
        }

        $this->max_width = $max_width;
        $this->max_height = $max_height;
        $this->percent = $percent;
        $this->file = $file;
    }

    function calc_width($width, $height)
    {
        $new_width = $this->max_width;
        $new_wp = (100*$new_width)/$width;
        $new_height = ($height*$new_wp)/100;
        return array ($new_width, $new_height);
    }

    function calc_height($width, $height)
    {
        $new_height = $this->max_height;
        $new_hp = (100*$new_height)/$height;
        $new_width = ($width*$new_hp)/100;
        return array ($new_width, $new_height);
    }

    function calc_percent($width, $height)
    {
        $new_width = ($width*$this->percent)/100;
        $new_height = ($height*$this->percent)/100;
        return array ($new_width, $new_height);
    }

    function return_value($array)
    {
        $array[0] = intval($array[0]);
        $array[1] = intval($array[1]);
        return $array;
    }

    function calc_image_size($width, $height)
    {
        $new_size = array ($width, $height);

        if ($this->max_width > 0 && $width > $this->max_width)
        {
            $new_size = $this->calc_width($width, $height);

            if ($this->max_height > 0 && $new_size[1] > $this->max_height)
            {
                $new_size = $this->calc_height($new_size[0], $new_size[1]);
            }

            return $this->return_value($new_size);
        }

        if ($this->max_height > 0 && $height > $this->max_height)
        {
            $new_size = $this->calc_height($width, $height);
            return $this->return_value($new_size);
        }

        if ($this->percent > 0)
        {
            $new_size = $this->calc_percent($width, $height);
            return $this->return_value($new_size);
        }
		
		return $this->return_value($new_size);
    }

    function show_error_image()
    {
        header("Content-type: image/png");
        $err_img = ImageCreate(220, 25);
        $bg_color = ImageColorAllocate($err_img, 0, 0, 0);
        $fg_color1 = ImageColorAllocate($err_img, 255, 255, 255);
        $fg_color2 = ImageColorAllocate($err_img, 255, 0, 0);
        ImageString($err_img, 3, 6, 6, "ERROR:", $fg_color2);
        ImageString($err_img, 3, 55, 6, $this->errmsg, $fg_color1);
        ImagePng($err_img);
        ImageDestroy($err_img);
    }

    function show($name = "", $logo_file = "")
    {
        if ($this->error)
        {				
            //$this->show_error_image();
			echo $this->errmsg;
            return;
        }

        $size = GetImageSize($this->file);
        
		$new_size = $this->calc_image_size($size[0], $size[1]);
		        
		
        if (function_exists("ImageCreateTrueColor"))
        {
            $new_image = ImageCreateTrueColor($new_size[0], $new_size[1]);
        }
        else
        {
            $new_image = ImageCreate($new_size[0], $new_size[1]);
        }

        switch($this->format)
        {
            case "GIF":
                $old_image = ImageCreateFromGif($this->file);
                break;
            case "JPEG":
                $old_image = ImageCreateFromJpeg($this->file);
                break;
            case "PNG":
                $old_image = ImageCreateFromPng($this->file);
                break;
        }

        ImageCopyResized($new_image, $old_image, 0, 0, 0, 0, $new_size[0], $new_size[1], $size[0], $size[1]);

        switch($this->format)
        {
            case "GIF":
                if (! empty($name))
                {
                    ImageGif($new_image, $name);
                }
                else
                {
                    header("Content-type: image/gif");
                    ImageGif($new_image);
                }
            break;
            case "JPEG":
                if (!empty($logo_file) && !empty($name)) {
                	$logo_places = array(
				        0=>array('text' => "верх - слева", 'x' => 0, 'y' => 0),  
				        1=>array('text' => "верх - справа", 'x' => 1, 'y' => 0),
				        2=>array('text' => "низ - слева", 'x' => 0, 'y' => 1),
				        3=>array('text' => "низ - справа", 'x' => 1, 'y' => 1),
				        );
					
					$kx = $logo_places[3]['x'];
        			$ky = $logo_places[3]['y'];
					
					$logo = ImageCreateFromPNG($logo_file);
					$a1 = GetImageSize($logo_file);
					$a2 = $new_size;
					$f = ImageCreateTrueColor(imagesx($new_image), imagesy($new_image));
					ImageCopy($f,$new_image,0,0,0,0,imagesx($new_image),imagesy($new_image));
					ImageAlphaBlending($f, 1);
					ImageAlphaBlending($logo, 1);
					ImageCopy($f, $logo, $kx * ($a2[0] - $a1[0]) + 2 * (0.5 - $kx), $ky * ($a2[1] - $a1[1]) + 2 * (0.5 - $ky), 0, 0, $a1[0], $a1[1]);
					ImageJpeg($f, $name, 100);					
                } elseif (!empty($name)) {
                    ImageJpeg($new_image, $name, 100);
                } else {
                    header("Content-type: image/jpeg");
                    ImageJpeg($new_image, NULL, 100);
                }
            break;
            case "PNG":
                if (! empty($name))
                {
                    ImagePng($new_image, $name);
                }
                else
                {
                    header("Content-type: image/png");
                    ImagePng($new_image);
                }
            break;
    }

    ImageDestroy($new_image);
    ImageDestroy($old_image);
    return;    
}

	function save($name, $logo_file = "")
	{
		if(!empty($logo_file)) $this->show($name, $logo_file); 
	    else $this->show($name);
	}
		 
}
