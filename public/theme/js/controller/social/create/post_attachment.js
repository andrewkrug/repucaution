(function ($) {
    $(document).ready(function () {
        /**
         * Contains 'post a link' form / Suggested RSS form / Custom RSS form
         *
         * @type {*|HTMLElement}
         */
        var $container = $('#attachment');

        /**
         * Container for file is uploading
         *
         * @type {*|HTMLElement}
         */
        var $file_name = $('#filename');

        /**
         * Form for post to socials
         *
         * @type {*|HTMLElement}
         */
        var $form = $('#post-update-form');

        var $need_attach = $('#need_attach');

        var $imageDesignerData = $('input[name="image_designer_data"]');

        var $logo = $('#image-designer-logo');

        var $bgImages = $('.image-designer-bg-image');

        var $addSecondaryText = $('#image-designer-add-secondary-text');
        var $secondaryTextBlock = $('#secondary-text-block');

        var $headlineText = $('#image-designer-headline-text');
        var $secondaryText = $('#image-designer-secondary-text');

        var headlineDefaultText = lang('design_a_custom_image');
        var secondaryDefaultText = lang('design_a_custom_image');

        $headlineText.val(headlineDefaultText);
        $secondaryText.val(secondaryDefaultText);

        var $headlineFont = $('#headline_font_select');
        var $secondaryFont = $('#secondary_font_select');

        var $pickColor = $(".pick-a-color");

        var $bgContrast = $('#image-designer-bg-type-contrast');

        var filters = [
            'contrast',
            'blurred',
            'grayscale'
        ];

        var canvas = new fabric.Canvas('image-designer-canvas');
        var canvasImage = null;
        var canvasBgImage = null;
        var canvasHeaderText = new fabric.Text($headlineText.val(), {
            left: 10,
            top: 10,
            fontFamily: $headlineFont.val(),
            fontSize: 30,
            fill: '#ffffff',
            shadow: 'rgba(0,0,0,0.3) 1px 1px 1px'
        });
        var canvasSecondaryText = new fabric.Text($secondaryText.val(), {
            left: 10,
            top: 100,
            fontFamily: $secondaryFont.val(),
            fontSize: 22,
            fill: '#ffffff',
            shadow: 'rgba(0,0,0,0.3) 1px 1px 1px'
        });
        canvas.backgroundColor = 'grey';
        canvas.add(canvasHeaderText);

        if($imageDesignerData.val()) {
            canvas.loadFromJSON($imageDesignerData.val(), canvas.renderAll.bind(canvas));
        }

        $pickColor.pickAColor({
            showHexInput: false
        });

        $pickColor.on('change', function() {
            var $this = $(this);
            if($this.attr('name') == 'headline_color') {
                canvasHeaderText.setColor('#'+$this.val());
            } else {
                canvasSecondaryText.setColor('#'+$this.val());
            }
            canvas.renderAll();
        });

        $headlineText.on('keyup blur', function(){
            var $this = $(this);
            canvasHeaderText.setText($this.val());
            canvas.renderAll();
        });

        $secondaryText.on('keyup blur', function(){
            var $this = $(this);
            canvasSecondaryText.setText($this.val());
            canvas.renderAll();
        });

        $headlineText.on('focus', function(){
            var $this = $(this);
            if($this.val() == headlineDefaultText) {
                $this.val('');
            }
        });

        $secondaryText.on('focus', function(){
            var $this = $(this);
            if($this.val() == secondaryDefaultText) {
                $this.val('');
            }
        });

        $headlineFont.on('change', function() {
            var $this = $(this);
            canvasHeaderText.setFontFamily($this.val());
            canvas.renderAll();
        });

        $secondaryFont.on('change', function() {
            var $this = $(this);
            canvasSecondaryText.setFontFamily($this.val());
            canvas.renderAll();
        });

        $container.on('change', 'input[name="attachment_type"]', function () {
            var $this = $(this);
            var $block = $('#'+$this.val()+'-block');
            if($block.css('display') == 'none') {
                $('.attachment-block').hide(200);
                $block.show(200);
            }
        });

        $addSecondaryText.on('click', function() {
            var $this = $(this);
            if($this.data('added') == 'true') {
                $this.data('added', 'false');
                $this.html(lang('add_secondary_text'));
                $this.removeClass('btn-remove');
                $this.addClass('btn-add');
                $secondaryTextBlock.hide(1000);
                canvas.remove(canvasSecondaryText);
                canvas.renderAll();
            } else {
                $this.data('added', 'true');
                $this.html(lang('remove_secondary_text'));
                $this.removeClass('btn-add');
                $this.addClass('btn-remove');
                $secondaryTextBlock.show(1000);
                canvas.add(canvasSecondaryText);
                canvas.renderAll();
            }
            return false;
        });

        $container.on('click', '.image-designer-bg-image', function(){
            var $this = $(this);
            $bgImages.removeClass('active');
            $this.addClass('active');
            fabric.Image.fromURL($this.data('src'), function(img) {
                img.set({width: canvas.width, height: canvas.height, originX: 'left', originY: 'top'});
                canvas.setBackgroundImage(img, function(){});
                canvasBgImage = img;
                $('input[name="bg_image_type"]').trigger('change');
            });
        });
        $($bgImages[Math.floor(Math.random() * $bgImages.length)]).trigger( "click" );


        fabric.Image.filters.Blur = fabric.util.createClass({

            type: 'Blur',
            radius: 9,

            applyTo: function(canvasEl) {
                stackBlurImage(canvasEl, this.radius, false);
            }
        });

        fabric.Image.filters.Blur.fromObject = function(object) {
            return new fabric.Image.filters.Redify(object);
        };


        $container.on('change', 'input[name="bg_image_type"]', function() {
            var $this = $(this);
            if(!this.checked) {
                return;
            }
            canvasBgImage.filters[filters.indexOf('contrast')] = ($bgContrast[0].checked) ?
                new fabric.Image.filters.Tint({
                    color: '#000',
                    opacity: 0.5
                }) :
                null;
            switch ($this.val()) {
                case 'normal':
                    canvasBgImage.filters[filters.indexOf('grayscale')] = null;
                    canvasBgImage.filters[filters.indexOf('blurred')] = null;
                    break;
                case 'blurred':
                    canvasBgImage.filters[filters.indexOf('grayscale')] = null;
                    canvasBgImage.filters[filters.indexOf($this.val())] = new fabric.Image.filters.Blur({
                        radius: 13
                    });
                    break;
                case 'grayscale':
                    canvasBgImage.filters[filters.indexOf('blurred')] = null;
                    canvasBgImage.filters[filters.indexOf($this.val())] = new fabric.Image.filters.Grayscale();
                    break;
            }
            canvasBgImage.applyFilters(canvas.renderAll.bind(canvas));
        });

        $bgContrast.on('change', function() {
            $('input[name="bg_image_type"]').trigger('change');
        });

        var $current_progress, $current_button;
        var options = {
            maxFileSize: 10000000,
            dataType: 'json',
            multiple: true,
            done: function (e, data) {
                $current_button.show();
                $current_progress.hide();
                var result = data.result[0];

                if (undefined !== result.error && result.error.length) {
                    $file_name.empty();
                    showFlashErrors(result.error);
                    return;
                }
                var size = data.files[0].size / 1000;
                var type = $(this).attr('id');
                var preview;
                if (type == 'videos') {
                    preview = '<div class="preview" >' +
                    '<img class="img-close" src="' + g_settings.base_url + '/public/images/im_prev_close.png" />' +
                    '<video class="img-preview">' +
                    '<source src="' + result.url + '"/>' +
                    '<span>' + data.files[0].name + '</span>' +

                    '</video></div>';
                } else if(type =='image-designer') {
                    preview = '<img class="image-designer-logo" src="' + result.url + '" />'
                    +'<button class="btn-close-image"></button>';
                } else if(type =='image-designer-bg') {
                    preview = '<img class="image-designer-bg-image" src="' + result.url + '"  data-src="' + result.url + '" />';
                } else {
                    preview = '<div class="preview" >' +
                    '<img class="img-close" src="' + g_settings.base_url + '/public/images/im_prev_close.png" />' +
                    '<img class="img-preview" src="' + result.url + '" />' +
                    '</div>';
                }
                if (type == 'image-designer') {
                    $logo.show();
                    $logo.find('.preview').html(preview);
                    if(canvasImage === null) {
                        fabric.Image.fromURL(result.url, function(oImg) {
                            canvasImage = oImg;
                            var delta = canvasImage.getWidth() / 128;
                            canvasImage.setWidth(128);
                            canvasImage.setHeight(canvasImage.getHeight() / delta);
                            canvas.add(canvasImage);
                        });
                    } else {
                        canvasImage.setSrc(result.url, function() {
                            var delta = canvasImage.getWidth() / 128;
                            canvasImage.setWidth(128);
                            canvasImage.setHeight(canvasImage.getHeight() / delta);
                            canvas.renderAll();
                        });
                    }
                } else if(type =='image-designer-bg') {
                    $(preview).insertBefore($(this).parent().find('.image-designer-bg-image:first'));
                } else {
                    $(this).parent().find('.preview').remove();
                    $(this).parent().find('i').hide();
                    $(this).parent().prepend(preview);
                }
                $form.find('input[name=image_name]').val(data.files[0].name).trigger('change');
            },
            start: function () {

            },
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $current_progress.find('.progressLine').val(progress);
            },
            fail: function (e, data) {
                $current_button.show();
                $current_progress.hide();
                showFlashErrors(lang('file_upload_error'));
            },
            add: function (e, data) {
                $current_progress = $(this).parent().find('.progressBar');
                $current_button = $(this).parent().find('.fileSelect');
                $current_progress.show();
                $current_button.hide();
                var acceptFileTypes;
                if ($(this).attr('id') == 'videos') {
                    acceptFileTypes = /\.(wmv|avi|mpe?g|mp4|webm|3gpp|mpegps|flv)$/;
                } else if($(this).attr('id') == 'image-designer'
                    || $(this).attr('id') == 'image-designer-bg') {

                    acceptFileTypes = /\.(jpeg|jpg|png)$/;
                }  else {
                    acceptFileTypes = /\.(gif|jpeg|jpg|png)$/;
                }
                if (!acceptFileTypes.test(data.files[0].name)) {
                    showFlashErrors(lang('file_type_error'));
                    $current_button.show();
                    $current_progress.hide();
                    return false;
                } else {
                    data.submit();
                    $file_name.html(data.files[0].name);
                }

            },
            autoUpload: true,
            url: g_settings.base_url + 'social/create/upload_images'
        };

        $(".inputFile").fileupload(options);

        $container.on('click', '.img-close, .btn-close-image', function () {
            var $self = $(this);
            var parent = $self.parent();
            var img = parent.find('.img-preview');
            if(!img.length) {
                img = parent.find('.image-designer-logo');
            }
            var vid = parent.find('video');
            var fname;
            if (vid.length) {
                fname = vid.find('source').attr('src');
            } else {
                fname = img.attr('src');
            }
            var url = g_settings.base_url + 'social/create/upload_images?file=' + fname;
            var $post_id = $('[name="post_id"]');
            if ($post_id.length) {
                url += '&post_id=' + $post_id.val();
            }
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function (success) {
                    if (success) {
                        $file_name.empty();
                        $form.find('input[name=image_name]').val('').trigger('change');
                        var type = $form.find('input[name=image_name]').attr('id');
                        if(!type == 'imange-designer') {
                            parent.parent().find('i').show();
                            parent.remove();
                        } else {
                            parent.html('');
                            $logo.hide();
                            canvas.remove(canvasImage);
                            canvasImage = null;
                        }
                        $(".inputFile").fileupload(options);
                    }
                }
            });
            return false;
        });

        $('.main_block').on('click', '#post-button', function () {
            wait();
            try {
                $imageDesignerData.val(JSON.stringify(canvas.toJSON()));
            } catch(e) {

            }
            if($need_attach.prop('checked') && $('input[name="attachment_type"]:checked').val() == 'image-designer') {
                var strDataURI = canvas.toDataURL({
                    format: 'png',
                    multiplier: 2
                });
                strDataURI = strDataURI.substr(22, strDataURI.length);
                $.post(g_settings.base_url + 'social/create/uploadImageDesignerFile',{
                    image_designer_data_url: strDataURI
                },
                function(data){
                    var answer = JSON.parse(data);
                    if(answer.success || !answer.image_name) {
                        $form.find('input[name=image_name]').val(answer.image_name);
                        $form.submit();
                    } else {
                        showFlashErrors(lang('image_designer_upload_error'));
                        stopWait();
                    }
                }).fail(function() {
                        showFlashErrors(lang('other_error'));
                        stopWait();
                    });
            } else {
                $form.submit();
            }
        });

    });
})(jQuery);