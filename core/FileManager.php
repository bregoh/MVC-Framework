<?php

/**
 * Credits to Tutorial 24X7 for the file resize function
 * https://github.com/tutorials24x7/image-resize-php/blob/master/resize-image.php
 */

namespace Core;

class FileManager
{
    // Allowed Extensions for file uploads
    private $allowedExtensions = array('jpg','jpeg','png','docx','pdf','txt','html','php','gif', 'avi', 'mp4', 'mkv');

    // new file name for the uploaded files
    private $newFileName = null;

    // Array of new sizes for resizing images and resize properties
    private $sizesArray = [];
    private $minimumDimensions = false;
    private $exactDimensions = false;
    private $quality = 100;

    // file logger array
    private $fileErrors = [];
    private $fileEvent = [];
    
    private $guestFolder = "application" . DS . "controller";
    private $uploadPath = null;
    private $uploadFolder = null;
    private $totalSize = 0;
    private $maxSize = 3 * 1024 * 1024; // number * kb * mb;
    public $duplicate = false;

    // ACL for file manipulation
    public $canDelete = true;
    public $canEdit = true;
    public $canCreate = true;
    public $authorized = true;
    public $isAdmin = true;

    
    /**
     * getRootPath
     *
     * @return string
     */
    public function getRootPath()
    {
        if(!$this->isAdmin)
        {
            return ROOT_PATH . DS . $this->guestFolder;
        }

        return ROOT_PATH;
    }
    
    /**
     * getExcludedFiles
     * 
     * @return Array
     */
    public function getExcludedFiles()
    {
        if(!$this->isAdmin)
        {
            return array(".", "..", ".git", ".htaccess", "core", "README.md", "css", "js", "fonts", "config", "debug", "img");
        }
        
        return array(".", "..", ".git", ".htaccess");
    }
    
    /**
     * getSize
     *
     * @param  string $file
     * @return integer
     */
    public function getSize($file)
    {
        $excludedFiles = $this->getExcludedFiles();
        $filename = $file;
        if(\is_dir($filename))
        {
            $dirList = array_diff(\scandir($filename), $excludedFiles);

            foreach($dirList as $dir)
            {
                $this->getSize($filename. DS . $dir); // recursively search the folders
            }
        }
        else
        {
            $this->totalSize += \filesize($filename);
        }

        return $this->totalSize;
    }
    
    /**
     * formatSize
     *
     * @param  string $size
     * @return string
     */
    public function formatSize($size)
    {
        $output = 0;

        if($size >= 1073741824)
        {
            $output = \number_format($size / 1073741824, 2)." GB";
        }
        else if($size >= 1048576)
        {
            $output = \number_format($size / 1048576, 2)." MB";
        }
        else if($size >= 1024)
        {
            $output = \number_format($size / 1024, 2)." KB";
        }
        else if($size > 1)
        {
            $output = $size." bytes";
        }
        else if($size == 1)
        {
            $output = $size." byte";
        }
        else
        {
            $output = $size." bytes";
        }

        return $output;
    }
    
    /**
     * setMaxSize
     *
     * @param  integer $size
     * @return void
     */
    public function setMaxSize($size)
    {
        $this->maxSize = $size;
    }
       
    /**
     * isSizeValid
     *
     * @param  integer $size
     * @return void
     */
    public function isSizeValid($size)
    {
        if(intval($size) > $this->maxSize)
        {
            $this->logError(
                "file_size_error", 
                "file Size is greater than the allowed size for this upload", 
                "upload files less than ".$this->formatSize($this->maxSize)
            );

            $this->logFileEvent("failed", "File size validation");
            return false;
        }

        $this->logFileEvent("success", "file size validation");
        return true;
    }
    
