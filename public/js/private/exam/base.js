( function($, $C) {
    'use strict';

    $( document ).ready(function() {

        $('#reading_comprehension').submit(function(){
            var id_list = $('#reading_comprehension_id_list').val().split(',');
            var format = $('#build_format').val();


            console.log('/create_exam/reading_comprehension/' + id_list.join('_') + '/' + format);

            window.open('/create_exam/reading_comprehension/' + id_list.join('_') + '/' + format, '_blank');
            
            

            /*var submit_data = {
                id_list: $('#reading_comprehension_id_list').val(),
                format: $('#build_format').val()
            }

            $.ajax({
                    type:'POST',
                    url:'/create_exam/reading_comprehension',
                    data: submit_data,
                    success: function(data){

                        if(data.success){
                            console.log(data);
                            $C.success('Test successfully built');
                        }else{
                            $C.error(data.message);
                        }
                    }
                });
*/

            return false;
        });


    });
}(jQuery, COMMON) );
