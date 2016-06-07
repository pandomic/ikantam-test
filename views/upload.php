<h1>Upload file</h1>
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
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="<?php echo $csrfName; ?>" value="<?php echo $csrfToken; ?>">
    <div class="data-row">
        <label for="app-title">File title</label>
        <input id="app-title" type="text" name="Upload[title]" value="<?php echo $model->title; ?>" placeholder="File title" autocomplete="off">
    </div>
    <div class="data-divider"></div>
    <div class="data-row">
        <label class="fake-loader" for="app-upload"><span>Select file to upload</span></label>
        <input id="app-upload" type="file" name="Upload[file]" class="fake-loader">
    </div>
    <button type="submit">Submit</button>
</form>