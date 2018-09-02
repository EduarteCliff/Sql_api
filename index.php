<meta charset="utf-8">
<?php include("banip.php");?>
<?php
/*
    #文件名 读/写数据库API 定制版本
	#作者联系方式：QQ207083702
	#CopyRight 2018 CraftAge AllRights Reserved                   
	#使用说明
	#
    #写请求：
	#m=write
	#	n=用户名（TEXT）
	#	l=lvl（INT）
	#	p=point（INT）
	#	i=Image_Url（TEXT）
	#	openid=openid（INT）
	#读取请求：
	#	m=read
	#	openid=openid（INT）
	#
	#验证身份（无论何种模式均必填）：
	#stamp=时间戳
	#sault=随机数
	#sign=stamp + 私钥 + sault 的md5
	#
	#返回格式：JSON
	#
	#请求方式 POST
	#如果要开启IP黑名单，请将 IP黑名单 部分的注释删去
*/
	define('DB_HOST','p:127.0.0.1');	//数据库地址
    define('DB_USER','data');	//数据库用户
    define('DB_PWD','你的数据库密码');	//这里是数据库密码
    define('DB_NAME','data');	//库名
	define('Secret','55ad964a9e98857f96915d5f80980aee'); //定义私钥
/*
	以上内容改为自己的数据库连接信息
*/
/*
	$key=md5(urldecode($_GET["key"]));
	echo "生成的密钥是 " . $key;
	die("");
	//私钥生成示例，可以不按照这种方法来，只是提供一个便利方法，需要生成时去掉代码前后注释，生成完成后记得将注释加回
*/
	$method=$_POST["m"];
	$name=$_POST["n"];
	$lvl=$_POST["l"];
	$point=$_POST["p"];
	$image_url=$_POST["i"];
	$UUID=$_POST["openid"];
	$timestamp=$_POST["stamp"];
	$sault=$_POST["sault"];
	$sign=$_POST["sign"];	//获取传入数据
	$verify=md5($timestamp . Secret . $sault);	//计算签名
	if($verify == $sign){
		$connect = mysqli_connect(DB_HOST,DB_USER,DB_PWD) or die('数据库连接失败，错误信息：'.mysqli_error($connect));	//数据库连接
		mysqli_select_db($connect,DB_NAME) or die('数据库连接错误，错误信息：'.mysqli_error($connect));		//选择库
		if($method == "write"){
			$query = "INSERT INTO CLASS(
				name,
				lvl,
				point,
				image,
				UUID)
			VALUES (
			'" . $name . "',
			" . $lvl . ",
			" . $point . ",
			'" . $image_url . "',
			" . $UUID . "
			)";
			//echo $query;		//显示执行的参数，调试时用
			mysqli_query($connect,$query) or die('新增错误：'.mysqli_error($connect));
			$e_time=microtime(true);
			$total=$e_time-$s_time;
			echo "[Done! Time used:" . $total . "ms]";		//写入数据库
		}
		elseif($method == "read"){
			$query = "SELECT * FROM CLASS WHERE `UUID`='" . $UUID . "'";
			$result = mysqli_query($connect,$query);
			$e_time=microtime(true);
			$total=$e_time-$s_time;
			while($row = mysqli_fetch_assoc($result)) {
				$str = array(
					'name' => $row["name"],
					'point' => $row["point"],
					'lvl' => $row["lvl"],
					'time_used' => $total
				);
				//echo $row["name"] . " " . $row["point"] . " " . $row["lvl"] . " " . $row["image"] . " " . $row["openid"];		//直接输出内容
			}
			echo json_encode($str);		//读取数据库并返回JSON
		}
		else{
			echo "你还想要什么功能???";		//method参数不正确时提示
		}
	}
	else{
		$ip = $_SERVER["REMOTE_ADDR"];
		$scheme = $_SERVER['REQUEST_SCHEME'];
		$domain = $_SERVER['HTTP_HOST']; 
		$requestUri = $_SERVER['REQUEST_URI']; 
		$req = $scheme . "://" . $domain . $requestUri;		//记录访问者IP以及请求内容
		$connect = mysqli_connect(DB_HOST,DB_USER,DB_PWD) or die('数据库连接失败，错误信息：'.mysqli_error($connect));		//数据库连接
		mysqli_select_db($connect,DB_NAME) or die('数据库连接错误，错误信息：'.mysqli_error($connect));		//选择库
		$query = "INSERT INTO IPS(
				IP,
				REQ)
			VALUES (
			'" . $ip . "',
			'" . $req . "'
			)";
		mysqli_query($connect,$query) or die('新增错误：'.mysqli_error($connect));		//写入数据库
      
      	/*IP黑名单 START
        
        $handle = fopen("blacklist", "a");
		if ($handle) {
			fwrite($handle, $ip . "\n");
			fclose($handle);
		}
        
        IP黑名单 END*/
      
		echo "[未经授权的访问,IP已记录]";
		//echo "<br>";
		//echo $_SERVER['HTTP_USER_AGENT'];
	}
?>
