<?php
namespace codad5;

class EditImage {
    protected $images = [];
    protected $source;
    protected $mimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    // this are propertied that assume the serer are working with webp format
    protected $webpSupported = true;
    protected $useImageScale = true;
    protected $invalid = [];
    protected $outputSizes = [];
    protected $useLongerDimension;
    protected $jpegQuality = 75;
    protected $pngCompression = 0;
    protected $resample = IMG_BILINEAR_FIXED;
    protected $watermark;
    protected $markW;
    protected $markH;
    protected $markType;
    protected $marginR;
    protected $marginB;
    protected $destination;
    protected $generated = [];

    public function __construct(array $images, $sourceDirectory = null)
    {
        if (!is_null($sourceDirectory) && !is_dir($sourceDirectory)) {
            throw new \Exception("$sourceDirectory is not a directory.");
        }
        $this->images = $images;
        $this->source = $sourceDirectory;
        // Remove support for webp images if < PHP 5.5.0
        if (PHP_VERSION_ID < 50500) {
            array_pop($this->mimeTypes);
            $this->webpSupported = false;
        }
        // Check whether imagescale() is supported 
        if (PHP_VERSION_ID < 50519 || (PHP_VERSION_ID >= 50600 && PHP_VERSION_ID < 50603)) {
            $this->useImageScale = false;
        }
        $this->checkImages();
    }

    public function setOutputSizes(array $sizes, $useLongerDimension = true)
    {   
        if(empty($sizes)){

            throw new \Exception('Sizes can not be empty');
        }
        foreach ($sizes as $i => $size) {
            if(!is_numeric($size) || $size == 0){
                throw new \Exception('Sizes must be an array of positive integer found this `'.$size."` in sizes at position ".$i + 1);

            }

            $this->outputSizes[] = (int) $size;

        }

        $this->useLongerDimension = $useLongerDimension;
        if(!$this->useImageScale){
            $this->calculateRatios();
        }
    }

    public function setJpegQuality($number) 
    {
        if (!is_numeric($number) || $number < 0 || $number > 100) {
            throw new \Exception('JPG Quality must be a number in the range 0-100.');
        }
        $this->jpegQuality = $number;
    }

    public function setPngCompression($number) 
    {
        if (!is_numeric($number) || $number < 0 || $number > 9) {
            throw new \Exception('PNG Compression must be a number from 0 (no compression) to 9.');
        }
        $this->pngCompression = $number;
    }

    public function setResamplingMethod($value)
    {
        switch (strtolower($value)) {
            case 'bicubic':
            $this->resample = IMG_BICUBIC;
                break;
            case 'bicubic-fixed':
                $this->resample = IMG_BICUBIC_FIXED;
                break;
            case 'nearest-neighbour':
            case 'nearest-neighbor':
                $this->resample = IMG_NEAREST_NEIGHBOUR;
                break;
            default:
                $this->resample = IMG_BILINEAR_FIXED;
        }
    }
    

    public function watermark($filepath, $margin_right = 30, $margin_button = 30)
    {
        if(!file_exists($filepath) || !is_readable($filepath)){
            throw new \Exception("Error handling watermark image at $filepath");

        } 
        $size = getimagesize($filepath);
        if($size === false && $this->webpSupported && mime_content_type($filepath) == 'image/webp'){
            $SIZE['mime'] = 'image/png';

        }
        if(!in_array($size['mime'], $this->mimeTypes)){
            throw new \Exception("Watermark must be one of the following types: ".implode(', ', $this->mimeTypes));

        }
        $this->watermark = $this->createImageResource($filepath, $size['mime']);
        if($size['mime'] == 'image/webp'){
            $this->markW = imagesx($this->watermark);
            $this->markH = imagesy($this->watermark);

        }else{
            $this->markW = $size[0];
            $this->markH = $size[1];
        }

        if(is_numeric($margin_right) && $margin_right > 0){
            $this->marginR = $margin_right;
        }
        if(is_numeric($margin_button) && $margin_button > 0){
            $this->marginB = $margin_button;
        }
    }

