<?php
/*
* Thumbnail类 生成缩略图
* 2010-09-08
*/

//自定义一组异常类，发生错误时自动抛出异常
class ThumbnailException extends Exception{
public function __construct($message = null ,$code =0){
   parent::__construct($message ,$code);
   error_log('Error in '.$this->getFile(). 'line: '.$this->getLine().' Error: '.$this->getMessage());
}
}
class ThumbnailFileException extends ThumbnailException{ }
class ThumbnailNotSupportedException extends ThumbnailException{ }

class Thumbnail{
      private $maxWidth;
      private $maxHeight;
      private $scale;
      private $inflate;
      private $types;
      private $imgLoaders;
      private $imgCreators;
      private $source;
      private $sourceWidth;
      private $sourceHeight;
      private $sourceMime;
      private $thumb;
      private $thumbWidth;
      private $thumbHeight;
/*
* 构造函数 带4个参数 前两个参数分别是缩略图的最大像素宽度和最大像素高度 第三个参数设置是否按比例将图像缩小为缩略图 第四个参数设置处理小图像时 是否放大
* $types 缩略图的图片类型
* loadFile 允许指定一个装载的本地文件
* loadData 同 loadFile   取得数据库中的字符串
*/
   public function __construct($maxWidth, $maxHeight, $scale = true,$inflate = true){
      $this->maxWidth = $maxWidth;
      $this->maxHeight = $maxHeight;
      $this->scale = $scale;
      $this->inflate = $inflate;

      $this->types = array('image/jpeg','image/png','image/gif');
      $this->imgLoaders = array(
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png' => 'imagecreatefrompng',
            'image/gif' => 'imagecreatefromgif'
      );
      $this->imgCreators = array('image/jpeg'=>'imagejpeg','image/png'=>'imagepng','image/gif'=>'imagegif');
   }

   public function loadFile($image){
   if(!$dims = @getimagesize($image)){
      throw new ThumbnailFileException('Could not find image: '.$image);
   }
   if(in_array($dims['mime'],$this->types)){
      $loader = $this->imgLoaders[$dims['mime']];
      $this->source = $loader($image);
      $this->sourceWidth = $dims[0];
      $this->sourceHeight = $dims[1];
      $this->sourceMime = $dims['mime'];
      $this->initThumb();
      return true;
   }else{
      throw new ThumbnailNotSupportedException('Image Mime type '.$dims['mime'].' not supported!');
   }
   }

   public function loadData($image, $mime){
   if(in_array($mime,$this->types)){
      if($this->source = @imagecreatefromstring($image)){
       $this->sourceWidth = imagesx($this->source);
       $this->sourceHeight = imagesy($this->source);
       $this->sourceMime =$mime;
       $this->initThumb();
       return true;
      }else{
       throw new ThumbnailFileException('Could not load image from string');
      }
   }else{
      throw new ThumbnailNotSupportedException('Image Mine type '.$mime.' not supported');
   }
   }

/*]
* buildThumb() 生成缩略图的函数
* getMime() 返回MIME类型 该类型可以为缩略图生成Content-type头
* getThumbWidth() 返回缩略图的像素宽度
* getThumbHeight() 返回缩略图的像素高度
* 私有方法initThumb() 为类处理缩放功能
*/
   public function buildThumb($file = null){
   $createor = $this->imgCreators[$this->sourceMime];
   if(isset($file)){
      return $createor($this->thumb, $file);
   }else{
      return $createor($this->thumb);
   }
   }

   public function getMime(){
   return $this->sourceMime;
   }

   public function getThumbWidth(){
   return $this->thumbWidth;
   }
   public function getThumbHeight(){
   return $this->thumbHeight;
   }
   private function initThumb(){
   if($this->scale){
      if($this->sourceWidth > $this->sourceHeight){
       $this->thumbWidth = $this->maxWidth;
       $this->thumbHeight = floor($this->sourceHeight * ($this->maxWidth / $this->sourceWidth));
      }else if($this->sourceWidth < $this->sourceHeight){
       $this->thumbHeight = $this->maxHeight;
       $this->thumbWidth = floor($this->sourceWidth * ($this->maxHeight / $this->sourceHeight));
       //$this->thumbHeight = floor($this->sourceHeight * ($this->maxWidth / $this->sourceWidth));
      }else{
       $this->thumbWidth = $this->maxWidth;
       $this->thumbHeight = $this->maxHeight;
      }
   }
    // imagecreatetruecolor() 创建空白的缩略图
    $this->thumb = imagecreatetruecolor($this->thumbWidth, $this->thumbHeight);
    //将原图像复制到缩略图图像
    if($this->sourceWidth <= $this->maxWidth && $this->sourceHeight <= $this->maxHeight && $this->inflate == false){
    $this->thumb = $this->source;
    }else{
    imagecopyresampled( $this->thumb, $this->source, 0 ,0 , 0, 0, $this->thumbWidth, $this->thumbHeight, $this->sourceWidth, $this->sourceHeight);
    }
   }
}
?>