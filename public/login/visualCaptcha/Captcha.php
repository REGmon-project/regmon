<?php

namespace visualCaptcha;

//use Zend\Cache\StorageFactory;

class Captcha {
    // Object that will have a reference for the session object
    // It will have .visualCaptcha.images, .visualCaptcha.audios, .visualCaptcha.validImageOption, and .visualCaptcha.validAudioOption
    private $session = null;

    // Assets path.
    // By default, it will be ./assets
    private $assetsPath = '';

    // All the image options.
    // These can be easily overwritten or extended using addImageOptions( <Array> ), or replaceImageOptions( <Array> )
    // By default, they're populated using the ./images.json file
    private $imageOptions = Array();

    // All the audio options.
    // These can be easily overwritten or extended using addImageOptions( <Array> ), or replaceImageOptions( <Array> )
    // By default, they're populated using the ./audios.json file
    private $audioOptions = Array();

    // All the cache options
    // Theses options are the related to \Zend\Cache\Storage
    // By default, it�s been populated as null on Constructor, but you can use array options of Cache ZF2 backend   
    private $cache;
    
    // @param session is the default session object
    // @param defaultImages is optional. Defaults to the array inside ./images.json. The path is relative to ./images/
    // @param defaultAudios is optional. Defaults to the array inside ./audios.json. The path is relative to ./audios/
    public function __construct( $session, $assetsPath = null, $defaultImages = null, $defaultAudios = null, $cached = false, $cacheOptions = null) {
        // Attach the session object reference to visualCaptcha
        $this->session = $session;

        // If no assetsPath is specified, set the default
        if ( ! $assetsPath || empty( $assetsPath ) ) {
            $this->assetsPath = __DIR__ . '/assets';
        } else {
            $this->assetsPath = $assetsPath;
        }

        // If there are no defaultImages, get them from ./images.json
        if ( ! $defaultImages || count( $defaultImages ) == 0 ) {
            $defaultImages = $this->utilReadJSON( $this->assetsPath . '/images.json' );
        }

        // If there are no defaultAudios, get them from ./audios.json
        if ( ! $defaultAudios || count( $defaultAudios ) == 0 ) {
            $defaultAudios = $this->utilReadJSON( $this->assetsPath . '/audios.json' );
        }

        // Attach the images object reference to visualCaptcha
        $this->imageOptions = $defaultImages;

        // Attach the audios object reference to visualCaptcha
        $this->audioOptions = $defaultAudios;
        
        
        $this->cache = $this->setCache($cached, $cacheOptions);
        
    }
    
    /**
     * 
     * //@return \Zend\Cache\Storage\StorageInterface
     */
    private function setCache($cached, $options = null)
    {
    //    if($cached)
    //        return StorageFactory::factory($options);
        
        return false;
    }

    // Generate a new valid option
    // @param numberOfOptions is optional. Defaults to 5
    public function generate( $numberOfOptions = 5 ) {
        $imageValues = Array();

        // Save previous image & audio options from session
        $oldImageOption = $this->getValidImageOption();
        $oldAudioOption = $this->getValidAudioOption();

        // Reset the session data
        $this->session->clear();

        // Avoid the next IF failing if a string with a number is sent
        $numberOfOptions = intval( $numberOfOptions );

        // Set the minimum numberOfOptions to four
        if ( $numberOfOptions < 4 ) {
            $numberOfOptions = 4;
        }

        // Shuffle all imageOptions
        shuffle( $this->imageOptions );

        // Get a random sample of X images
        $images = $this->utilArraySample( $this->imageOptions, $numberOfOptions );

        // Set a random value for each of the images, to be used in the frontend
        foreach ( $images as &$image ) {
            $randomValue = $this->utilRandomHex( 10 );
            $imageValues[] = $randomValue;

            $image[ 'value' ] = $randomValue;
        }

        $this->session->set( 'images', $images );

        // Select a random image option, pluck current valid image option
        do {
            $newImageOption = $this->utilArraySample( $this->getImageOptions() );
        } while ( $oldImageOption && $oldImageOption[ 'path' ] == $newImageOption[ 'path' ] );

        $this->session->set( 'validImageOption', $newImageOption );

        // Select a random audio option, pluck current valid audio option
        do {
            $newAudioOption = $this->utilArraySample( $this->audioOptions );
        } while ( $oldAudioOption && $oldAudioOption[ 'path' ] == $newAudioOption[ 'path' ] );

        $this->session->set( 'validAudioOption', $newAudioOption );

        // Set random hashes for audio and image field names, and add it in the frontend data object
        $validImageOption = $this->getValidImageOption();

        $this->session->set( 'frontendData', Array(
            'values' => $imageValues,
            'imageName' => $validImageOption[ 'name' ],
            'imageFieldName' => $this->utilRandomHex( 10 ),
            'audioFieldName' => $this->utilRandomHex( 10 )
        ) );
    }