    public function outputImages($destination)
    {
        if (!is_dir($destination) || !is_writable($destination)) {
            throw new \Exception('The destination must be a writable directory.');
        }
        $this->destination = $destination;
        // Loop through the source images
        foreach ($this->images as $i => $img) {
            // Skip files that are invalid
            if (in_array($this->images[$i]['file'], $this->invalid)) {
                continue;
            }
            // Create an image resource for the current image
            $resource = $this->createImageResource($this->images[$i]['file'], $this->images[$i]['type']);
            // Add a watermark if the $watermark property contains a value
            if ($this->watermark) {
                $this->addWatermark($this->images[$i], $resource);
            }
            // Delegate the generation of output to another method
            $this->generateOutput($this->images[$i], $resource);
            imagedestroy($resource);
        }
        // Return arrays of output and invalid files
        return ['output' => $this->generated, 'invalid' => $this->invalid];
    }

    protected function checkImages()
    {
        foreach($this->images as $i => $image) {
            $this->images[$i] = [];
            if($this->source){
                $this->images[$i]['file'] = $this->source.DIRECTORY_SEPARATOR.$image;

            }else{
                $this->images[$i]['file'] = $image;
            }
            if(file_exists($this->images[$i]['file']) && is_readable($this->images[$i]['file']))
            {
                $size = getimagesize($this->images[$i]['file']);
                if($size === false && $this->webpSupported && mime_content_type($this->images[$i]['file']) == 'image/webp')
                {
                    $this->images[$i] = $this->getWebpDetails($this->images[$i]['file']);

                }
                elseif($size[0] == 0 || !in_array($size['mime'], $this->mimeTypes)){
                    $this->invalid[] = $this->images[$i]['file'];
                }
                else{
                    if($size['mime'] == 'image/jpeg'){
                        $result = $this->checkJpegOrientation($this->images[$i]['file'], $size);
                        $this->images[$i]['file'] = $result['file'];
                        $size = $result['size'];

                    }
                    $this->images[$i]['w'] = $size[0];
                    $this->images[$i]['h'] = $size[1];
                    $this->images[$i]['type'] = $size['mime'];


                }
            }
            else{
                $this->invalid[] = $this->images[$i]['file'];
            }
        }
    }

    protected function getWebpDetails($image)
    {
        $details = [];
        $resource = imagecreatefromwebp($image);
        $details['file'] = $image;
        $details['w'] = imagesx($resource);
        $details['h'] = imagesy($resource);
        $details['type'] = 'image/webp';
        imagedestroy($resource);
        return $details;
    }

