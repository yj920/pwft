<?php 
require_once 'main.php';
$path="file";
$path=$_REQUEST['path']?$_REQUEST['path']:$path;
$act=$_REQUEST['act'];
$filename=$_REQUEST['filename'];
$dirname=$_REQUEST['dirname'];
$info=readDirectory($path);
if(!$info){
	echo "<script>alert('没有文件或目录！！！');location.href='index.php';</script>";
}
$redirect="index.php?path={$path}";
if($act=="创建文件"){
	$mes=createFile($path."/".$filename);
	alertMes($mes,$redirect);
}elseif($act=="renameFile"){
	$str=<<<EOF
	<form action="index.php?act=doRename" method="post" class="form-inline" role="form"> 
	新文件名：<input class="form-control" type="text" name="newname" placeholder="重命名"/>
	<input type='hidden' name='filename' value='{$filename}' />
	<button type="submit" class="btn btn-default">确认</button>
	</form>
EOF;
echo $str;
}elseif($act=="doRename"){
	$newname=$_REQUEST['newname'];
	$mes=renameFile($filename,$newname);
	alertMes($mes,$redirect);
}elseif($act=="delFile"){
	$mes=delFile($filename);
	alertMes($mes,$redirect);
}elseif($act=="downFile"){
	$mes=downFile($filename);
}elseif($act=="创建文件夹"){
	$mes=createFolder($path."/".$dirname);
	alertMes($mes,$redirect);
}elseif($act=="renameFolder"){
	$str=<<<EOF
	<form action="index.php?act=doRenameFolder" method="post" class="form-inline" role="form"> 
	新文件夹名:<input class="form-control" type="text" name="newname" placeholder="重命名"/>
	<input type="hidden" name="path" value="{$path}" />
	<input type='hidden' name='dirname' value='{$dirname}' />
	<button class="btn btn-default" type="submit">确认</button>
	</form>
EOF;
echo $str;
}elseif($act=="doRenameFolder"){
	$newname=$_REQUEST['newname'];
	$mes=renameFolder($dirname,$path."/".$newname);
	alertMes($mes,$redirect);
}elseif($act=="delFolder"){
	$mes=delFolder($dirname);
	alertMes($mes,$redirect);
}elseif($act=="上传文件"){
	$fileInfo=$_FILES['myFile'];
	$mes=uploadFile($fileInfo,$path);
	alertMes($mes, $redirect);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>文件管理</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>	
	<script type="text/javascript">
	function show(dis){
		document.getElementById(dis).style.display="block";
	}
	function delFile(filename,path){
		if(window.confirm("确认删除")){
				location.href="index.php?act=delFile&filename="+filename+"&path="+path;
		}
	}
	function delFolder(dirname,path){
		if(window.confirm("确认删除")){
			location.href="index.php?act=delFolder&dirname="+dirname+"&path="+path;
		}
	}
	function goBack($back){
		location.href="index.php?path="+$back;
	}
</script>
</head>
<body>
    <nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <a class="navbar-brand" href="#">在线文件管理工具</a>
    </div>
    <div>
        <ul class="nav navbar-nav">
            <li><a href="index.php">返回主目录</a></li>
            <?php 
            $back=($path=="file")?"file":dirname($path);
            ?>
            <li><a href="#" onclick="goBack('<?php echo $back;?>')">返回上级目录</a></li>
            <li><a href="#" onclick="show('createFile')">创建文件</a></li>
            <li><a href="#" onclick="show('createFolder')">创建文件夹</a></li>
            <li><a href="#" onclick="show('uploadFile')">上传文件</a></li>
        </ul>
    </div></nav>

    <form action="index.php" method="post" enctype="multipart/form-data" class="form-inline" role="form">
        <div class="form-group" id="createFolder" style="display:none;">
            <input type="text" name="dirname" class="form-control" id="name" placeholder="请输入文件夹名称">
            <input type="hidden" name="path"  value="<?php echo $path;?>"/>
            <input type="submit" class="btn btn-default"  name="act" value="创建文件夹"/>
        </div>

        <div class="form-group" id="createFile" style="display:none;">
            <input type="text"  name="filename" class="form-control" placeholder="请输入文件名"/>
            <input type="hidden" name="path" value="<?php echo $path;?>"/>
            <input type="submit" class="btn btn-default" name="act" value="创建文件"/>
        </div>

        <div class="form-group" id="uploadFile" style="display:none;">
            <input type="file" name="myFile" id="inputfile">
            <input type="submit" class="btn btn-default" name="act" value="上传文件"/>
        </div>
    </form>

    <table class="table table-hover table-bordered">        
        <caption>文件位置：</caption>
        <thead>
            <tr>
                <th>文件名</th>
                <th>文件类型</th>
                <th>文件大小</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php 
if($info['file']){
	foreach($info['file'] as $val){
	$p=$path."/".$val;
?>
	<tr>
		<td><?php echo $val;?></td>
		<td><?php echo filetype($p)=="file"?"文件":"文件夹";?></td>
		<td><?php echo transByte(filesize($p));?></td>
		<td><?php echo date("Y-m-d H:i:s",filectime($p));?></td>
		<td>		
			<div class="btn-group btn-group-xs">
				<a class="btn btn-info" href="index.php?act=downFile&path=<?php echo $path;?>&filename=<?php echo $p;?>">下载</a>
				<a class="btn btn-warning" href="index.php?act=renameFile&path=<?php echo $path;?>&filename=<?php echo $p;?>">重命名</a>
				<a class="btn btn-danger" href="#"  onclick="delFile('<?php echo $p;?>','<?php echo $path;?>')">删除</a>
			</div>
		</td>		
	</tr>
<?php 

	}
}



?>

<?php 
if($info['dir']){
	foreach($info['dir'] as $val){
		$p=$path."/".$val;
?>
	<tr>
		<td><?php echo $val;?></td>
		<td><?php echo filetype($p)=="file"?"文件":"文件夹";?></td>
		<td><?php  $sum=0; echo transByte(dirSize($p));?></td>
		<td><?php echo date("Y-m-d H:i:s",filectime($p));?></td>
		<td>		
			<div class="btn-group btn-group-xs">
				<a class="btn btn-success" href="index.php?path=<?php echo $p;?>">打开</a>
				<a class="btn btn-warning" href="index.php?act=renameFolder&path=<?php echo $path;?>&dirname=<?php echo $p;?>">重命名</a>
				<a class="btn btn-danger" href="#"  onclick="delFolder('<?php echo $p;?>','<?php echo $path;?>')">删除</a>
			</div>
		</td>		
	</tr>
<?php
	}
}

?>
        </tbody>
    </table>
</body>
</html>