( function($, $C, $M) {
    'use strict';

    $( document ).ready(function() {

        $('.class_dropdown').on('change', function(){
            window.location.href='/grade_dashboard/grade/' + $(this).val();
            return false;
        });

        //var container = document.getElementById('grade_table');
        var $container = $('#grade_table');
        var $save = $('.save');
        $container.handsontable({
            data: window.grade_row_data,

            colHeaders: function (col){
                return  '<span class="glyphicon glyphicon-remove delete_assignment" aria-hidden="true" assignment_id="' + 
                        window.grade_col_id_mapper[col]['assignment_id'] + '"></span>&nbsp;' + 
                        '<span class="">Lesson ' + window.grade_col_id_mapper[col]['lesson_number'] + '</span></br>' + 
                        window.grade_col_header[col];
            },

            rowHeaders: window.grade_row_header,
            afterChange: function (change, source) {

                if (source === 'loadData') {
                    return;
                }

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

        return;

    });

}(jQuery, COMMON, Mousetrap) );