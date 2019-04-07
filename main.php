<?php 

// 提示操作信息的，并且跳转

function alertMes($mes,$url){
	echo "<script type='text/javascript'>alert('{$mes}');location.href='{$url}';</script>";
}

// 产生唯一名称
function getUniqidName($length=10){
	return substr(md5(uniqid(microtime(true),true)),0,$length);
}


function transByte($size) {
	$arr = array ("B", "KB", "MB", "GB", "TB", "EB" );
	$i = 0;
	while ( $size >= 1024 ) {
		$size /= 1024;
		$i ++;
	}
	return round ( $size, 2 ) . $arr [$i];
}


function renameFile($oldname,$newname){
	if(checkFilename($newname)){
		$path=dirname($oldname);
		if(!file_exists($path."/".$newname)){
			if(rename($oldname,$path."/".$newname)){
				return "重命名成功";
			}else{
				return "重命名失败";
			}
		}else{
			return "存在同名文件，请重新命名";
		}
	}else{
		return "非法文件名";
	}
	
}


function checkFilename($filename){
	$pattern = "/[\/,\*,<>,\?\|]/";
	if (preg_match ( $pattern,  $filename )) {
		return false;
	}else{
		return true;
	}
}


function delFile($filename){
	if(unlink($filename)){
		$mes="文件删除成功";
	}else{
		$mes="文件删除失败";
	}
	return $mes;
}


function downFile($filename){
	header("content-disposition:attachment;filename=".basename($filename));
	header("content-length:".filesize($filename));
	readfile($filename);
}
}


function uploadFile($fileInfo,$path,$allowExt=array("gif","jpeg","jpg","png","txt"),$maxSize=10485760){
	if($fileInfo['error']==UPLOAD_ERR_OK){
		//文件是否是通过HTTP POST方式上传上来的
		if(is_uploaded_file($fileInfo['tmp_name'])){
			//上传文件的文件名，只允许上传jpeg|jpg、png、gif、txt的文件
			//$allowExt=array("gif","jpeg","jpg","png","txt");
			$ext=getExt($fileInfo['name']);
			$uniqid=getUniqidName();
			$destination=$path."/".pathinfo($fileInfo['name'],PATHINFO_FILENAME)."_".$uniqid.".".$ext;
			if(in_array($ext,$allowExt)){
				if($fileInfo['size']<=$maxSize){
					if(move_uploaded_file($fileInfo['tmp_name'], $destination)){
						$mes="文件上传成功";
					}else{
						$mes="文件移动失败";
					}
				}else{
					$mes="文件过大";
				}
			}else{
				$mes="非法文件类型";
			}
		}else{
			$mes="文件不是通过HTTP POST方式上传上来的";
		}
	}else{
		switch($fileInfo['error']){
			case 1:
				$mes="超过了配置文件的大小";
				break;
			case 2:
				$mes="超过了表单允许接收数据的大小";
				break;
			case 3:
				$mes="文件部分被上传";
				break;
			case 4:
				$mes="没有文件被上传";
				break;
		}
	}
	
	return $mes;
	
}


function readDirectory($path) {
	$handle = opendir ( $path );
	while ( ($item = readdir ( $handle )) !== false ) {
		//.和..这2个特殊目录
		if ($item != "." && $item != "..") {
			if (is_file ( $path . "/" . $item )) {
				$arr ['file'] [] = $item;
			}
			if (is_dir ( $path . "/" . $item )) {
				$arr ['dir'] [] = $item;
			}
		
		}
	}
	closedir ( $handle );
	return $arr;
}

function dirSize($path){
	$sum=0;
	global $sum;
	$handle=opendir($path);
	while(($item=readdir($handle))!==false){
		if($item!="."&&$item!=".."){
			if(is_file($path."/".$item)){
				$sum+=filesize($path."/".$item);
			}
			if(is_dir($path."/".$item)){
				$func=__FUNCTION__;
				$func($path."/".$item);
			}
		}
		
	}
	closedir($handle);
	return $sum;
}

function createFolder($dirname){
	if(checkFilename(basename($dirname))){
		if(!file_exists($dirname)){
			if(mkdir($dirname,0777,true)){
				$mes="文件夹创建成功";
			}else{
				$mes="文件夹创建失败";
			}
		}else{
			$mes="存在相同文件夹名称";
		}
	}else{
		$mes="非法文件夹名称";
	}
	return $mes;
}


function renameFolder($oldname,$newname){
	if(checkFilename(basename($newname))){
		if(!file_exists($newname)){
			if(rename($oldname,$newname)){
				$mes="重命名成功";
			}else{
				$mes="重命名失败";
			}
		}else{
			$mes="存在同名文件夹";
		}
	}else{
		$mes="非法文件夹名称";
	}
	return $mes;
}




function delFolder($path){
	$handle=opendir($path);
	while(($item=readdir($handle))!==false){
		if($item!="."&&$item!=".."){
			if(is_file($path."/".$item)){
				unlink($path."/".$item);
			}
			if(is_dir($path."/".$item)){
				$func=__FUNCTION__;
				$func($path."/".$item);
			}
		}
	}
	closedir($handle);
	rmdir($path);
	return "文件夹删除成功";
}