    /**
     * getFileExtension
     *
     * @param  mixed $file
     * @return string/null
     */
    public function getFileExtension($file = null)
    {
        if (!is_null($file))
		{
			$file = explode(".", $file);
			$extension = !empty($file) ? array_pop($file) : null;
			switch($extension)
			{
				case 'pdf': return 'pdf'; break;
				case 'jpg': case 'jpeg': return 'jpg'; break;
				case 'png': return 'png'; break;
				case 'gif': return 'gif'; break;
				case 'doc':	return 'doc'; break;
				case 'docx': return 'docx'; break;
				case 'css': return 'css'; break;
				case 'php': return 'php'; break;
				case 'js': return 'js'; break;
				case 'txt': return 'txt'; break;
				case 'mp3': return 'mp3'; break;
				case 'mp4': return 'mp4'; break;
				case 'avi': return 'avi'; break;
				case 'mkv': return 'mkv'; break;
				case 'html': return 'html'; break;
				case 'ppt': return 'ppt'; break;
				case 'xls': case 'xlsx': return 'xlsx'; break;
				default: return null;
			}
		}
    }
    
    /**
     * isExtensionValid
     *
     * @param  string $file
     * @return boolean
     */
    public function isExtensionValid($file)
    {
        $file_ext = $this->getFileExtension($file);

        if(is_null($file_ext))
        {
            $this->logError(
                "extension_error", 
                "extension is null, file is invalid or not supported", 
                "try again with a valid file"
            );

            return false;
        }

		if(!in_array($file_ext, $this->allowedExtensions))
		{
            $this->logError(
                "extension_error", 
                "extension is not supported", 
                "try again with a valid file"
            );

			return false;
        }
        
		return true;
    }

    public function setExactDimensions($bool = null)
    {
        if(is_null($bool))
        {
            $this->logError(
                "dimension_error",
                "setExactDimnensions function called but parameter is set to null",
                "default is set to true"
            );

            return;
        }
        
        $this->exactDimensions = $bool;
    }

    public function setMinimumDimensions($bool = true)
    {
        if(is_null($bool))
        {
            $this->log(
                "dimension_error",
                "setMinimumDimensions function called but parameter is set to null",
                "default is set to true"
            );

            return;
        }

        $this->minimumDimensions = $bool;
    }

    public function setUploadPath($path = null)
    {
        if(!\is_null($path))
        {
            $this->uploadPath = ROOT_PATH . DS . $path;
            $this->uploadFolder = $path;
        }
    }

    public function setFileName($name)
    {
        $this->newFileName = $name;
    }

    public function setResizeValues($sizes = [])
    {
        $error = [];
        foreach($sizes as $size)
        {
            if(!array_key_exists("width", $size))
            {
                $this->logError(
                    "size_error",
                    "width not available as an array key in the size",
                    "check the sizes if width is added and correctly spelt"
                );

                $error[] = "size_error";
            }

            if(!array_key_exists("height", $size))
            {
                $this->logError(
                    "size_error",
                    "height not available as an array key in the size",
                    "check the sizes if height is added and correctly spelt"
                );

                $error[] = "size_error";
            }
        }

        if(!empty($error))
        {
            $this->sizesArray = [];
            return;
        }

        $this->sizesArray = $sizes;
    }

    public function allowedUploadExtensions($extension = [])
    {
        $this->allowedExtensions = $extension;
    }

    public function createFileName($file, $ext)
    {
        if(file_exists($this->uploadPath . DS . $file))
        {
            $fileTmp = \explode(".", $file);
            $filename = $fileTmp[0]."_".rand(0, 99)."_".date("Y-m-d")."_".date("H:i:s").".".$ext;
        }
        else
        {
            $filename = $file;
        }

        if(!is_null($this->newFileName))
        {
            $filename = $this->newFileName.".".$ext;
        }

        return $filename; 
    }

