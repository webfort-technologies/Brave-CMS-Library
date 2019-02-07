<?php

/*
Programmer : Shishir Raven
Purpose : Class to create thumbnail at runtime for Bigger images
How it works 
a) Whenever we have to display thumbnail call a object as $a = new thumbnail('image.jpg','200',300,'images')
	Parameter 1 - Image Name
	Parameter 2 - Image width
	Parameter 3 - Image height
	Parameter 4 - Image foldoer
	
b) This code first checks the Thumbnail folder inside the Image folder to see if the thumbnail of that size if already available
	If thumbnail is avialable it displays the same thumbail
	If thumbail is not avialbale it creates one. 
	The name of the thumbail would be like image_200_300.jpg for dimensions 200 X 300

Note : Create a folder named thumb and give it wirte rights before you use the class. 

*/
/*   Example of how to use the class
include("../thumbnailer.php");
$config['image']="sample.jpg";
$config['folder']="images";
$config['width'] ="100";
$config['height']="300";
$config['compression']="100";
$config['fit_to_box']=true;
$config['fit_to_scale']=true;
$thumb1 = new thumbnailer($config);
echo "<img src='".$thumb1->create_thumb()."'/>";
 */
Class thumbnailer
{
	var $image="";
	var $folder="";
	var $width="";
	var $height="";
	var $compression="100";	
	var $new_image_type="";
	var $new_image="";
	var $old_image_width="";
	var $old_image_height="";
	var $fit_to_box=false;
	var $fit_to_scale=false;
	var $fit_aspect_ratio=false;
	var $orignal_width="";
	var $orignal_height="";

	//Initializer function - We pass a array. Matches array keys with the class variable. if they match sets class vaiable value equal to the array key value.
	
	function initializer($config= array())
	{
		if(count($config)>0) // Checking to see if the array is empty or not
		{
				foreach($config as $key=> $value) // Looping a config array extracting array key and its value into $key and $value
				{
						if(isset($this->$key)) // Is a Class variable there corresponing to the array key
						{
							$this->$key=$value; // Setting Class Variable to the array Key Value
						}
				}
		}
		$this->orignal_width=$this->width;
		$this->orignal_height=$this->height;
	}
	
	
	// Constructor  
	
	function thumbnailer($config)
	{
	// running initializer with the configration array
		$this->initializer($config);
		// $imagepath =$this->folder."/".$this->image;
		$imagepath =$this->folder."/".$this->image;
		if(file_exists($imagepath) && !is_dir($imagepath)) {
			$this->create_new_image();
			$this->check_dimensions();
		}
	}	
	
	// The following function checks to see if the image thumnail is already build
	function create_thumb()
	{	
		if($this->fit_to_scale === true)
		{
			$thumbpath = $this->folder."/thumbs/fts_".$this->width."_".$this->height."_".$this->image;
		}
		else
		{
			$thumbpath = $this->folder."/thumbs/ftb_".$this->width."_".$this->height."_".$this->image;
		}
			   	if(!file_exists($thumbpath)) // Checking to see it the file already exists or not 
                {	 
					$imagepath = $this->folder."/".$this->image; 
					if(file_exists($imagepath) && !is_dir($imagepath)) {
						
						$this->resize();
						$this->save($this->new_image_type);
					}else{
					  return '';
					}
				}
				return $thumbpath; 
	}
	//Function to create a New image form the image path and store it into $new_image identifier
	function create_new_image() 
	{
		$blending = true;
	  $imagepath =$this->folder."/".$this->image;
      $image_info = getimagesize($imagepath);
	  $this->new_image_type = $image_info[2];
	  if($this->new_image_type == IMAGETYPE_JPEG) {
         $this->new_image = imagecreatefromjpeg($imagepath);
      } elseif( $this->new_image_type == IMAGETYPE_GIF ) {
         $this->new_image = imagecreatefromgif($imagepath);
      } elseif( $this->new_image_type == IMAGETYPE_PNG ) {
		$blending = false;
         $this->new_image = imagecreatefrompng($imagepath);
      }
	  
	  // Finding width and height of current image
	  	$this->old_image_width=imagesx($this->new_image);
		$this->old_image_height=imagesy($this->new_image);
		
	 
		
	}
	
	// The following function finds out new width or height according to aspect ratio
	function check_dimensions()
	{
		if($this->width=="")
		{
		$ratio = $this->height / $this->old_image_height;
      	$this->width = round($this->old_image_width * $ratio);	
		}
		if($this->height=="")
		{
		$ratio = $this->width / $this->old_image_width;
      	$this->height = round($this->old_image_height * $ratio);	
		}
	}
	// Resizing Thumbnail
   function resize() {
     
		$crop_x_offset = 0;
		$crop_y_offset = 0;
		if($this->fit_to_scale === true)
		{	
			$source_image_path=$this->folder.$this->image;
			$thumbnail_image_path=$this->folder."thumbs/".$this->width."_".$this->height."_".$this->image;
			
			$source_image_width=$this->old_image_width ;
			$source_image_height=$this->old_image_height;

			$source_aspect_ratio = $source_image_width / $source_image_height;
			$thumbnail_aspect_ratio = $this->width / $this->height;
			if ($source_image_width <= $this->width && $source_image_height <= $this->height)
			{
				$thumbnail_image_width = $source_image_width;
				$thumbnail_image_height = $source_image_height;
			}
			elseif ($thumbnail_aspect_ratio > $source_aspect_ratio)
			{
				$thumbnail_image_width = (int) ($this->height * $source_aspect_ratio);
				$thumbnail_image_height = $this->height;
			}
			else 
			{
				$thumbnail_image_width = $this->width;
				$thumbnail_image_height = (int) ($this->width / $source_aspect_ratio);
			}
			if ($this->fit_aspect_ratio == true) {
				$thumbnail_gd_image = imagecreatetruecolor($this->old_image_width, $this->old_image_height);
			}
			else{
				$thumbnail_gd_image = imagecreatetruecolor($this->width, $this->height);
			}
			
			$dest_starting_x_corrdinate=round(($this->width-$thumbnail_image_width)/2);
			$dest_starting_y_corrdinate=round(($this->height-$thumbnail_image_height)/2);
			imagealphablending($thumbnail_gd_image, FALSE);
        	imagesavealpha($thumbnail_gd_image, TRUE);
    
			$whiteBackground = imagecolorallocatealpha($thumbnail_gd_image,255, 255, 255, 127);
			
			imagefill($thumbnail_gd_image,0,0,$whiteBackground);

			if ($this->fit_aspect_ratio == true) {
				
				imagecopyresampled($thumbnail_gd_image, $this->new_image, $dest_starting_x_corrdinate, $dest_starting_y_corrdinate, 0, 0, $this->old_image_width, $this->old_image_height, $source_image_width, $source_image_height);
			}
			else{
				imagecopyresampled($thumbnail_gd_image, $this->new_image, $dest_starting_x_corrdinate, $dest_starting_y_corrdinate, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
			}
			
			$this->new_image = $thumbnail_gd_image;  
		}
		else
		{
		if($this->fit_to_box === true)
		{
		
	  // Fit to box code. // this code will be responisble for fitting the image to box. 
		$image_aspect = $this->old_image_width / $this->old_image_height;	
		$thumb_aspect = $this->width / $this->height;
	
		if ($image_aspect > $thumb_aspect) {
			$crop_height = $this->old_image_height;
			$crop_width = round($crop_height * $thumb_aspect);
			$crop_x_offset = round(($this->old_image_width - $crop_width) / 2);
		} else {
			$crop_width = $this->old_image_width;
			$crop_height = round($crop_width / $thumb_aspect);
			$crop_y_offset = round(($this->old_image_height - $crop_height) / 2);
		}
 
 
		// crop parameter
		//$crop_size = $crop_width.'x'.$crop_height.'+'.$crop_x_offset.'+'.$crop_y_offset;
		$this->old_image_width	= $crop_width;
		$this->old_image_height	= $crop_height;
		//  echo $crop_width."<br/>";
	 // echo  $crop_height."<br/>";
		
		}
	  // code ends here 
		if ($this->fit_aspect_ratio == true) {
			$new_image_final = imagecreatetruecolor($this->old_image_width, $this->old_image_height);
		}
		else{
			$new_image_final = imagecreatetruecolor($this->width, $this->height);
		}
		
		
	 // preserve transparency for PNG and GIF images
		
        if ($this->new_image_type == 3 || $this->new_image_type == 1){
		
          // allocate a color for thumbnail
            $background_black = imagecolorallocate($new_image_final, 0, 0, 0);
			// define a color as transparent
            imagecolortransparent($new_image_final, $background_black);
			
         // set the blending mode for thumbnail
			imagealphablending($new_image_final, false);
          //   set the flag to save alpha channel
           imagesavealpha($new_image_final, true);
        }
		if ($this->fit_aspect_ratio == true) {
			imagecopyresampled($new_image_final, $this->new_image,0, 0, $crop_x_offset, $crop_y_offset,  $this->old_image_width, $this->old_image_height, $this->old_image_width, $this->old_image_height);
		}
		else{
			imagecopyresampled($new_image_final, $this->new_image,0, 0, $crop_x_offset, $crop_y_offset,  $this->width, $this->height, $this->old_image_width, $this->old_image_height);
		}
	
	  //imagecopyresampled($new_image, $this->new_image,  0,0,0, 0, $this->width, $this->height, $this->old_image_width, $this->old_image_height);
	  
      $this->new_image = $new_image_final;   
	  }
   }  
		
	
	// Saving Thumbail
	function save($image_type) 
	{

		if($this->fit_to_scale === true)
		{
			 $filename =$this->folder."/thumbs/fts_".$this->orignal_width."_".$this->orignal_height."_".$this->image;
		}
		else
		{
			 $filename =$this->folder."/thumbs/ftb_".$this->orignal_width."_".$this->orignal_height."_".$this->image;
		}
	 
	  if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->new_image,$filename,$this->compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->new_image,$filename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
		imagepng($this->new_image,$filename);
			
      }   
   }

}



?>