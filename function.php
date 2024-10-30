<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Image resize
 * @param int $width
 * @param int $height
 */
function crowdideas_resize($width, $height, $rand){
  /* Get original image x y*/
  $uploads = wp_upload_dir();
  list($w, $h) = getimagesize($_FILES['image']['tmp_name']);
  /* calculate new image size with ratio */
  $ratio = max($width/$w, $height/$h);
  $h = ceil($height / $ratio);
  $x = ($w - $width / $ratio) / 2;
  $w = ceil($width / $ratio);
  /* new file name */
  #$path = ROOTDIR.'/uploads/campaigns/'.$width.'x'.$height.'_'.$rand.'_'.$_FILES['image']['name'];  
  $path = $uploads['basedir'].'/crowdideas/campaigns/'.$width.'x'.$height.'_'.$rand.'_'.$_FILES['image']['name'];  
  /* read binary data from image file */
  $imgString = file_get_contents($_FILES['image']['tmp_name']);
  /* create image from string */
  $image = imagecreatefromstring($imgString);
  $tmp = imagecreatetruecolor($width, $height);
  imagecopyresampled($tmp, $image,
    0, 0,
    $x, 0,
    $width, $height,
    $w, $h);
  /* Save image */
  switch ($_FILES['image']['type']) {
    case 'image/jpeg':
      imagejpeg($tmp, $path, 100);
      break;
    case 'image/png':
      imagepng($tmp, $path, 0);
      break;
    case 'image/gif':
      imagegif($tmp, $path);
      break;
    default:
      exit;
      break;
  }
  return $path;
  /* cleanup memory */
  imagedestroy($image);
  imagedestroy($tmp);
}
?>