    protected function checkJpegOrientation($image, $size)
    {
        $outputFile = $image;
        $exif = exif_read_data($image);
        // Calculate required angle of rotation
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $angle = 180;
                    break;
                case 6:
                    $angle = -90;
                    break;
                case 8:
                    $angle = 90;
                    break;
                default:
                    $angle = null;
            }
            // If necessary, rotate the image
            if (!is_null($angle)) {
                $original = imagecreatefromjpeg($image);
                $rotated = imagerotate($original, $angle, 0);
                // Save the rotated file with a new name
                $extension = pathinfo($image, PATHINFO_EXTENSION);
                $outputFile = str_replace(".$extension", '_rotated.jpg', $image);
                imagejpeg($rotated, $outputFile, 100);
                // Get the dimensions and MIME type of the rotated file
                $size = getimagesize($outputFile);
                imagedestroy($original);
                imagedestroy($rotated);
            }
        }
        return ['file' => $outputFile, 'size' => $size];
    }

    protected function calculateRatios()
    {
        foreach($this->images as $i => $image) {
            $this->images[$i]['ratios'] = [];
            if($this->images[$i]['h'] > $this->images[$i]['w'] && $this->useLongerDimension){
                $divisor = $this->images[$i]['h'];

            }else{
                $divisor = $this->image[$i]['w'];
            }
            foreach($this->outputSizes as $outputsize){
                $ratio = $outputsize/$divisor;
                $this->images[$i]['ratios'][] = $ratio > 1 ? 1 : $ratio;

            }
        }
    }

    protected function createImageResource($file, $type)
    {
        switch ($type) {
            case 'image/jpeg':
                return imagecreatefromjpeg($file);
            case 'image/png';
                return imagecreatefrompng($file);
            case 'image/gif':
                return imagecreatefromgif($file);
            case 'image/webp':
                return imagecreatefromwebp($file);
        }
    }

    protected function addWatermark(array $image, $resource)
    {
        // Calculate position of top-left corner in relation to main image
        $x = $image['w'] - $this->markW - $this->marginR;
        $y = $image['h'] - $this->markH - $this->marginB;
        // Merge the watermark resource into the main image resource
        imagecopy($resource, $this->watermark, $x, $y, 0, 0, $this->markW, $this->markH);
    }

    protected function generateOutput($image, $resource)
    {
        // Store the $outputSizes in a temporary variable
        $storedSizes = $this->outputSizes;
        // Get the constituent parts of the current file name
        $nameParts = pathinfo($image['file']);
        // Use imagescale() if supported
        if ($this->useImageScale) {
            // Recalculate $outputSizes if the image's height is greater than its width
            if ($this->useLongerDimension && imagesy($resource) > imagesx($resource)) {
                $this->recalculateSizes($resource);
            }
            
            foreach ($this->outputSizes as $outputSize) {
                // Don't resize if current output size is greater than the original
                if ($outputSize >= $image['w']) {
                    continue;
                }
                $scaled = imagescale($resource, $outputSize, -1, $this->resample);
                $filename = $nameParts['filename'] . '_' . $outputSize . '.' . $nameParts['extension'];
                // Delegate file output to specialized method
                $this->outputFile($scaled, $image['type'], $filename);
            }
            if(empty($this->outputSizes)){
                $outputSize = $this->useImageSize();
                $scaled = imagescale($resource, $outputSize, -1, $this->resample);
                $filename = $nameParts['filename'] . '_' . $outputSize . '.' . $nameParts['extension'];
                // Delegate file output to specialized method
                $this->outputFile($scaled, $image['type'], $filename);
                // throw new \Exception('Hello Earth');
            }
        } else {
            // Use imagecopyresampled() if imagescale() is not supported
            foreach ($image['ratios'] as $ratio) {
                $w = round($image['w'] * $ratio);
                $h = round($image['h'] * $ratio);
                $filename = $nameParts['filename'] . '_' . $w . '.' . $nameParts['extension'];
                $scaled = imagecreatetruecolor($w, $h);
                imagecopyresampled($scaled, $resource, 0, 0, 0, 0, $w, $h, $image['w'], $image['h']);
                $this->outputFile($scaled, $image['type'], $filename);
            }
        }
        // Reassign temporarily stored sizes to the $outputSizes property
        $this->outputSizes = $storedSizes;
    }

    protected function useImageSize()
    {
        foreach($this->images as $i => $image){
        $dimension = getimagesize($image['file']);
        return  $dimension[0];

           
            

        }
    }

    protected function recalculateSizes($resource)
    {
        // Get the width and height of the image resource
        $w = imagesx($resource);
        $h = imagesy($resource);
        foreach ($this->outputSizes as &$size) {
            // Multiply the size by the width divided by the height
            // Setting the second argument of round() to -1 rounds to the nearest multiple of 10
            $size = round($size * $w / $h, -1);
        }
    }

    protected function outputFile($scaled, $type, $name)
    {
        $success = false;
        $outputFile = $this->destination . DIRECTORY_SEPARATOR . $name;
        switch ($type) {
            case 'image/jpeg':
                $success = imagejpeg($scaled, $outputFile, $this->jpegQuality);
                break;
            case 'image/png':
                $success = imagepng($scaled, $outputFile, $this->pngCompression);
                break;
            case 'image/gif':
                $success = imagegif($scaled, $outputFile);
                break;
            case 'image/webp':
                $success = imagewebp($scaled, $outputFile);
        }
        imagedestroy($scaled);
        if ($success) {
            $this->generated[] = $outputFile;
        }
    }

}