<h1>Sign in</h1>
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

<form method="post">
    <input type="hidden" name="<?php echo $csrfName; ?>" value="<?php echo $csrfToken; ?>">
    <div class="data-row">
        <label for="app-login">Eemail</label>
        <input id="app-login" type="text" name="Login[email]" value="<?php echo $model->email; ?>" placeholder="Email" autocomplete="off">
    </div>
    <div class="data-divider"></div>
    <div class="data-row">
        <label for="app-login">Password</label>
        <input id="app-login" type="password" name="Login[password]" placeholder="Password" autocomplete="off">
    </div>
    <button type="submit">Submit</button>
    <a href="/base/signup" class="signup">Sign up</a>
</form>