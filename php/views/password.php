<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>File Sharing</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
</head>
<body>
	<?php
		foreach($messages as $message){
			echo $message;
		}
	?>
    <form action="/<?php echo $uid; ?>" method="POST">Password <input name="password" type="text"/><input type="submit"/></form>
</body> 
</html>