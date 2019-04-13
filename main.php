<?php 

// 提示操作信息并跳转
function alertMes($mes,$url){
	echo "<script type='text/javascript'>alert('{$mes}');location.href='{$url}';</script>";
}

// 获取文件拓展名
function getExt($filename){
	return strtolower(pathinfo($filename,PATHINFO_EXTENSION));
}

function createFile($filename) {
	$pattern = "/[\/,\*,<>,\?\|]/";
	if (! preg_match ( $pattern, basename ( $filename ) )) {
		if (! file_exists ( $filename )) {
			if (touch ( $filename )) {
				return "文件创建成功";
			} else {
				return "文件创建失败";
			}
		} else {
			return "文件已存在，请重命名后创建";
		}
	} else {
		return "非法文件名";
	}
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


function uploadFile($fileInfo,$path,$maxSize=10485760){
	if($fileInfo['error']==UPLOAD_ERR_OK){
		if(is_uploaded_file($fileInfo['tmp_name'])){
			$ext=getExt($fileInfo['name']);
			$destination=$path."/".pathinfo($fileInfo['name'],PATHINFO_FILENAME).$_FILES['upFile'].".".$ext;		
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