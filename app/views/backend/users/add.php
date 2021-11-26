<?php
echo (!empty($msg)) ? $msg : false;
?>
<form method="post" action="<?php echo _WEB_ROOT; ?>/home/postUser">
<div>
<input type="text" name="fullName" placeholder="full name" value="<?php echo old('fullName') ?>"><br/>
<?php echo form_errors('fullName', '<span style="color:red">', '</span>') ?>
</div>
<div>
<input type="email" name="email" placeholder="email" value="<?php echo old('email') ?>"><br/>
<?php echo form_errors('email', '<span style="color:red">', '</span>') ?>
</div>
<div>
<input type="text" name="age" placeholder="age" value="<?php echo old('age') ?>"><br/>
<?php echo form_errors('age', '<span style="color:red">', '</span>') ?>
</div>
<div>
<input type="text" name="yeah" placeholder="yeah" value="<?php echo old('yeah') ?>"><br/>
<?php echo form_errors('yeah', '<span style="color:red">', '</span>') ?>
</div>
<div>
<input type="password" name="password" placeholder="pass"><br/>
<?php echo form_errors('password', '<span style="color:red">', '</span>') ?>
</div>
<div>
<input type="password" name="confirm_password" placeholder="confirm pass"><br/>
<?php echo form_errors('confirm_password', '<span style="color:red">', '</span>') ?>
</div>
<button type="submit"> Submit </button>
</form>
