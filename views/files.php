<h1>Files list</h1>
<?php
$error = $request->getFlash('error');
$info = $request->getFlash('info');
list($csrfName, $csrfToken) = $request->getCSRF();
if ($error !== FALSE):
?>
<div class="error">
    <?php echo $error; ?>
</div>
<?php
endif;
if ($info !== FALSE):
?>
<div class="info">
    <?php echo $info; ?>
</div>
<?php endif; ?>
<?php if (count($models) == 0): ?>
<div class="info">
    There is no currently any files
</div>
<?php
else:
    foreach ($models as $model):   
    $extension = explode('.', $model->location);
    $extension = array_pop($extension);
?>
    <div class="file">
        
        <?php if($userid == $model->user_id): ?><a class="remove" href="/base/remove?id=<?php echo $model->id; ?>">
            <img src="<?php echo ASSETS_PATH . '/icons/remove.png' ?>">
        </a><?php endif; ?>
        <?php if (in_array($extension, array('zip'))): ?>
            <img src="<?php echo ASSETS_PATH . '/icons/zip.png' ?>">
        <?php endif; ?>
        <?php if (in_array($extension, array('jpg', 'jpeg', 'png'))): ?>
            <img src="<?php echo ASSETS_PATH . '/icons/image.png' ?>">
        <?php endif; ?>
        
        <a href="/base/download?l=<?php echo $model->token; ?>" target="__blank"><?php echo $model->title; ?></a>
        
        
    
    </div>
<?php
    endforeach;
endif;
?>
<a class="signup" href="/base/upload">Upload your file</a>