    public function upload($files)
    {
        $output = [];
        $filename = "";

        if(!$this->isUploadPathValid()) return false;

        for($i = 0; $i < count($files["tmp_name"]); $i++)
        {
            $filename = $files["name"][$i];
            $fileTmp = $files["tmp_name"][$i];
            $sizeTmp = $files["size"][$i];

            $ext = $this->getFileExtension($filename);

            if(!$this->validateUploadRequest($filename, $sizeTmp))
            {
                $output[] = [
                    "filename" => $filename,
                    "upload_status" => "failed",
                    "disk_path" => null,
                    "web_link" => null,
                    "date" => $this->systemTime()
                ];

                continue;
            }
            $newFileName = $this->createFileName($filename, $ext);
            $fileToUpload = $this->uploadPath . DS . $newFileName;
            
            if(!move_uploaded_file($fileTmp, $fileToUpload))
            {
                $this->logError(
                    "upload_error", 
                    "could not upload file", 
                    "contact admin"
                );

                $output[] = [
                    "filename" => $filename,
                    "upload_status" => "failed",
                    "disk_path" => null,
                    "web_link" => null,
                    "date" => $this->systemTime()
                ];

                $this->logFileEvent(
                    "failed",
                    "unable to upload ".$newFileName
                );

                continue;
            }

            $uploadFolder = $this->uploadFolder;

            $this->logFileEvent(
                "success",
                "$filename uploaded"
            );

            $output[] = [
                "original_filename" => $filename,
                "new_name" => $newFileName,
                "upload_status" => "success",
                "disk_path" => $fileToUpload,
                "resize" => false,
                "resizeInfo" => [],
                "web_link" => BASE_URL.$uploadFolder."/".$newFileName,
                "date" => $this->systemTime()
            ];
        }
        
        return json_encode($output);
    }

    public function uploadFromURL($urlToUpload)
    {
        $output = [];

        $files = array_filter($urlToUpload);

        if(!$this->isUploadPathValid()) return false;

        for($i = 0; $i < count($files); $i++)
		{
            if(!filter_var($files[$i], FILTER_VALIDATE_URL)) continue;

            $name_bits = explode('/', $files[$i]);
            $filename = end($name_bits);
                
            $size_img = get_headers($files[$i], 1);
            $sizeTmp = $size_img["Content-Length"];

            $extension = $this->getFileExtension($filename);

            if(!$this->validateUploadRequest($filename, $sizeTmp))
            {
                $output[] = [
                    "filename" => $filename,
                    "upload_status" => "failed",
                    "disk_path" => null,
                    "resize" => false,
                    "resizeInfo" => [],
                    "web_link" => null,
                    "date" => $this->systemTime()
                ];

                continue;
            }

            if(!empty($this->getError()))
            {
                $output[] = [
                    "filename" => $filename,
                    "upload_status" => "failed",
                    "disk_path" => null,
                    "resize" => false,
                    "resizeInfo" => [],
                    "web_link" => null,
                    "date" => $this->systemTime(),
                    "message" => "view error log for details"
                ];

                continue;
            }

            $image = $this->createImageByExtension($files[$i], $extension);
            $aspectRatio = $this->getAspectRatio($image);
            
            if(empty($this->sizesArray))
            {
                $this->logError(
                    "image_resize_error",
                    "resize sizes are empty",
                    "set resize sizes in array [width => xx, height => xx]"
                );
                break;
            }

            foreach($this->sizesArray as $size)
            {
                $tmpOutput = [];

                $newFileName = $this->createFileName($filename, $extension);
                $optimalSize = $this->getOptimalSize($aspectRatio, $image, $size);
                $width = $optimalSize["width"];
                $height = $optimalSize["height"];

                $tmpImage = \imagecreatetruecolor($width, $height);

                $fileToUpload = $this->uploadPath . DS . $newFileName;

                $this->saveImage($tmpImage, $image, $extension, $width, $height, $size, $fileToUpload);

                $uploadFolder = $this->uploadFolder;

                $this->logFileEvent(
                    "sucess",
                    $newFileName." uploaded"
                );

                $tmpOutput[] = [
                    "new_name" => $newFileName,
                    "width" => $size["width"],
                    "height" => $size["height"],
                    "web_link" => BASE_URL.$uploadFolder."/".$newFileName
                ];
            }

            $output[] = [
                "original_filename" => $filename,
                "upload_status" => "success",
                "disk_path" => $fileToUpload,
                "resize" => true,
                "resizeInfo" => $tmpOutput,
                "web_link" => "",
                "date" => $this->systemTime()
            ];
        }
        
        return json_encode($output);
    }

