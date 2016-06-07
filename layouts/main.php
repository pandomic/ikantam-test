<!DOCTYPE html>
<html lang="ru">
<head>
	<title>Главная</title>
        <link rel="stylesheet" type="text/css" href="<?php echo ASSETS_PATH; ?>/css/style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
    <?php
    list($controller, $action) = $application->getRoute()->getCurrentRoute();
    $route = $controller . '/' . $action;
    ?>
    <header>
        <?php
        if ($application->getAcc()->getAccess()):
        ?>
            <ul>
                <li class="<?php echo $route == 'base/index' ? 'active' : '' ?>"><a href="/">Home</a>
                <li class="<?php echo $route == 'base/upload' ? 'active' : '' ?>"><a href="/base/upload">Upload file</a>
                <li><a href="/base/logout">Log out</a>
            </ul>
        <?php
        else:
        ?>
            <ul>
                <li class="<?php echo $route == 'base/index' ? 'active' : '' ?>"><a href="/">Home</a>
                <li class="<?php echo $route == 'base/signup' ? 'active' : '' ?>"><a href="/base/signup">Sign up</a>
            </ul>
        <?php
        endif;
        ?>
    </header>
    <main>
        <div class="logo">#Test @pplication</div>
        <?php echo $content; ?>
    </main>
    
</body>
</html>