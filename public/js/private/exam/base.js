( function($, $C) {
    'use strict';

    $( document ).ready(function() {

        $('#reading_comprehension').submit(function(){
            var id_list = $('#reading_comprehension_id_list').val().split(',');
            var format = $('#build_format').val();

            //window.open('/create_exam/reading_comprehension/' + id_list.join('_') + '/' + format, '_blank');
            
            

            var submit_data = {
                id_list: $('#reading_comprehension_id_list').val(),
                format: $('#build_format').val()
            }

            var popupWindow = window.open();

            $.ajax({
                    type:'POST',
                    url:'/create_exam/reading_comprehension',
                    data: submit_data,
                    success: function(data){
                        console.log(data);
                        if(data.success){
                            $C.success('Test successfully built. File ID: ' + data.file_id );
                            popupWindow.location.href = '/file/download/' + data.file_id;

                        }else{
                            $C.error(data.message);
                        }
                    }
                });


            return false;
        });


    });
}(jQuery, COMMON) );
