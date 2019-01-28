<?
class SimpleImage {

   var $image;
   var $image_type;
   var $image_var;

   function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
         $this->image_var = 'jpg';
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
         $this->image_var = 'gif';
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
         $this->image_var = 'png';
      } elseif( $this->image_type == IMAGETYPE_WEBP ) {
         $this->image = imagecreatefromwebp($filename);
         $this->image_var = 'webp';
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
      $filename = $this->getFilename($filename);
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);      
      } elseif( $image_type == IMAGETYPE_WEBP ) {
         imagewebp($this->image,$filename);
      }     
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
   }
   function getFilename($filename) {
      $filename .= '.'.$this->image_var;
      echo "filename:$filename<br>";
      return $filename;
   }
   function saveWebp($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
      if( $image_type == IMAGETYPE_WEBP ) {
         imagewebp($this->image,$filename);
      }
      else {
         $webPName = explode('.', $filename)[0].'.webp';
         exec("cwebp -q 70 ".$filename." -o ".$webPName." ");
         imagewebp($this->image,$webPName);
      }
   }

   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }
   }
   function getWidth() {
      return imagesx($this->image);
   }
   function getHeight() {
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }
}
?>