    // Stream audio file
    // @param headers object. used to store http headers for streaming
    // @param fileType defaults to 'mp3', can also be 'ogg'
    public function streamAudio( &$headers, $fileType ) {
        $audioOption = $this->getValidAudioOption();
        $audioFileName = isset( $audioOption ) ? $audioOption[ 'path' ] : ''; // If there's no audioOption, we set the file name as empty
        $audioFilePath = $this->assetsPath . '/audios/' . $audioFileName;

        // If the file name is empty, we skip any work and return a 404 response
        if ( !empty( $audioFileName ) ) {
            // We need to replace '.mp3' with '.ogg' if the fileType === 'ogg'
            if ( $fileType === 'ogg' ) {
                $audioFilePath = preg_replace( '/\.mp3/i', '.ogg', $audioFilePath );
            } else {
                $fileType = 'mp3'; // This isn't doing anything, really, but I feel better with it
            }

            return $this->utilStreamFile( $headers, $audioFilePath );
        }

        return false;
    }

    // Stream image file given an index in the session visualCaptcha images array
    // @param headers object. used to store http headers for streaming
    // @param index of the image in the session images array to send
    // @param isRetina boolean. Defaults to false
    public function streamImage( &$headers, $index, $isRetina ) {
        $imageOption = $this->getImageOptionAtIndex( $index );
        $imageFileName = $imageOption ? $imageOption[ 'path' ] : ''; // If there's no imageOption, we set the file name as empty
        $imageFilePath = $this->assetsPath . '/images/' . $imageFileName;

        // Force boolean for isRetina
        //$isRetina = intval( $isRetina ) >= 1; //we give true/false

        // If retina is requested, change the file name
        if ( $isRetina ) {
            $imageFileName = preg_replace( '/\.png/i', '@2x.png', $imageFileName );
            $imageFilePath = preg_replace( '/\.png/i', '@2x.png', $imageFilePath );
        }

        // If the index is non-existent, the file name will be empty, same as if the options weren't generated
        if ( !empty( $imageFileName ) ) {
            return $this->utilStreamFile( $headers, $imageFilePath );
        }

        return false;
    }

    // Get data to be used by the frontend
    public function getFrontendData() {
        return $this->session->get( 'frontendData' );
    }

    // Get the current validImageOption
    public function getValidImageOption() {
        return $this->session->get( 'validImageOption' );
    }

    // Get the current validAudioOption
    public function getValidAudioOption() {
        return $this->session->get( 'validAudioOption' );
    }

    // Validate the sent image value with the validImageOption
    public function validateImage( $sentOption ) {
        $validImageOption = $this->getValidImageOption();

        return ( $sentOption == $validImageOption[ 'value' ] );
    }

    // Validate the sent audio value with the validAudioOption
    public function validateAudio( $sentOption ) {
        $validAudioOption = $this->getValidAudioOption();
        $validAnswers = explode("|", strtoupper($validAudioOption[ 'value' ]));
        return (in_array(strtoupper($sentOption), $validAnswers));
    }

    // Return generated image options
    public function getImageOptions() {
        return $this->session->get( 'images' );
    }