    private function createImageByExtension($file, $extension)
    {
        $image = null;

        switch($extension) {
            case 'jpg':
            case 'jpeg': {
                $image = @\imagecreatefromjpeg($file);
                break;
            }
            case 'png': {
                $image = @\imagecreatefrompng($file);
                break;
            }
            case 'gif': {
                $image = @\imagecreatefromgif($file);
                break;
            }
        }

        return $image;
    }

    private function getAspectRatio($image)
    {
        $aspectRatio = 0;

        $width = \imagesx($image);
        $height = \imagesy($image);

        $aspectRatio = $width / $height;

        return $aspectRatio;
    }

    private function getOptimalSize($aspectRatio, $image, $newSize)
    {
        $optimalSize = [];
        $optimalRatio = 0;

        $optimalHeight = 0;
        $optimalWidth = 0;

        $oldHeight = \imagesy($image);
        $oldWidth = \imagesx($image);

        $newHeight = $newSize["height"];
        $newWidth = $newSize["width"];

        if($this->minimumDimensions)
        {
            $heightRatio = $oldHeight / $newHeight;
            $widthRatio = $oldWidth / $newWidth;

            $optimalRatio = $widthRatio;

            if($widthRatio > $heightRatio)
            {
                $optimalRatio = $heightRatio;
            }

            $optimalWidth = $oldWidth / $optimalRatio;
            $optimalHeight = $oldHeight / $optimalRatio;
        }
        else
        {
            $optimalWidth = $aspectRatio >= 1 ? $newWidth : ($aspectRatio * $newHeight);
            $optimalHeight = $aspectRatio <= 1 ? $newHeight : ($newWidth / $aspectRatio);
        }

        return $optimalSize = [
            "width" => $optimalWidth,
            "height" => $optimalHeight
        ];
    }

    public function resizeImage($fileToUpload)
    {
        $output = [];

        $files = array_filter($fileToUpload);

        if(!$this->isUploadPathValid()) return false;

        for($i = 0; $i < count($files["tmp_name"]); $i++)
        {
            $tmpFile = $files["tmp_name"][$i];
            $filename = $files["name"][$i];
            $sizeTmp = $files["size"][$i];

            if($filename == "")
            {
                continue;
            }

            $extension = $this->getFileExtension($filename);

            if(!$this->validateUploadRequest($filename, $sizeTmp))
            {
                $output[] = [
                    "filename" => $filename,
                    "upload_status" => "failed",
                    "disk_path" => null,
                    "resize" => false,
                    "resizeInfo" => [],
                    "web_link" => null,
                    "date" => $this->systemTime()
                ];

                continue;
            }

            if(!empty($this->getError()))
            {
                $output[] = [
                    "filename" => $filename,
                    "upload_status" => "failed",
                    "disk_path" => null,
                    "resize" => false,
                    "resizeInfo" => [],
                    "web_link" => null,
                    "date" => $this->systemTime(),
                    "message" => "view error log for details"
                ];
                continue;
            }

            $image = $this->createImageByExtension($tmpFile, $extension);
            $aspectRatio = $this->getAspectRatio($image);

            if(empty($this->sizesArray))
            {
                $this->logError(
                    "image_resize_error",
                    "resize sizes are empty",
                    "set resize sizes in array [width => xx, height => xx]"
                );
                break;
            }

            foreach($this->sizesArray as $size)
            {
                $tmpOutput = [];

                $newFileName = $this->createFileName($filename, $extension);
                $optimalSize = $this->getOptimalSize($aspectRatio, $image, $size);
                $width = $optimalSize["width"];
                $height = $optimalSize["height"];

                $tmpImage = \imagecreatetruecolor($width, $height);

                $fileToUpload = $this->uploadPath . DS . $newFileName;

                $this->saveImage($tmpImage, $image, $extension, $width, $height, $size, $fileToUpload);

                $uploadFolder = $this->uploadFolder;

                $this->logFileEvent(
                    "sucess",
                    $newFileName." uploaded"
                );

                $tmpOutput[] = [
                    "new_name" => $newFileName,
                    "width" => $size["width"],
                    "height" => $size["height"],
                    "web_link" => BASE_URL.$uploadFolder."/".$newFileName
                ];
            }

            $output[] = [
                "original_filename" => $filename,
                "upload_status" => "success",
                "disk_path" => $fileToUpload,
                "resize" => true,
                "resizeInfo" => $tmpOutput,
                "web_link" => "",
                "date" => $this->systemTime()
            ];
        }
        
        return json_encode($output);
    }

