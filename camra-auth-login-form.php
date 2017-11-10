<style type="text/css">
.form-group, .message, .btn-group {
    margin-bottom: 20px;
}
.btn-group {
    margin-top: 40px;
}
.message {
    color: #bf5329;
}
</style>
<form method="post">
    <div class="form-group <?php if (!empty($_POST)): ?>has-error<?php endif; ?>">
        <label class="control-label" for="camra_auth_memno">
            Membership number
        </label>
        <input class="form-control" id="camra_auth_memno" name="camra_auth_memno" placeholder="Membership number" type="text" value="<?php echo form_val($camra_auth_member->memno()) ?>">
    </div>
    <div class="form-group <?php if (!empty($_POST)): ?>has-error<?php endif; ?>">
        <label class="control-label" for="camra_auth_pass">
            Password
        </label>
        <input class="form-control" id="camra_auth_pass" name="camra_auth_pass" placeholder="Password" type="password">
    </div>
    <?php if (!empty($_POST)): ?>
    <div class="message">
        <span class="help-block">
            Authentication has failed. Please try again.
        </span>
    </div>
    <?php endif; ?>
    <p><a href="https://password.camra.org.uk/">Forgotten password?</a></p>
    <div class="btn-group">
        <input type="hidden" name="login_form" value="camra_auth">
        <input name="submit" type="submit" id="camra_auth_submit" class="submit" value="Sign in">
    </div>
</form>