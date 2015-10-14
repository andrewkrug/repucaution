jQuery(document).ready(function() {
    var fu = $('#file_upload');
    var accept = $('#accept-btn');
    var reset = $('#reset-btn');
    var preset = 'wide';
    var ias;
    var img;
    fu.fileupload({
        dataType:'json',
        autoUpload: false,
        url: g_settings.base_url+'upload/uploadimagefile',
        add: function (e, data) {
            data.submit();
        },
        always: function(e, data){
            var result = data.result.info;
            if (result && result.error == 0) {
                var fileId = data.result.upload_file_id;
                var update = data.result.image_update;
                $('#file_id').val(fileId);
                $('#image_update').val(update);
                $('.avatar-container').prepend(
                    '<img id="preset-img" class="preset-image" src="'+g_settings.base_url+
                        'images/'+preset+'/'+fileId+'/'+update+'" />'
                );
                bindAreaSelect();
                $('#file_upload').hide();
                accept.show();
                reset.show();
            }
        }

    });

    /**
     * Accept image
     */
    accept.on('click', function(){
        if (ias && $('#preset-img').length) {

            img = $('#preset-img');
            var area = ias.getSelection();
            var imgWidth = img.width();
            var imgHeight = img.height();

            var scale = {
                x : area.x1/imgWidth,
                y : area.y1/imgHeight,
                w : area.width/imgWidth,
                h : area.height/imgHeight
            };

            if (scale.w && scale.h) {
                $.ajax({
                    url: $('#preset-img').attr('src'),
                    type: 'POST',
                    dataType: 'html',
                    data:{
                        x : scale.x,
                        y : scale.y,
                        w : scale.w,
                        h : scale.h
                    },
                    success: function(data){
                        $('#preset-img').attr('src', g_settings.base_url+data);
                        ias.cancelSelection();
                        ias.setOptions({hide:true, disable:true});
                        accept.hide();
                    }
                });
            }

        }
    });
    /**
     * Reset image
     */
    reset.on('click', function(){
        $.ajax({
            url: $('#preset-img').attr('src'),
            type: 'POST',
            dataType: 'html',
            data:{
                x : 0,
                y : 0,
                w : 0,
                h : 0
            },
            success: function(data){
                $('#preset-img').attr('src', g_settings.base_url+data);
                accept.show();
            }
        });
        ias.setSelection(0, 0, 0, 0, true);
        ias.setOptions({enable:true});
    });

    function bindAreaSelect(){
        ias = $("#preset-img").imgAreaSelect({
            instance:true
        });
    }


});
