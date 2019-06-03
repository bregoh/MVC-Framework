<?php
class File_Manager
{
	public $rootPath = ROOT_PATH;
	public $newfile;
	public $uploadpath;
	public $maxsize = 3072 * 1024;
	public $allowedExt = array('jpg','jpeg','png','docx','pdf','txt','html','php','gif');
	public $getuploadPath = array();
	public $file_errors;
	public $p;
	public $fileName;
	
	
	/*************************************************************
	** The structure function is used to load the files to the
	** user interface when the page loads
	*************************************************************/
	public function structure()
	{
		//$home =  ROOT_PATH;

		return $this->getDirectory($this->rootPath);

	}

	/*************************************************************
	** The getDirectory function core function that get the.......
	** directory list, and controls how files are displayed
	*************************************************************/
	public function getDirectory($path, $rel = DS, $up = '')
	{
		//var_dump($path);
		$handle = opendir($path);

		$output = '';
		/*************************************************************
		** Array to hide files from showing in the user interface
		*************************************************************/
		$fileArray = array('.', '..', '.htaccess', 'core', 'config.php', 'index.php', 'lib', '_notes', 'application', 'config', 'css', 'js', 'revolution', 'email_templates', 'fonts', 'font-awesome', 'images', 'img', 'jsh_shop', 'README.md', 'favicon.ico', 'deployment-config.json', 'default.php');

		$output .= '<ul id="file-structure" class="list-unstyled" data-path="'.$rel.'">'.$up;
		while(false != $file = readdir($handle))
		{
			if(!in_array($file, $fileArray)) // check if files in array
			{
				if(is_dir($path . DS . $file)) //  is file a directory
				{
					if($this->isDirEmpty($path . DS . $file)) // is directory
					{
						$output .= '<li id="openFileDir" class="list-folder folder" data-path="'.$file.'"><a href="#."><img src="'.BASE_URL.'images/folders-empty.png" width="30" height="30" /><span> '.$file.'</span></a></li>';
					}
					else
					{
						$output .= '<li id="openFileDir" class="list-folder folder" data-path="'.$file.'"><a href="#."><img src="'.BASE_URL.'images/folders.png" width="30" height="30" /><span> '.$file.'<span></a></li>';
					}
				}
				else // if its a file get extension and output the icon
				{
					$ext = $this->getFileExtension($file);
					$img = '';
					if($ext == 'php')
					{
						$img = '<img src="'.BASE_URL.'images/phip-512.png" width="30" height="30" />';
					}
					if($ext == 'pdf')
					{
						$img = '<img src="'.BASE_URL.'images/logo-adobe.jpg" width="30" height="30" />';
					}
					if($ext == 'txt')
					{
						$img = '<img src="'.BASE_URL.'images/txt.png" width="30" height="30" />';
					}
					if($ext == 'js')
					{
						$img = '<img src="'.BASE_URL.'images/javascript.png" width="30" height="30" />';
					}
					if($ext == 'css')
					{
						$img = '<img src="'.BASE_URL.'images/cs3s-512.png" width="30" height="30" />';
					}
					if($ext == 'doc' || $ext == 'docx')
					{
						$img = '<img src="'.BASE_URL.'images/docx.png" width="30" height="30" />';
					}
					if($ext == 'jpg' || $ext == 'jpeg')
					{
						$img = '<img src="'.BASE_URL.$rel.'/'.$file.'" width="30" height="30" />';
					}
					if($ext == 'png')
					{
						$img = '<img src="'.BASE_URL.$rel.'/'.$file.'" width="30" height="30" />';
					}
					if($ext == 'gif')
					{
						$img = '<img src="'.BASE_URL.$rel.'/'.$file.'" width="30" height="30" />';
					}
					if($ext == 'mp3')
					{
						$img = '<img src="'.BASE_URL.'images/mp3.png" width="30" height="30" />';
					}
					if($ext == 'html')
					{
						$img = '<img src="'.BASE_URL.'images/htm1l.png" width="30" height="30" />';
					}
					if($ext == 'ppt')
					{
						$img = '<img src="'.BASE_URL.'images/ppt.png" width="30" height="30" />';
					}
					if($ext == 'xlsx')
					{
						$img = '<img src="'.BASE_URL.'images/excel.png" width="30" height="30" />';
					}
					$output .= '<li class="list-folder"><a target="_blank" href="'.rtrim(BASE_URL, '/').$rel.'/'.$file.'">'.$img.$file.'</a></li>';
				}
				$output .= '<div class="dropdown">
										<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
										<span class="caret"></span></button>
										<ul class="dropdown-menu folder-action">
											<li><a id="folderAction" href="#." data-name="'.$file.'" data-action="rename">Rename</a></li>
											<li><a id="folderAction" href="#." data-name="'.$file.'" data-action="delete">Delete</a></li>
										</ul>
									</div>';
			}
		}
		$output .= "</ul>";

		return $output;

	}

