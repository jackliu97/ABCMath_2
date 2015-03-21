( function($, $C) {
    'use strict';

    function clear_fields(){
        $('input').each(function(index){
            $(this).val('');
        });

         $('select').each(function(index){
            $(this).val('');
        });
    }

    function mark_required(missing){
        //unmark everything first.
        $('input').each(function(index){
            $(this).closest('.form-group').removeClass('has-error');
        });

        //unmark everything first.
        $('select').each(function(index){
            $(this).closest('.form-group').removeClass('has-error');
        });

        //mark all the bad ones.
        for(var i=0, l=missing.length; i<l; i++){
            $('#' + missing[i]).closest('.form-group').addClass('has-error');
        }
    }

    $(document).ready(function() {

        $('#register_new_student').submit(function(){
            var submit_data = $( this ).serializeArray();

            if($("#gender_male").prop("checked") === true){
                submit_data['gender'] = 'male';
            }else{
                submit_data['gender'] = 'female';
            }

            $.ajax({
                type:'POST',
                url:'/register/save',
                data: submit_data,
                success: function(data){
                    if(data.success === false){
                        $C.error(data.message);
                    }else{
                        $C.success(data.message);
                        clear_fields();
                    }

                    mark_required(data.missing);
                    $(document).scrollTop( $("#main_error").offset().top );
                }
            });
            return false;
        });

    });

}(jQuery, COMMON) );