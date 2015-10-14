$(function() {
/**
 * Form for adding category
 *
 * @type {*|HTMLElement}
 */
var addCategory = $('#addCategory');

/**
 * Form for adding category
 *
 * @type {*|HTMLElement}
 */
var trainingForm = $('#training-manage');

/**
 * Loading div
 *
 * @type {*}
 */
var $loading = $('<div>').addClass('loading');

/**
 * Show it then post successfully created
 *
 * @type {*|HTMLElement}
 */
var $successfully_message = $('#successfully');



});
/**
 * Append error message for input
 *
 * @param $container HtmlElement - container of inputs
 * @param errors Array 
 */
function appendErrors($container, errors){
    for (var error in errors) {
            var errMsg = errorHtml(errors[error]);
            $container.find('input[name="' + error + '"]').parents('.control-group').find('label').append(errMsg);
            $container.find('textarea[name="' + error + '"]').parents('.control-group').find('label').append(errMsg);
    }
}

/**
 * Return html of error message
 *
 * @param text
 */
function errorHtml(text){
    return '<span class="message-error configure-error">'+
                                    text+
                               '</span>';
}
//#################################333
function bytesToSize(bytes, precision)
{
    var kilobyte = 1024;
    var megabyte = kilobyte * 1024;
    var gigabyte = megabyte * 1024;
    var terabyte = gigabyte * 1024;

    if ((bytes >= 0) && (bytes < kilobyte)) {
        return bytes + ' B';

    } else if ((bytes >= kilobyte) && (bytes < megabyte)) {
        return (bytes / kilobyte).toFixed(precision) + ' KB';

    } else if ((bytes >= megabyte) && (bytes < gigabyte)) {
        return (bytes / megabyte).toFixed(precision) + ' MB';

    } else if ((bytes >= gigabyte) && (bytes < terabyte)) {
        return (bytes / gigabyte).toFixed(precision) + ' GB';

    } else if (bytes >= terabyte) {
        return (bytes / terabyte).toFixed(precision) + ' TB';

    } else {
        return bytes + ' B';
    }
}
jQuery(document).ready(function() {
    
    /**
     * Video control block 
     *
     * @type {*|HTMLElement}
     */
    var video = $('div.video');

    /**
     * Category control block 
     *
     * @type {*|HTMLElement}
     */
    var category = $('div.categories');
       
    /**
     * Form  
     *
     * @type {*|HTMLElement}
     */
    var $form = $('#training-manage');
    $form.submit( function(e) {
        $('.message-error').remove();
        if(video_to_upload == undefined ||  !video_to_upload){
            e.preventDefault();
            video.prepend(errorHtml('Please select a video file.'));
            return;
        }
        categoryCheck = $('.check input:checked');
        if(!categoryCheck.length){
            e.preventDefault();
            category.prepend(errorHtml('Please select a category.'));
            return;
        }
       
        $form.find('.btn').attr('disabled', 'disabled');
        $('#file_upload').fileupload('disable');
        $('#file_upload-button').hide();

        $.post($(this).attr('action'), $(this).serialize()).fail(function(jqXHR, textStatus, errorThrown){
            if(undefined !== jqXHR.responseText && jqXHR.responseText.length){
                video.prepend(errorHtml(jqXHR.responseText));
                $form.find('.btn').removeAttr('disabled');
                $('#file_upload').fileupload('enable');
                $('#file_upload-button').show();
            }
        }).done(function(data){
                video_to_upload.formData = data;
                video_to_upload.submit();
        });
        return false;
    });
    var video_to_upload;
    var progress_container = $('#progress-container');
    var file_info = $('#file-info');
    var progress = false;
    if (window.FileReader && window.Blob) {
        progress = true;
    } else {
        video.prepend(errorHtml('Please note, video upload is only supported with <b>Firefox</b> and <b>Chrome</b> browsers.</p>'));
    }


    var options = {
        dataType: 'json',
        maxChunkSize: (1000 * 1000 * 1), //1mb
        autoUpload:false,
        maxFileSize: (5 * 1024 * 1024 * 1024), // 5000MB
        add: function (e, data) {
            var fu = $(this).data('fileupload');

            if( data != undefined &&
                data.files != undefined &&
                data.files[0] != undefined &&
                data.files[0].size != undefined &&
                fu.options.maxFileSize != undefined &&
                fu.options.maxFileSize > 0 &&
                data.files[0].size > fu.options.maxFileSize){

                var file_size = bytesToSize(data.files[0].size,1);
                var max_available_size = bytesToSize(fu.options.maxFileSize, 1);

                $('#content .alert-error').html('<p>Error: max available upload file size is '+max_available_size+', you try to upload '+file_size+'</p>').show();

                return;
            }

            var file_info_string = '';
            if( data.files[0].name != undefined ){
                file_info_string = 'Name: '+data.files[0].name;
                if(data.files[0].size != undefined ){
                    var file_size = data.files[0].size;
                    file_info_string += ' | Size: '+ bytesToSize(file_size, 2);
                }
                file_info.html(file_info_string);
            }
            file_info.show();
            video_to_upload = data;
            if( progress ){
                progress_container.html('<div class="progress progress-striped active"><div class="text-info"></div><div class="bar" style="width: 0%;"></div></div>');
                progress_container.show();
            }

        },
        submit:function(e, data){
            if( !progress ){
                progress_container.html('<div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div>');
                progress_container.show();
            }
        },
        fail:function (e, data) {
            data.retries_fail = true;
        },
        always:function (e, data) {

            if(data.textStatus == 'success' || (data.textStatus == 'error' && data.retries_fail != undefined && data.retries_fail) ){

                progress_container.empty();
                progress_container.hide();
                if( progress ){
                    file_info.hide();
                }
                if(data.textStatus == 'error'){
                    $('#file_upload-button').show();
                    $('#file_upload').fileupload('enable');
                    video.prepend(erroHtml('Connection error. please try again.'));
                    video_to_upload = null;
                    $form.find('.btn').removeAttr('disabled');
                }
                if(data.textStatus == 'success'){

                    if(data.result.file[0].error != 0){
                        $('#uploadify').parent().prepend(errorHtml(data.result.file[0].error));
                        
                    } /* else {
                        $.ajax({
                            url: $('#upload_video_url').val(),
                            async: true,
                            data: {filename: video_to_upload},
                            type: 'POST',
                            success: function(){
                                $('#video_uploaded_popup').modal('show');
                            },
                            fail: function(){
                                $('#content .alert-error').html('<p>Connection error. please try again.</p>').show();
                                alert('Connection error. please try again.');
                            }
                        })
                    } */
                    $('#file_upload-button').show();
                    $('#file_upload').fileupload('enable');
                    video_to_upload = null;
                    $form.find('.btn').removeAttr('disabled');


                }


            }
        }

    };
    if(progress){
        options.progressall = function (e, data) {
            var percents = parseInt(data.loaded / data.total * 100, 10);
            var bar = progress_container.find('.bar');
            var val = percents+'%';
            bar.css('width', val);
            progress_container.find('.text-info').text( bytesToSize(data.loaded, 2)+' ('+val+')' );
        };
        options.maxRetries=0;
        options.retryTimeout=500;
        options.fail = function (e, data) {
            var fu = $(this).data('fileupload'),
                retries = data.retries || 0,
                retry = function () {
                    data.data = null;
                    data.submit();
                };
            if (data.errorThrown !== 'abort' &&
                data.errorThrown !== 'uploadedBytes' &&
                ( !fu.options.maxRetries || retries < fu.options.maxRetries ) ) {
                retries += 1;
                data.retries = retries;
                window.setTimeout(retry, retries * fu.options.retryTimeout);

                //sent interval set to 1 min
                if(!fu.options.maxRetries && data.retries > 600){
                    data.retries = 120;
                }
                return;
            }
            data.retries = null;
            data.retries_fail = true;
        }
    }

    $('#file_upload').fileupload(options);

    /* $('#video_ok').click(function() {
        $('#video_uploaded_popup').modal('hide');
        window.location.href=window.location.href;
    }); */

});


