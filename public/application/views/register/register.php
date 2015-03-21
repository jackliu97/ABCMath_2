<form role="form" id="register_new_student">

<div class="row">
    <div class="col-sm-3 col-sm-offset-1">
        <label>Student Name</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name">
        </div>
    </div>

    <div class="col-sm-3">
        <label>&nbsp;</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Middle Name">
        </div>
    </div>

    <div class="col-sm-3">
        <label>&nbsp;</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-3 col-sm-offset-1">
        <label>Birthday - Month</label>
        <div class="form-group input-group-lg">
            <?php
                echo form_dropdown(
                    'dob_month',
                    month_options(),
                    '',
                    'id="dob_month" class="form-control"');
            ?>
        </div>
    </div>

    <div class="col-sm-2">
        <label>Day</label>
        <div class="form-group input-group-lg">
            <?php
                echo form_dropdown(
                    'dob_day',
                    day_options(),
                    '',
                    'id="dob_day" class="form-control"');
            ?>
        </div>
    </div>

    <div class="col-sm-2">
        <label>Year</label>
        <div class="form-group input-group-lg">
            <?php
                echo form_dropdown(
                    'dob_year',
                    year_dob_options(),
                    '',
                    'id="dob_year" class="form-control"');
            ?>
        </div>
    </div>

    <div class="col-sm-2">
        <label>Gender</label>
        <div class="form-group input-group-lg">
            <?php
                $options = array(

                        ''=>'Pick a gender',
                        'male'=>'Male',
                        'female'=>'Female',

                    );
                echo form_dropdown(
                    'gender',
                    $options,
                    '',
                    'id="gender" class="form-control"');
            ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-9 col-sm-offset-1">
        <label>Address</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="address1" name="address1" placeholder="43-55 Kissena Blvd">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-9 col-sm-offset-1">
        <label>Address 2</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="address2" name="address2" placeholder="Apartment 2C">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-3 col-sm-offset-1">
        <label>City</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="city" name="city" placeholder="Flushing">
        </div>
    </div>

    <div class="col-sm-3">
        <label>State</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="state" name="state" placeholder="NY">
        </div>
    </div>

    <div class="col-sm-3">
        <label>Zip Code</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="zip" name="zip" placeholder="11355">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-3 col-sm-offset-1">
        <label>Home Phone</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="telephone" name="telephone" placeholder="718-888-7866">
        </div>
    </div>

    <div class="col-sm-3">
        <label>Work Phone</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="telephone2" name="telephone2" placeholder="718-888-7866">
        </div>
    </div>

    <div class="col-sm-3">
        <label>Cell Phone</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="cellphone" name="cellphone" placeholder="718-888-7866">
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-9 col-sm-offset-1">
        <label>Parent's Email</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="email" name="email" placeholder="parent@email.com">
        </div>
    </div>

</div>

<div class="row">
    <div class="col-sm-9 col-sm-offset-1">
        <label>Student's Email</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="email2" name="email2" placeholder="student@email.com">
        </div>
    </div>

</div>

<div class="row">
    <div class="col-sm-5 col-sm-offset-1">
        <label>Current School</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="school" name="school" placeholder="Stuyversant">
        </div>
    </div>

    <div class="col-sm-2">
        <label>GPA</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="gpa" name="gpa" placeholder="3.5">
        </div>
    </div>

    <div class="col-sm-2">
        <label>Grade (Year)</label>
        <div class="form-group input-group-lg">
            <?php
                $options = array(

                        ''=>'Pick a grade',
                        '3'=>'3rd',
                        '4'=>'4th',
                        '5'=>'5th',
                        '6'=>'6th',
                        '7'=>'7th',
                        '8'=>'8th',
                        '9'=>'9th',
                        '10'=>'10th',
                        '11'=>'11th',
                        '12'=>'12th',

                    );
                echo form_dropdown(
                    'grade',
                    $options,
                    '',
                    'id="grade" class="form-control"');
            ?>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-sm-9 col-sm-offset-1">
        <label>Courses requested</label>
        <div class="form-group input-group-lg">
            <?php
            echo form_dropdown(
                    'first_class',
                    $all_courses,
                    '',
                    'id="first_class" class="form-control"');
            ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-9 col-sm-offset-1">
        <label>Courses requested</label>
        <div class="form-group input-group-lg">
            <?php
            echo form_dropdown(
                    'second_class',
                    $all_courses,
                    '',
                    'id="second_class" class="form-control"');
            ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-9 col-sm-offset-1">
        <label>Courses requested</label>
        <div class="form-group input-group-lg">
            <?php
            echo form_dropdown(
                    'third_class',
                    $all_courses,
                    '',
                    'id="third_class" class="form-control"');
            ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-9 col-sm-offset-1">
        <label>Transportation</label>
        <div class="form-group input-group-lg">
            <?php
                $options = array(

                        ''=>'Select a transportation mode',
                        'parent'=>'Pickup by parent',
                        'alone'=>'Going home alone',

                    );
                echo form_dropdown(
                    'pickup_method',
                    $options,
                    '',
                    'id="pickup_method" class="form-control"');
            ?>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-sm-9 col-sm-offset-1">
        <label>Other forms of transportation</label>
        <div class="form-group input-group-lg">
            <input autocomplete="off" type="text" class="form-control" id="other_pickup_method" name="other_pickup_method" placeholder="Taking a taxi... etc">
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-6 col-sm-offset-1">
        <button class="btn btn-default btn-lg" type="submit">Submit</button>
    </div>
</div>


</form>