	/*************************************************************
	** Function to get file extension
	*************************************************************/
	public function getFileExtension($file = null)
	{
		if (!empty($file))
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
				case 'html': return 'html'; break;
				case 'ppt': return 'ppt'; break;
				case 'xls': case 'xlsx': return 'xlsx'; break;
				default: return null;
			}
		}
	}

	/*************************************************************
	** Function to check if directory is empty
	*************************************************************/
	public function isDirEmpty($dir = null)
	{
		return (($files = @scandir($dir)) && count($files) <= 2);
	}

	/*************************************************************
	** Function to access inside a folder
	*************************************************************/
	public function goIn($p_path, $f_folder)
	{
		if(!empty($p_path))
		{
			$folder = substr($p_path, 1);

			if (!empty($f_folder))
			{
				if (!empty($folder))
				{
					$rel = DS.$folder.DS.$f_folder;
				}
				else
				{
					$rel = DS.$f_folder;
				}

				$up = '<li class="folder-up"><a href="#."><img src="'.BASE_URL.'images/return.png" width="30" height="30" /><span>Go up</span></a></li>';
				$path = ROOT_PATH.$rel;
			}
			else
			{
				$path = ROOT_PATH;
				$rel = DS;
				$up = null;
			}
		}

		return $this->getDirectory($path, $rel, $up);
	}

	/*************************************************************
	** Function to go one folder back
	*************************************************************/
	public function goUp($p_path)
	{
		if (!empty($p_path))
		{
			$folder = substr($p_path, 1);
			if (!empty($folder))
			{
				$folder = explode(DS, $folder);
				if (count($folder) > 1)
				{
					array_pop($folder);
					$rel = DS.implode(DS, $folder);
					$up = '<li class="folder-up"><a href="#."><img src="'.BASE_URL.'images/return.png" width="30" height="30" /><span>Go up</span></a></li>';
				}
				else
				{
					$up = null;
					$rel = DS;
				}

				$path = ROOT_PATH.$rel;

			}
			else
			{
				$path = ROOT_PATH;
				$rel = DS;
				$up = null;
			}
		}
		return $this->getDirectory($path, $rel, $up);
	}

	/*************************************************************
	** Function to retrieve the current folder path
	*************************************************************/
	private function getCurrentPath($p)
	{
		$getPath = explode(DS, $p);
		$path = '';
		if(!empty($getPath))
		{
			foreach ($getPath as $key)
			{
				if($key != '' || $key != null)
				{
					$path .= $key.DS;
				}
			}
		}
		return $path;
	}
	/*************************************************************
	** Function to rename folder
	*************************************************************/
	public function renameFolder($p_path, $oldName,$newName)
	{
		$relPath = $p_path;
		$path = $this->getCurrentPath($relPath);

		if(rename($path.$oldName, $path.$newName))
		{
			return true;
		}
	}

	/*************************************************************
	** Function to delete folder
	*************************************************************/
	public function deleteFolder($p_path, $fileName)
	{
		$relPath = $p_path;
		$path = $this->getCurrentPath($relPath);
		$file = $path.$fileName;
		if(is_dir($file))
		{
			if($this->isDirEmpty($file))
			{
				if(rmdir($file))
					return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if(unlink($file))
				return true;
		}
	}

	/*************************************************************
	** Function to Create folder
	*************************************************************/
	public function createFile($p_path, $fileName)
	{
		$relPath = $p_path;
		$file = $fileName;
		$path = $this->getCurrentPath($relPath);
		if(!file_exists($path.$file))
		{
			$ext = $this->getFileExtension($file);
			if($ext == null)
			{
				if(mkdir($path.$file, 0777, true))
				{
					return true;
				}
			}
			else
			{
				if(fopen($path.$file, 'w'))
				{
					return true;
				}
			}
		}
	}

	/*************************************************************
	** Function to Copy folder
	*************************************************************/
	public function copyFile($src, $dest)
	{
		if(copy($src, $dest))
		{
			return true;
		}
	}
	
	/*************************************************************
	** Function to validate a file size before upload
	*************************************************************/
	
	public function isSizeValid($fileSize)
	{
		if($fileSize > $this->maxsize)
		{
			$this->u_errors('File Size','Size is greater than the set threshold');
			return false;
		}
		return true;
	}
	
	public function fileExist($file)
	{
		if(file_exists($this->uploadpath.DS.$file))
		{
			$this->u_errors('File Exist','File already exist in the path');
			return true;
		}
		return false;
	}
	
	/*************************************************************
	** Function to validate a file extension before upload
	*************************************************************/
	public function isExtValid($file)
	{
		$file_ext = $this->getFileExtension($file);

		if(!empty($this->allowedExt) && in_array($file_ext, $this->allowedExt))
		{
			return true;
		}
		$this->u_errors('File Extension','File type is not in allowed extension or array is empty');
		return false;
	}
	
	/*************************************************************
	** Function to validate a file before upload
	*************************************************************/
	public function validate()
	{
		for($i = 0; $i < count($this->newfile['tmp_name']); $i++)
		{	
			$fileSize = $this->newfile['size'][$i];
			$fileName = $this->newfile['name'][$i];
			
			if($this->isSizeValid($fileSize)&&$this->isExtValid($fileName))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	/*************************************************************
	** Function to save and upload image
	*************************************************************/
	private function saveImage($src, $path, $ext, $width, $height, $oldwidth, $oldheight)
	{
		$tmpImage = imagecreatetruecolor($width, $height);
		imagealphablending($tmpImage, false); ///
		imagecopyresampled($tmpImage, $src, 0, 0, 0, 0, $width, $height, $oldwidth, $oldheight);

		if($ext == 'jpg' || $ext == 'jpeg')
		{
			imagejpeg($tmpImage, $path, 100);
		}
		else if($ext == 'png' || $ext == 'PNG')
		{
			imagesavealpha($tmpImage, true); ///
			imagepng($tmpImage, $path,8);
		}
		else if($ext == 'gif' || $ext == 'GIF')
		{
			imagegif($tmpImage, $path, 100);
		}
		else // error for uploading image
		{
			$this->u_errors('File Save','Error creating and saving files');
			return false;
		}
		imagedestroy($tmpImage);
		return true;
	}
	
	/*************************************************************
	** Function to resize image before upload
	*************************************************************/
	public function resizeImage($file, $width = 0, $height = 0, $sizeArray = array())
	{
		$this->newfile = $file;
		
		if($this->validate())
		{
			for($i = 0; $i < count($this->newfile['tmp_name']); $i++)
			{
				$fileName = $this->newfile['name'][$i];
				$newNameTemp = ($this->fileName == "") ? rand(10000,1000000) : $this->fileName."_".($i+1);
				$fileTmp = $this->newfile['tmp_name'][$i];
				
				$ext = $this->getFileExtension($fileName);
				
				$path = '';
				
				$src = '';
				
				if($fileName != "")
				{
					if($ext == 'jpg' || $ext == 'jpeg')
					{
						$src = imagecreatefromjpeg($fileTmp);
						//echo 'jpg';
					}
					if($ext == 'png' || $ext == 'PNG')
					{
						$src = imagecreatefrompng($fileTmp);
						//echo 'png';
					}
					if($ext == 'gif' || $ext == 'GIF')
					{
						$src = imagecreatefromgif($fileTmp);
						//echo 'gif';
					}

					list($oldwidth, $oldheight) = getimagesize($fileTmp);

					// check if its a single file resize or an array of size
					if(empty($sizeArray) & $height > 0 && $width > 0)
					{
						$newName = $newNameTemp."_".$height.'x'.$width.".".$ext;
						$path = $this->uploadpath.DS.$newName;
						
						if($this->saveImage($src, $path, $ext, $width, $height, $oldwidth, $oldheight))
						{
							array_push($this->getuploadPath, BASE_URL.$this->p.'/'.$newName);
						}
					}
					else
					{
						$arr = array(); // empty array to save file link
						for($j = 0; $j < count($sizeArray); $j++)
						{
							$path = $this->uploadpath.DS.$newNameTemp."_".$sizeArray[$j].'x'.$sizeArray[$j].".".$ext;

							if($this->saveImage($src, $path, $ext, $sizeArray[$j], $sizeArray[$j], $oldwidth, $oldheight))
							{
								$newName = $newNameTemp."_".$sizeArray[$j].'x'.$sizeArray[$j].".".$ext;
								array_push($arr, BASE_URL.$this->p.'/'.$newName);
							}
						}
						array_push($this->getuploadPath, $arr);
					}
					
				}
			}
		}
		else
		{
			return false;
		}
		return true;
	}
	
	public function upload($file, $filename = "")
	{
		$this->newfile = $file;
		$copy = "";
		$newName = "";
		
		if($this->validate())
		{
			for($i = 0; $i < count($this->newfile['tmp_name']); $i++)
			{
				$fileName = $this->newfile['name'][$i];
				$newNameTemp = rand(10000,1000000);
				$fileTmp = $this->newfile['tmp_name'][$i];
				
				$ext = $this->getFileExtension($fileName);
				
				$newName = $filename.".".$ext;;
				
				if($filename == "")
					$newName = $newNameTemp.".".$ext;
				
				$v = $i + 1;
				
				if($fileName != "")
				{
					if($this->fileExist($newName))
					{
						$copy .= "_copy";
						$newName = $newNameTemp.$copy.".".$ext;
					}
					
					move_uploaded_file($fileTmp, $this->uploadpath.DS.$newName);
					array_push($this->getuploadPath, BASE_URL.$this->p.'/'.$newName);
					//unlink($fileTmp);
				}		
				
			}
		}
		else
		{
			return false;
		}
		
		return true;
	}
	
	public function setFileName($filename)
	{
		$nameArray = explode(".",$filename);
		if(!empty($nameArray))
		{
			$this->fileName = $nameArray[0];
		}
		else
		{
			$this->fileName = $filename;
		}
	}
	
	public function setPath($path)
	{
		$this->p = $path;
		$this->uploadpath = $this->rootPath.DS.$path;
	}
	
	public function getSize()
	{
		echo $this->newfile["size"];
	}
	
	public function getWidth()
	{
		
	}
	
	public function getHeight()
	{

	}
	
	public function setMaxSize($maxsize)
	{
		if($maxsize < $this->maxsize)
		{
			$this->maxsize = $maxsize;
		}
	}
	
	public function setAllowedExt($fileTypes)
	{
		if(!empty($fileTypes) || $fileTypes !== "")
		{
			$this->allowedExt = $fileTypes;
		}
	}
	
	public function getuploadpath()
	{
		return $this->getuploadPath;
	}
	
	public function resize($width, $height)
	{
		
	}
	
	private function u_errors($base, $message)
	{
		$this->file_errors = "Base Error : ".$base."<br/>"."Message : ".$message;
	}
	
	/*public function p_errors()
	{
		return $this->file_errors;
	}*/
}
?>
