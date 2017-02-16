<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>File Sharing</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="css/semantic.min.css">
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
</head>
<body>
	<?php
		if(isset($messages)){
			foreach($messages as $message){
				echo $message;
			}
		}
	?>
    <form action="/<?php echo $uid; ?>" method="POST">
    	Password <input name="password" type="text"/><input class="ui button" type="submit"/>
	</form>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/semantic.min.js"></script>
</body> 
</html>