    // Return generated image option at index
    public function getImageOptionAtIndex( $index ) {
        $imageOptions = $this->getImageOptions();

        return ( isset( $imageOptions[ $index ] ) ) ? $imageOptions[ $index ] : null;
    }

    // Alias for getValidAudioOption
    public function getAudioOption() {
        return $this->getValidAudioOption();
    }

    // Return all the image options
    public function getAllImageOptions() {
        return $this->imageOptions;
    }

    // Return all the audio options
    public function getAllAudioOptions() {
        return $this->audioOptions;
    }

    // Create a hex string from random bytes
    private function utilRandomHex( $count ) {
        return bin2hex( openssl_random_pseudo_bytes( $count ) );
    }

    // Return samples from array
    private function utilArraySample( $arr, $count = null ) {
        if ( !$count || $count == 1 ) {
            return $arr[ array_rand( $arr ) ];
        } else {
            // Limit the sample size to the length of the array
            if ( $count > count( $arr ) ) {
                $count = count( $arr );
            }

            $result = Array();
            $rand = array_rand( $arr, $count );

            foreach( $rand as $key ) {
                $result[] = $arr[ $key ];
            }

            return $result;
        }
    }

    // Read input file as JSON
    private function utilReadJSON( $filePath ) {
        if ( !file_exists( $filePath ) ) {
            return null;
        }

        return json_decode( file_get_contents( $filePath ), true );
    }

    // Stream file from path
    private function utilStreamFile( &$headers, $filePath ) {
        if ( !file_exists( $filePath ) ) {
            return false;
        }

        $mimeType = $this->getMimeType( $filePath );

        // Set the appropriate mime type
        $headers[ 'Content-Type' ] = $mimeType;

        // Make sure this is not cached
        $headers[ 'Cache-Control' ] = 'no-cache, no-store, must-revalidate';
        $headers[ 'Pragma' ] = 'no-cache';
        $headers[ 'Expires' ] = 0;
        
        $img = $this->getImageFromCache($filePath);
        echo $img;
        
        //readfile( $filePath );
        // Add some noise randomly, so images can't be saved and matched easily by filesize or checksum
        echo $this->utilRandomHex( rand(0,1500) );

        return true;
    }

    /**
     * Return the image string from cache to avoid I/O
     * @param string $filePath
     */
    private function getImageFromCache($filePath)
    {
        if($this->cache != false){
            $cacheKey = md5($filePath);
            $img = $this->cache->getItem($cacheKey);
            if(!$img){
                $img = file_get_contents($filePath);
                $this->cache->setItem($cacheKey,$img);
            }
            return $img;
        }
        
        return file_get_contents($filePath);
    }
    
    
    // Get File's mime type
    private function getMimeType( $filePath ) {
        if ( function_exists('mime_content_type') ) {
            return mime_content_type( $filePath );
        } else {
            // Some PHP 5.3 builds don't have mime_content_type because it's deprecated
            if ( function_exists('finfo_open') ) {// Use finfo (right way)
                $finfo = finfo_open( FILEINFO_MIME_TYPE );

                if ( $mimetype = finfo_file($finfo, $filePath) ) {
                    finfo_close( $finfo );
                    return $mimetype;
                }
            } elseif ( function_exists('pathinfo') ) {// Use pathinfo
                if ( $pathinfo = pathinfo($filePath) ) {
                    $imagetypes = array( 'gif', 'jpg', 'png' );

                    if ( in_array($pathinfo['extension'], $imagetypes) && getimagesize($filePath) ) {
                        $size = getimagesize( $filePath );
                        return $size[ 'mime' ];
                    }
                }
            }

            // Just figure out from a set of possibilities, if we didn't figure it out before
            $fileProperties = explode('.', $filePath);
            $extension = end($fileProperties);

            switch ( $extension ) {
                case 'png':
                    return 'image/png';

                case 'gif':
                    return 'image/gif';

                case 'jpg':
                case 'jpeg':
                    return 'image/jpeg';

                case 'mp3':
                    return 'audio/mpeg3';

                case 'ogg':
                    return 'audio/ogg';

                default:
                    return 'application/octet-stream';
            }
        }
    }
};

?>
