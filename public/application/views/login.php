<div class="row">
<div class="col-md-1"><h1>&nbsp;</h1></div>
</div>

<div class="row">
<div class="col-md-4">&nbsp;</div>

<div class="col-md-4">
<form class="form-horizontal" role="form" id="login_form">
  <div class="form-group">
    <label for="username" class="col-lg-2 control-label">Email</label>
    <div class="col-lg-10">
      <input type="text" class="form-control" id="email" name="email" placeholder="Email">
    </div>
  </div>
  <div class="form-group">
    <label for="password" class="col-lg-2 control-label">Password</label>
    <div class="col-lg-10">
      <input type="password" class="form-control" id="password" name="password" placeholder="Password">
      <input type="hidden" class="form-control" id="redirect" name="redirect" value="<?php echo $redirect;?>">
    </div>
  </div>

  <div class="checkbox">

    <label for="remember">Remember Me</label>
    <?php
      $data = array(
          'name' => 'remember',
          'id' => 'remember',
          'value' => '1',
          'checked' => '',
      );
      echo form_checkbox($data);
    ?>

  </div>

  <div class="form-group">&nbsp;</div>


  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <button type="submit" class="btn btn-default">Sign in</button>
      <a href="/register" target="_blank" class="btn btn-default">Registration Form</a>
    </div>
  </div>
</form>
</div>

<div class="col-md-4">&nbsp;</div>
</div>