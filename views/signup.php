<h1>Sign up</h1>
<?php
$error = $request->getFlash('error');
list($csrfName, $csrfToken) = $request->getCSRF();
if ($error !== FALSE):
?>
<div class="error">
    <?php echo $error; ?>
</div>
<?php endif; ?>
<form method="post">
    <input type="hidden" name="<?php echo $csrfName; ?>" value="<?php echo $csrfToken; ?>">
    <div class="data-row">
        <label for="app-login">Your email</label>
        <input id="app-login" type="text" name="Register[email]" value="<?php echo $model->email; ?>" placeholder="Email" autocomplete="off">
    </div>
    <div class="data-divider"></div>
    <div class="data-row">
        <label for="app-password">Password</label>
        <input id="app-password" type="password" name="Register[password]" placeholder="Password" autocomplete="off">
    </div>
    <div class="data-divider"></div>
    <div class="data-row">
        <label for="app-repeat">Repeat</label>
        <input id="app-repeat" type="password" name="Register[repeat]" placeholder="Repeat password" autocomplete="off">
    </div>
    <button type="submit">Submit</button>
</form>