<?php
include 'config.php';
include 'function.php';
if(isset($_REQUEST['userid']))
{
    //$firstname  =	$_REQUEST['firstname'];
    //$lastname	=	$_REQUEST['lastname'];
    $userid     =   $_REQUEST['userid'];
    if ($_FILES['image_file']['tmp_name']!= "") 
    {
    	$validation_type = 2;
		if($validation_type == 1)
		{
			$mime = array('image/gif' => 'gif',
						  'image/jpeg' => 'jpeg',
						  'image/png' => 'png',
						  'application/x-shockwave-flash' => 'swf',
						  'image/psd' => 'psd',
						  'image/bmp' => 'bmp',
						  'image/tiff' => 'tiff',
						  'image/tiff' => 'tiff',
						  'image/jp2' => 'jp2',
						  'image/iff' => 'iff',
						  'image/vnd.wap.wbmp' => 'bmp',
						  'image/xbm' => 'xbm',
						  'image/vnd.microsoft.icon' => 'ico');
		}
		else if($validation_type == 2) // Second choice? Set the extensions
		{
			$image_extensions_allowed = array('jpg', 'jpeg', 'png', 'gif','bmp');
		}
    
		$upload_image_to_folder = '../admin/upload/users/';
		$file = $_FILES['image_file'];
		
		$file_name = $file['name'];
		
		$error = ''; // Empty
    
    	// Get File Extension (if any)
    
    	$ext = strtolower(substr(strrchr($file_name, "."), 1));
    
    	// Check for a correct extension. The image file hasn't an extension? Add one
    
       if($validation_type == 1)
       {
    	 $file_info = getimagesize($_FILES['image_file']['tmp_name']);
    	 
    
    	  if(empty($file_info)) // No Image?
    	  {
    	  //$error .= "The uploaded file doesn't seem to be an image.";
          $content=array("response"=>ERROR,"status"=>"0","message"=>"The uploaded file doesn't seem to be an image.");
          echo json_encode($content);
          exit;
    	  }
    	  else // An Image?
    	  {
    	  $file_mime = $file_info['mime'];
    
    		 if($ext == 'jpc' || $ext == 'jpx' || $ext == 'jb2')
    		 {
    		 $extension = $ext;
    		 }
    		 else
    		 {
    		 $extension = ($mime[$file_mime] == 'jpeg') ? 'jpg' : $mime[$file_mime];
    		 }
    
    		 if(!$extension)
    		 {
    		 $extension = '';  
    		 $file_name = str_replace('.', '', $file_name); 
    		 }
    	  }
       }
       else if($validation_type == 2)
       {
    	  if(!in_array($ext, $image_extensions_allowed))
    	  {
    	  //$exts = implode(', ',$image_extensions_allowed);
    	  //$error .= "You must upload a file with one of the following extensions: ".$exts;
          $content=array("response"=>ERROR,"status"=>"0","message"=>"You must upload a file with one of the following extensions: ".$exts);
            echo json_encode($content);
            exit;
    	  }
    
    	  $extension = $ext;
       }
       if($error == "") // No errors were found?
       {
       
       	/*$file_sizebytes = filesize($_FILES['image_file']['tmp_name']);
      	 $file_sizekb=round($file_sizebytes/1024);
    	if($file_sizekb>1024) 
    	{ 
    	//$error .= "You must upload a image of maximum of 1MB. ";
        $content=array("response"=>ERROR,"message"=>"You must upload a image of maximum of 1MB. ");
            echo json_encode($content);
            exit;	
    	}*/			
    	
    	 //11.27 kb=12001 byte
       $new_file_name = strtolower($file_name);
       $new_file_name = str_replace(' ', '-', $new_file_name);
       $new_file_name = substr($new_file_name, 0, -strlen($ext));
       $new_file_name .= $extension;
       
       // File Name
       $move_file = move_uploaded_file($file['tmp_name'], $upload_image_to_folder.$new_file_name);
    
    	 if($move_file)
    	  {
    	  $done = 'The image has been uploaded.';
    	  }
       }
       else
       {
       @unlink($file['tmp_name']);
       }
    
       $file_uploaded = true;
    }
    else
    {
    $new_file_name=$_REQUEST['logo'];
    }
    
    		
    $sql ="update tbl_registeration set logo='".$new_file_name."' where id=".$userid; 
    $rec=mysql_query($sql);
    if($rec){
        $content=array("response"=>SUCCESS,"status"=>"1","message"=>SUCCESS);
        echo json_encode($content);
        exit;
    }
    else{
        $content=array("response"=>ERROR,"status"=>"0","message"=>ERROR);
        echo json_encode($content);
        exit;
    }

}
else{
    $content=array("response"=>ERROR,"status"=>"0","message"=>PARAMETER_MSG);
    echo json_encode($content);
    exit;
}
//img_20140817_185551050_hdr~2.jpg
?>