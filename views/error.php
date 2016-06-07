<?php
switch ($error->getCode()) {
    case 400:
        header("HTTP/1.0 400 Bad Request");
    break;
    default:
        header("HTTP/1.0 404 Not Found");
    break;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<title>Главная</title>
        <link rel="stylesheet" type="text/css" href="<?php echo ASSETS_PATH; ?>/css/style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
    <main>
        <div class="logo"><?php echo $error->getCode() . '. ' . $error->getMessage(); ?></div>
    </main>
    
</body>
</html>