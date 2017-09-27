
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<h1>Ошибка</h1>
	<p>Произошла ошибка: <?php isset($templateData['error_connection']) ? print($templateData['error_connection']) : print(""); ?></p>
</body>
</html>