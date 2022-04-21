<?php

namespace App\Service;
class UploadService{
    public function upload($file, $fileName, $uploadPath)
    {
        if($file){
            $fileName = $fileName.'.'.$file->guessExtension();
            $file->move($uploadPath, $fileName);
            return $fileName;
        } else {
            return false;
        }
    }

}