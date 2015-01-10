<?php

use ABCMath\Permission\Navigation;

$ci = &get_instance();
$navigation = new Navigation();
$navigation->build($this->User_Model);
$navigation->setCI_URI($this->uri);
$all_semesters = $navigation->get_all_semesters();
$semester_id = $ci->session->userdata('semester_id');
$ci->session->set_userdata('semester_id', $semester_id);

?>


<div class="row">
<div class="col-lg-12">
<nav class="navbar navbar-default main-navbar" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="/landing">ABC Math Question Bank</a>
  </div>

  <form class="navbar-form navbar-left" role="search" id="semester_form">
    <div class="form-group">
     <div class="form-group">
          <?php
            echo form_dropdown(
              'semester_id', 
              $all_semesters, 
              $semester_id, 
              'id="semester_id" class="form-control change_semester"');
          ?>
        </div>
    </div>
  </form>

  <form class="navbar-form navbar-left" role="search" id="student_name_form">
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Search Student Name" id="student_name">
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
  </form>

  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav navbar-right">
    <?php
      echo $navigation->display_sections();
    ?>
    </ul>
  </div>
</nav>

</div>
</div>
<div class="row">
<div class="col-md-1 message">&nbsp;</div>
<div class="col-md-9 message">
  <ul class="nav nav-pills">
<?php
  echo $navigation->display_subsections();
?>
  </ul>
</div>
</div>

<div class="row">
<div class="col-md-1 message">&nbsp;</div>
<div class="col-md-10 message" id="main_error">
</div>
</div>