/**
 * Created with JetBrains PhpStorm.
 * User: Dred
 * Date: 21.03.13
 * Time: 17:16
 * To change this template use File | Settings | File Templates.
 */
var rebind_vars,check_len;

jQuery(function($){

    var textarea,tw_checkbox, url_input,char_counter, ln_checkbox;


    rebind_vars = function(){
        textarea = $('textarea');
        tw_checkbox = $('[name="post_to_social[]"][value=twitter]');
        ln_checkbox = $('[name="post_to_social[]"][value=linkedin]');
        // var error_box = $('.errors');
        url_input = $('[name=url]');
        char_counter = $('.char-counter');

        textarea.on('keyup blur',check_len);
        url_input.on('keyup blur',check_len);
        tw_checkbox.change(check_len);
        $('[type=file]').change(check_len);
        $('input[name=image_name]').change(check_len);
    }



    check_len = function(){
        char_counter.html('');
        if(!tw_checkbox.prop('checked')){
            return true;
        }

        var text_len = textarea.val().length;

        var default_type = 'maxLength';

        if(undefined === g_settings.twitterDefaultType){
            var isLink = !!(url_input.length && url_input.val().length );

            var isMedia = !!(($('input[name=image_name]').length && $('input[name=image_name]').val().length ) ||
                ( $('[type=file]').length && $('[type=file]').val().length ));

            if(isLink && isMedia){
                default_type = 'lowLength';
            } else if(isLink || isMedia){
                default_type = 'midLength';
            }
        } else {
            default_type = g_settings.twitterDefaultType;
        }


        var max_available = g_settings.twitterLimits[default_type];

        var text = '';
        if(text_len > max_available){
            text += g_settings.twitterLimitsText;
            char_counter.addClass('errors');
            //error_box.html(g_settings.twitterLimitsText);
        } else{
            char_counter.removeClass('errors');
        }
        text += ' ( '+text_len+'/'+max_available+' )';
        char_counter.html( $.trim(text) );

        return !(text_len > max_available);

    }

    $(document).ready(function(){
        rebind_vars();
    });

});