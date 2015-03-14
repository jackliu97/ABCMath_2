( function($, $C) {
    'use strict';

    function reset_student_datatable(){
        $('.datatable_student_container').empty();
        $('.datatable_student_container').html(
            '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-striped" id="students_table">' +
            '<thead><tr>' + 
            '<th>Internal ID</th>' + 
            '<th>External ID</th>' + 
            '<th>Name</th>' + 
            '<th>Email</th>' + 
            '<th>Home Phone</th>' + 
            '<th>Class Name</th>' + 
            '</tr></thead><tbody></tbody></table>');
    }

    function get_students(type){
        $('#students_table').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bLengthChange": false,
            "stateSave": true,
            "sAjaxSource": '/student_dashboard/get_students/' + type,
            "fnDrawCallback": function( oSettings ) {
                $('#students_table_wrapper').find('input').addClass('form-control dataTables_form_override');
                $('#students_table_wrapper').find('select').addClass('form-control dataTables_form_override');
                $('#students_table_wrapper').find('#classes_table_previous').addClass('btn btn-default');
                $('#students_table_wrapper').find('#classes_table_next').addClass('btn btn-default');
            }
        });

        $.extend( $.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        } );
    }

    $( document ).ready(function() {
        $('#student_name').focus();
        $('#all_students').addClass('active');

        reset_student_datatable();
        get_students('registered');
        console.log('registered');


        $('.datatable_student_container').on('click', '.student_detail', function(){
            window.location = 'student_dashboard/info/' + $(this).attr('student_id');
            return false;
        });

        $('#all_students').on('click', function(){
            if($(this).hasClass('active')){
                return false;
            }
            $('.student_panel').find('button').removeClass('active');
            $(this).addClass('active');
            reset_student_datatable();
            get_students('registered');
            return false;
        });

        $('#absent_students').on('click', function(){
            if($(this).hasClass('active')){
                return false;
            }
            $('.student_panel').find('button').removeClass('active');
            $(this).addClass('active');
            reset_student_datatable();
            get_students('Absent');
            return false;
        });

        $('#late_students').on('click', function(){
            if($(this).hasClass('active')){
                return false;
            }
            $('.student_panel').find('button').removeClass('active');
            $(this).addClass('active');
            reset_student_datatable();
            get_students('Tardy');
            return false;
        });

    });
}(jQuery, COMMON) );