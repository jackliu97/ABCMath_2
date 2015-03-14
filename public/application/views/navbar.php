<?php

use ABCMath\Permission\Navigation;

$ci = &get_instance();
$navigation = new Navigation();
$navigation->build($this->User_Model);
$navigation->setCI_URI($this->uri);
$all_semesters = $navigation->get_all_semesters();

$semester_id = 
  $ci->session->userdata('semester_id') ? 
  $ci->session->userdata('semester_id') : 
  $navigation->semester_id;


$ci->session->set_userdata('semester_id', $semester_id);

?>


<div class="row">
<div class="col-lg-12">
<nav class="navbar navbar-default main-navbar" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-1-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/landing">ABC Math Question Bank</a>
    </div>
    <div class="collapse navbar-collapse navbar-1-collapse">
      <form class="navbar-form navbar-left" role="search" id="semester_form">
              <?php
                echo form_dropdown(
                  'semester_id', 
                  $all_semesters, 
                  $semester_id, 
                  'id="semester_id" class="form-control change_semester"');
              ?>
      </form>

      <form class="navbar-form navbar-left" role="search" id="student_name_form">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search Student Name" id="student_name">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>

      <div class="collapse navbar-collapse navbar-1-collapse">
        <ul class="nav navbar-nav navbar-right">
        <?php
          echo $navigation->display_sections();
        ?>
        </ul>
      </div>
    </div>
  </div>
</nav>

</div>
</div>
<div class="row">
<div class="col-md-9 col-md-offset-1 message">
  <ul class="nav nav-pills">
<?php
  echo $navigation->display_subsections();
?>
  </ul>
</div>
</div>

<div class="row">
  <div class="col-md-1">&nbsp;</div>
</div>

<div class="row">
  <div class="col-md-9 col-md-offset-1 message">
    <!-- quick links -->
    <nav class="navbar navbar">
      <div class="container-fluid">

        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#quick-link-navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Quick Link</a>
        </div>

        <div class="collapse navbar-collapse" id="quick-link-navbar-collapse">
          <ul class="nav navbar-nav">
            <?php
              echo $navigation->display_quicklinks();
            ?>
          </ul>
        </div>
      </div>
    </nav>
  </div>
</div>

<div class="row">
<div class="col-md-10 col-md-offset-1 message" id="main_error">
</div>
</div>