    private function saveImage($tmpImage, $image, $extension, $width, $height, $size, $fileToUpload)
    {
        $oldWidth = \imagesx($image);
        $oldHeight = \imagesy($image);
        $output = [];
        $newWidth = $size["width"];
        $newHeight = $size["height"];

        if($extension == "png")
        {
            \imagealphablending($tmpImage, false);
            \imagesavealpha($tmpImage, true);
            
            //$transparent = \imagecolorallocate($tmpImage, 255, 255, 255, 127);
            $transparent = \imagecolorallocate($tmpImage, 255, 255, 255);
            \imagefilledrectangle($tmpImage, 0, 0, $width, $height, $transparent);
        }

        \imagecopyresampled($tmpImage, $image, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);

        //set exactDimension to false for images not needing cropping
        //set true if you need to crop the image
        if($this->exactDimensions)
        {
            $cropStartWidth = ($width / 2) - ($newWidth /2);
            $cropStartHeight = ($height / 2) - ($newHeight / 2);

            $crop = $tmpImage;

            $tmpImage = \imagecreatetruecolor($newWidth, $newHeight);

            if($extension == "png")
            {
                \imagealphablending($tmpImage, false);
                \imagesavealpha($tmpImage, true);

                $transparent = \imagecolorallocate($tmpImage, 255, 255, 255, 127);
                \imagefilledrectangle($tmpImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            \imagecopyresampled($tmpImage, $crop, 0, 0, $cropStartWidth, $cropStartHeight, $newWidth, $newHeight, $newWidth, $newHeight);
        }


        switch($extension) {
            case 'jpg':
            case 'jpeg': {
                if( imagetypes() & IMG_JPG ) 
                {
                    imagejpeg( $tmpImage, $fileToUpload, $this->quality );
                }
                break;
            }
            case 'png': {
                $scale = round( ( $this->quality / 100 ) * 9 );

                $invert = 9 - $scale;

                if( imagetypes() & IMG_PNG ) {

                    imagepng( $tmpImage, $fileToUpload, $invert );
                }
                break;
            }
            case 'gif': {
                if( imagetypes() & IMG_GIF ) {

                    imagegif( $tmpImage, $fileToUpload );
                }
                break;
            }
        }

        \imagedestroy($tmpImage);
    }

    public function isUploadPathValid()
    {
        if(is_null($this->uploadPath))
        {
            $this->logError(
                "upload_path_error",
                "upload path is null",
                "set an upload path for this action"
            );

            $this->logFileEvent(
                "failed",
                "upload path not provided for upload"
            );

            return false;
        }

        if(!\file_exists($this->uploadPath))
        {
            $this->logError(
                "upload_path_error",
                "upload path doesn't exist",
                "set a correct upload path for this action"
            );

            $this->logFileEvent(
                "failed",
                "upload path not available for upload"
            );

            return false;
        }

        return true;
    }

    public function validateUploadRequest($filename, $size)
    {
        // check if the extension is valid
        $extension = $this->getFileExtension($filename);

        if(!$this->validateCreateRequest())
        {
            return false;
        }

        if(!$this->isExtensionValid($extension))
        {
            return false;
        }

        // check if the file size is valid

        if(!$this->isSizeValid($size))
        {
            return false;
        }

        return true;
    }
    
    /**
     * validateCreateRequest
     *
     * @return boolean
     */
    public function validateCreateRequest()
    {
        if(!$this->canCreate)
        {
            $this->logError(
                "create_error",
                "no permission to perform this action",
                "contact admin"
            );

            $this->logFileEvent(
                "failed",
                "unauthorized create action performed"
            );

            return false;
        }

        if(!$this->authorized)
        {
            $this->logError(
                "create_error",
                "no permission to perform this action, user invalid",
                "contact admin"
            );

            $this->logFileEvent(
                "failed",
                "unauthorized create action performed"
            );

            return false;
        }

        return true;
    }
    
    /**
     * validateEditRequest
     *
     * @return boolean
     */
    public function validateEditRequest()
    {
        if(!$this->canDelete)
        {
            $this->logError(
                "edit_error",
                "no permission to perform this action",
                "contact admin"
            );

            $this->logFileEvent(
                "failed",
                "unauthorized edit action performed"
            );

            return false;
        }

        if(!$this->authorized)
        {
            $this->logError(
                "edit_error",
                "no permission to perform this action, user invalid",
                "contact admin"
            );

            $this->logFileEvent(
                "failed",
                "unauthorized edit action performed"
            );

            return false;
        }

        return true;
    }
    
    /**
     * validateDeleteRequest
     *
     * @return boolean
     */
    public function validateDeleteRequest()
    {
        if(!$this->canDelete)
        {
            $this->logError(
                "delete_error",
                "no permission to perform this action",
                "contact admin"
            );

            $this->logFileEvent(
                "failed",
                "unauthorized delete action performed"
            );

            return false;
        }

        if(!$this->authorized)
        {
            $this->logError(
                "delete_error",
                "no permission to perform this action, user invalid",
                "contact admin"
            );

            $this->logFileEvent(
                "failed",
                "unauthorized delete action performed"
            );

            return false;
        }

        return true;
    }
    
    /**
     * createFolder
     *
     * @param  mixed $currentPath
     * @param  mixed $folderName
     * @return boolean
     */
    public function createFolder($currentPath, $folderName)
    {
        $fileToCreate = $currentPath . DS . $folderName;
        if(\file_exists($fileToCreate))
        {
            if(!$this->duplicate)
            {
                $this->logError(
                    "folder_creation_error", 
                    "file/folder already exists in the location",
                    "change the filename/extension type or set duplicate to true"
                );
                $this->logFileEvent("failed", "Folder Creation");
                return false;
            }

            $folderName = $folderName."_Copy_".rand(0, 9999);
        }

        if(!\mkdir($fileToCreate, 0777, true))
        {
            $this->logError(
                "folder_creation_error",
                "unable to create folder",
                "check the path if its a valid path or check the root path to make sure you are giving the correct root path"
            );

            $this->logFileEvent("failed", "folder creation");
            return false;
        }

        $this->logFileEvent("success", "folder created");

        return true;
    }
    
    /**
     * deleteFolder
     *
     * @param  mixed $folderToDelete
     * @return void
     */
    public function deleteFolder($folderToDelete)
    {
        if(!$this->validateDeleteRequest())
        {
            return false;
        }

        if(!$this->isDirectoryEmpty($folderToDelete))
        {
            $output = $this->getDirectoryStructure($folderToDelete);
            
            foreach($output["file"] as $file)
            {
                $this->deleteFile($file["path"]);
            }

            
            foreach($output["folder"] as $folder)
            {
                $this->deleteFolder($folder["path"]);
            }
        }

        $this->logFileEvent("success", "folder deleted");
        \rmdir($folderToDelete);

        return true;
    }

    public function renameFolder($folderToRename, $newName)
    {
        if(!$this->validateEditRequest())
        {
            return false;
        }

        $tmpOldName = explode(DS, $folderToRename);
        array_pop($tmpOldName);
        $newName = \implode(DS, $tmpOldName). DS . $newName;

        if(!rename($folderToRename, $newName))
        {
            $this->logError(
                "folder_rename_error",
                "unable to rename folder",
                "check the path if its a valid path or check the root path to make sure you are giving the correct root path"
            );
            return false;
        }

        $this->logFileEvent("success", "folder renamed");
        return true;
    }

    public function copyFolder($source, $destination)
    {
        
    }

    public function moveFolder()
    {

    }

    public function zipFolder()
    {

    }

    public function unZipFolder()
    {

    }
    
    /**
     * changeFilePermission
     *
     * @param  string $file
     * @param  string $permission
     * @return boolean
     */
    public function changeFilePermission($fileToChange, $permission)
    {
        if(is_dir($fileToChange))
        {
            if(!$this->isDirectoryEmpty($fileToChange))
            {
                $output = $this->getDirectoryStructure($fileToChange);
                
                foreach($output["file"] as $file)
                {
                    $this->changeFilePermission($file["path"], 0777);
                }

                
                foreach($output["folder"] as $folder)
                {
                    $this->changeFilePermission($folder["path"], 0777);
                }
            }
        }

        if(!chmod($fileToChange, $permission))
        {
            $this->logError(
                "permission_error",
                "failed to change permission",
                "contact admin"
            );

            $this->logFileEvent(
                "failed",
                "failed to change file permission"
            );

            return false;
        }

        $tmp = explode(DS, $fileToChange);
        $fileName = array_pop($tmp);

        $this->logFileEvent(
            "success",
            "$fileName permission was changed"
        );        
        return true;
    }

    public function createFile($pathToSaveFile, $fileNameWithExtension)
    {
        if(!$this->validateCreateRequest())
        {
            return false;
        }

        if(\file_exists($pathToSaveFile . DS . $fileNameWithExtension))
        {
            if(!$this->duplicate)
            {
                $this->logError(
                    "file_creation_error",
                    "file duplication not allowed",
                    "contact admin to allow duplication"
                );

                $this->fileEvent(
                    "failed",
                    "failed to create file"
                );

                return false;
            }

            $fileNameWithExtension = "copy_".$this->systemTime()."_".$fileNameWithExtension;
        }
        
        if(!\fopen($pathToSaveFile . DS . $fileNameWithExtension, "w"))
        {
            $this->logError(
                "file_creation_error",
                "unable to create file",
                "contact admin"
            );

            $this->logFileEvent(
                "failed",
                "failed to create file"
            );

            return false;
        }

        $this->logFileEvent(
            "success",
            "file created"
        );

        return true;

    }
    
    
    /**
     * deleteFile
     *
     * @param  string $file
     * @return boolean
     */
    public function deleteFile($file)
    {
        if(!$this->validateDeleteRequest())
        {
            return false;
        }

        if(!unlink($file))
        {
            $this->logError(
                "file_deletion_error",
                "unable to delete file",
                "contact admin"
            );

            $this->logFileEvent(
                "failed",
                "failed to delete file"
            );

            return false;
        }

        $this->logFileEvent(
            "success",
            "file deleted"
        );

        return true;
    }

    public function copyFile($source, $destination)
    {
        if(!$this->validateEditRequest())
        {
            return false;
        }

        if(\file_exists($destination))
        {
            if(!$this->duplicate)
            {
                $this->logError(
                    "file_copy_error", 
                    "file already exists in the location",
                    "change the filename/type or set duplicate to true"
                );

                $this->logFileEvent("failed", "cannot copy file");
                return false;
            }

            $tmp = \explode(DS, $destination);
            $fileToCopy = array_pop($tmp);

            $fileToCopy = "copy_".rand(0, 9999)."_".$fileToCopy;
            $destination = implode(DS, $tmp). DS . $fileToCopy;
        }

        if(!copy($source, $destination))
        {
            $this->logError(
                "folder_copy_error",
                "unable to copy folder",
                "check the path if its a valid path or check the root path to make sure you are giving the correct root path"
            );

            return false;
        }

        $this->logFileEvent("success", "file copied");
        return true;
    }

    public function moveFile($source, $destination)
    {
        if(!$this->validateEditRequest())
        {
            return false;
        }

        if(\file_exists($destination))
        {
            if(!$this->duplicate)
            {
                $this->logError(
                    "file_move_error", 
                    "file already exists in the location",
                    "change the filename/type or set duplicate to true"
                );

                $this->logFileEvent("failed", "cannot move file");
                return false;
            }

            $tmp = \explode(DS, $destination);
            $fileToCopy = array_pop($tmp);

            $fileToCopy = "copy_".rand(0, 9999)."_".$fileToCopy;
            $destination = implode(DS, $tmp). DS . $fileToCopy;
        }

        if(!rename($source, $destination))
        {
            $this->logError(
                "file_move_error",
                "unable to move folder",
                "check the path if its a valid path or check the root path to make sure you are giving the correct root path"
            );
            return false;
        }

        $this->logFileEvent("success", "file moved");
        return true;
    }

    public function renameFile($source, $destination)
    {
        if(!$this->validateEditRequest())
        {
            return false;
        }

        if(\file_exists($destination))
        {
            $this->logError(
                "file_rename_error", 
                "file already exists in the location",
                "change the filename/type"
            );

            $this->logFileEvent("failed", "cannot rename file");

            return false;
        }

        if(!rename($source, $destination))
        {
            $this->logError(
                "file_rename_error",
                "unable to rename folder",
                "check the path if its a valid path or check of the name is correct"
            );
            return false;
        }

        $this->logFileEvent("success", "file renamed");
        return true;
    }

    public function downloadFile()
    {

    }

    public function readFile()
    {

    }

    public function getFileDetails()
    {

    }
    
    /**
     * getDirectoryStructure
     *
     * @param  mixed $path
     * @return void
     */
    public function getDirectoryStructure($path = null)
    {
        if(\is_null($path)) $path = $this->getRootPath();

        $excludeFiles = $this->getExcludedFiles();

        $output = [];
        $folder = [];
        $files = [];

        $scannedFiles = array_diff(\scandir($path), $excludeFiles);

        foreach($scannedFiles as $file)
        {
            $fileProperties = [];
            $tmpSize = "";

            if(\is_dir($path . DS . $file))
            {
                $fileProperties["isEmpty"] = false;

                if($this->isDirectoryEmpty($path . DS . $file))
                {
                    $fileProperties["isEmpty"] = true;
                }

                $fileProperties["filename"] = $file;
                $fileProperties["dir"] = $path;
                $fileProperties["path"] = $path . DS . $file;
                $fileProperties["size"] = $this->formatSize($this->getSize($path . DS . $file));
                $folder[] = $fileProperties;
                $this->totalSize = 0;
            }
            else
            {
                $fileProperties["filename"] = $file;
                $fileProperties["dir"] = $path;
                $fileProperties["path"] = $path . DS . $file;
                $fileProperties["size"] = $this->formatSize($this->getSize($path . DS . $file));
                $files[] = $fileProperties;
                $this->totalSize = 0;
            }
        }

        $output["folder"] = $folder;
        $output["file"] = $files;

        $this->logFileEvent("success", "Directory listing");
        \closedir();
        
        return json_encode($output);
    }
    
    /**
     * isDirectoryEmpty
     *
     * @param  mixed $dir
     * @return void
     */
    public function isDirectoryEmpty($dir)
    {
        return (($files = @scandir($dir)) && count($files) <= 2);
    }

    private function logFileEvent($type, $message)
    {
        $this->fileEvent[] = [
            "type" => $type,
            "message" => $message,
            "time" => $this->systemTime()
        ];
    }

    public function getFileEvent()
    {
        return $this->fileEvent;
    }

    private function logError($type, $message, $fix)
    {
        $this->fileErrors[] = [
            "type" => $type,
            "message" => $message,
            "fix" => $fix,
            "time" => $this->systemTime()
        ];
    }

    public function getError()
    {
        return $this->fileErrors;
    }


    public function systemTime()
    {
        return date("Y-m-d H:i:s");
    }
}
?>