( function($, $C, $M) {
    'use strict';

    function saveOneGrade(grade){
        $.ajax({
            type:'POST',
            url:'/grade_dashboard/save_delta',
            data: {
                'delta': grade,
                'col_mapper': window.grade_col_id_mapper,
                'row_mapper': window.grade_row_id_mapper
            },
            success: function(data){
                if(data.success){
                    console.log('Data successfully updated');
                }else{
                    $C.error(data.message);
                }
            }
        });
    }

    function clear_assignment_data(){
        $('.apply_to_all').show();
        $('#assignment_name').val('');
        $('#assignment_description').val('');
        $('#assignment_type_id').val('');
        $('#assignment_id').val('');
        $('#assignment_lesson_id').val('');
        $('#assignment_weight').val('');
        $('#maximum_score').val('');
        $('#apply_to_all').attr('checked', false);
    }

    function populate_assignment_data(id){
        $.ajax({
            type:'POST',
            url:'/class_dashboard/get_assignment_info',
            data: {
                'assignment_id': id
            },
            success: function(data){
                if(data.success){
                    $('#assignment_id').val(data.info.id);
                    $('#assignment_name').val(data.info.name);
                    $('#assignment_description').val(data.info.description);
                    $('#assignment_type_id').val(data.info.assignment_type_id);
                    $('#assignment_weight').val(data.info.weight);
                    $('#maximum_score').val(data.info.maximum_score);
                    $('#assignment_lesson_id').val(data.info.lesson_id);
                    $('#apply_to_all').attr('checked', false);
                    $('.apply_to_all').hide();
                }else{
                    $C.error(data.message);
                }
            }
        });
    }

    function build_grade_table(){
        //var container = document.getElementById('grade_table');
        var $container = $('#grade_table');
        $container.empty();
        $container.handsontable({
            data: window.grade_row_data,

            colHeaders: function (col){
                return  '<span class="glyphicon glyphicon-remove delete_assignment" aria-hidden="true" assignment_id="' + 
                        window.grade_col_id_mapper[col]['assignment_id'] + '"></span>&nbsp;' + 
                        '<span class="glyphicon glyphicon-edit edit_assignment" aria-hidden="true" assignment_id="' + 
                        window.grade_col_id_mapper[col]['assignment_id'] + '" lesson_id="' + 
                        window.grade_col_id_mapper[col]['lesson_id']
                        + '"></span>&nbsp;' +
                        '<span class="">Lesson ' + window.grade_col_id_mapper[col]['lesson_number'] + '</span></br>' + 
                        window.grade_col_header[col];
            },

            rowHeaders: window.grade_row_header,
            afterChange: function (change, source) {

                if (source === 'loadData') {
                    return;
                }

                setTimeout(function(){
                    $.ajax({
                        type:'POST',
                        url:'/grade_dashboard/save_delta',
                        data: {
                            'delta': change,
                            'col_mapper': window.grade_col_id_mapper,
                            'row_mapper': window.grade_row_id_mapper
                        },
                        success: function(data){
                            if(data.success){
                                console.log('Data successfully updated');
                            }else{
                                $C.error(data.message);
                            }
                        }
                    });
                }, 1000);

            }
        });
        
        $container.on('click', '.delete_assignment', function(){

            if(!confirm('Are you sure you want to delete this assignment?')){
                return false;
            }

            var $this = $(this);
            var assignment_id = $this.attr('assignment_id');
            $.ajax({
                type:'POST',
                url:'/class_dashboard/delete_assignment',
                data: {
                    'assignment_id': $(this).attr('assignment_id')
                },
                success: function(data){
                    if(data.success){
                        $this.closest('table').find('span[assignment_id="' + assignment_id + '"]').each(function(){
                            $(this).closest('td').remove();
                        });

                        $this.closest('table').find('input[assignment_id="' + assignment_id + '"]').each(function(){
                            $(this).closest('td').remove();
                        });

                        $this.closest('th').remove();
                    }else{
                        $C.error(data.message, $('#assignment_error'));
                        return false;
                    }
                }
            });

        });

        $container.find('.htCore').addClass('table-striped').addClass('table-hover');

    }

    $( document ).ready(function() {

        $('.class_dropdown').on('change', function(){
            window.location.href='/grade_dashboard/grade/' + $(this).val();
            return false;
        });

        build_grade_table();

        var $body = $('body');

        $body.on('click', '.edit_assignment, .add_assignment', function(){

            var assignment_id = $(this).attr('assignment_id');
            if(assignment_id){
                populate_assignment_data(assignment_id);
            }else{
                clear_assignment_data();
            }

            $('#assignment_modal').modal('show');
        });


        $('#assignment_form').on('submit', function(){
            $.ajax({
                type:'POST',
                url:'/class_dashboard/save_assignment',
                data: {
                    'id': $('#assignment_id').val(),
                    'name': $('#assignment_name').val(),
                    'description': $('#assignment_description').val(),
                    'assignment_type_id': $('#assignment_type_id').val(),
                    'maximum_score': $('#maximum_score').val(),
                    'weight': $('#assignment_weight').val(),
                    'lesson_id': $('#assignment_lesson_id').val(),
                    'apply_to_all': $('#apply_to_all').is(':checked') ? '1' : '0'
                },
                success: function(data){
                    if(data.success){
                        location.reload();
                        //$('#assignment_modal').modal('hide');
                    }else{
                        $C.error(data.message, $('#assignment_error'));
                        return false;
                    }
                }
            });
            return false;

        });

        return;

    });

}(jQuery, COMMON, Mousetrap) );