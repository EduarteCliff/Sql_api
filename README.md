# Sql_api
访问操作mysql的api
使用说明

写请求：
  m=write
	n=用户名（TEXT）
	l=lvl（INT）
	p=point（INT）
	i=Image_Url（TEXT）
	openid=openid（INT）
读取请求：
	m=read
	openid=openid（INT）

验证身份（无论何种模式均必填）：
  stamp=时间戳
  sault=随机数
  sign=stamp + 私钥 + sault 的md5

返回格式：JSON

请求方式 POST
如果要开启IP黑名单，请将 IP黑名单 部分的注释删去
