<?php
declare(strict_types=1);

namespace App\Services\ImageConverter;

class ImageConverter
{
    /**
     * @param string $base64String
     * @param string $outputFile
     *
     * @return string
     */
    public function base64ToImage(string $base64String, string $outputFile): string
    {
        $file = fopen($outputFile, "wb");

        $data = explode(',', $base64String);

        fwrite($file, base64_decode($data[1]));
        fclose($file);

        return $outputFile;
    }

    /**
     * @param string $base64ImageString
     *
     * @return string
     */
    public function getExtensionFromBase64(string $base64ImageString): string
    {
        $splited = explode(',', substr( $base64ImageString , 5 ) , 2);
        $mime = $splited[0];
        $mimeSplitWithoutBase64 = explode(';', $mime,2);
        $mimeSplit = explode('/', $mimeSplitWithoutBase64[0],2);

        if(count($mimeSplit)==2) {
            $extension = $mimeSplit[1];

            if($extension=='jpeg') {
                $extension='jpg';
            }
        }

        return $extension;
    }
}