<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:xim="http://www.ximdex.com/ximdex">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{$title}</title>

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />

	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<link rel="icon" href="{$_URL_ROOT}/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="{$_URL_ROOT}/favicon.ico" type="image/x-icon" />
	<link href='{$_URL_ROOT}/xmd/style/fonts.css' rel='stylesheet' type='text/css' />

	<link href="{$_URL_ROOT}/xmd/style/login/login.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="{EXTENSIONS::Jquery()}"></script>
	{foreach from=$js_files item=js_file}
	<script type="text/javascript" src="{$js_file}"></script>
	{/foreach}

	{literal}
	<script type="text/javascript" language="javascript">
		$(document).ready(function() {
			$('input#user').focus();
		});
	</script>
	{/literal}
</head>

<body>
	{* IMPORTANT *}
	<xim:meta name="X-XIMDEX" content="401 Unauthorized"></xim:meta>

	<div id="contenedor">
		<h1><a href="http://www.ximdex.com" title="Access"><img src="{$_URL_ROOT}/xmd/images/login/logo_ximdex.png" alt="Ximdex logo" title="Visit our web" /></a></h1>

		<div id="acceso" class="triangle">
			<form action="{$_URL_ROOT}/xmd/loadaction.php?action=login&amp;method=check" method="post" name="access">
				<div class="error">{$message}</div>
				<div class="col-md-12">
				<div class="form-group">
					<label for="user"><img src="{$_URL_ROOT}/xmd/images/login/access.png" width="15px"/> {t}User{/t}</label>
					<input type="text" class="form-control" name="user" id="user" />
				</div>
				</div>
				<div class="col-md-12">
				<div class="form-group">
					<label for="password"><img src="{$_URL_ROOT}/xmd/images/login/pass.png" width="15px"/> {t}Password{/t}</label>
					<input type="password" class="form-control" name="password" id="password" onkeypress="capLock(event)" />
					<span id="capsLockAdvice" class="warning-msg">CapsLock enabled</span>
				</div>
				</div>


				<div class="col-md-12 text-center">
					<button type="submit" name="login" id="login" value="{t}Sign in{/t}" class="btn btn-default">Sign in</button>
				</div>
				<div class="col-md-12">
					<div class="alert alert-info">
						<strong>Info!</strong> {t}Recommended browsers{/t}:<br/> Firefox &gt; 4, Chrome, Opera and Safari.
					</div>
				</div>
				<!--
				<p style="text-align: center;">
					<button type="submit" name="login" id="login" value="{t}Sign in{/t}" >{t}Sign in{/t}</button>
					<span>{t}Recommended browsers{/t}:<br/> Firefox &gt; 4, Chrome, Opera and Safari.</span> {* <a href="http://lab04.ximdex.net/ximdexDEMO/xmd/loadaction.php?action=forgot">Forgot your password?</a> *}
				</p>
				-->
			</form>
		</div>

		<div id="mas_info" class="triangle">
			<h2 class="comunidad"><img src="{$_URL_ROOT}/xmd/images/login/join.png" width="30px"/> {t}Join our community{/t}</h2>

			{*<p>Join our <a href="#">community</a>, consult your doubts, contribute with your suggestions. </p>*}

			<p>
				{t}Visit{/t} <a href="http://www.ximdex.com" target="_blank">{t}our website{/t}</a> {t}to learn more about the advantages of managing your projects with{/t} <strong>Ximdex</strong>.
			</p>

			<h2 class="siguenos"><img src="{$_URL_ROOT}/xmd/images/login/follow.png" width="25px"/> {t}Follow us{/t}</h2>


			<a href="http://twitter.com/ximdex" target="_blank" title="{t}Visit Ximdex on Twitter{/t}" class="twit"><div class="text">Twitter</div><div class="text2">Ximdex</div></a>



				<a href="http://www.facebook.com/Ximdex" target="_blank" title="{t}Visit Ximdex on Facebook{/t}" class="face"><div class="text">Facebook</div><div class="text2">Ximdex</div></a>



				<a href="http://www.linkedin.com/companies/ximdex" target="_blank" title="{t}Visit Ximdex on LinkedIn{/t}" class="link"><div class="text">LinkedIn</div><div class="text2">Ximdex</div></a>

		</div>

		<div id="news" class="news">
			<div class="alert alert-info">
				<strong>Info!</strong> {$news_content}
			</div>
		</div>
	</div>
</body>
</html>