<?php
/**
 * @var array $groups
 * @var array $imageDesignerImages
 */
?>
<style>
    .preview .img-close {
        position: absolute;
        right: 27px;
        top: 13px;
        cursor: pointer;
    }

    .preview .img-preview {
        width: 100%;
        margin-bottom: 10px;
    }
    .preview .img-close.logo-close {
        position: absolute;
        right: 8px;
        top: -5px;
        cursor: pointer;
    }

    .preview .img-preview.logo-preview {
        width: 100%;
        margin-bottom: 10px;
    }
</style>
<div class="row">
    <div class="col-xs-12 m-t5 custom-form">
        <label class="cb-checkbox regRoboto m-r10" data-toggle="#attachment">
            <input type="checkbox" id="need_attach">
            <?= lang('attach_image_or_video') ?>
        </label>
    </div>
</div>
<div class="row is-hidden" id="attachment">
    <div class="col-xs-12 custom-form">
        <label class="cb-radio regRoboto m-r10 checked">
            <input name="attachment_type" value="image-designer" checked="checked" type="radio">
            <?= lang('image_designer') ?>
        </label>
        <label class="cb-radio regRoboto m-r10">
            <input name="attachment_type" value="photo" type="radio">
            <?= lang('photo') ?>
        </label>
        <label class="cb-radio regRoboto">
            <input name="attachment_type" value="video" type="radio">
            <?= lang('video') ?>
        </label>
    </div>
    <div class="col-xs-12 attachment-block" id="image-designer-block">
        <div class="well well-standart well-upload-image">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-xs-12">
                            <p class="head_tab"><?= lang('head_text') ?></p>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group ">
                                        <select id="headline_font_select" class="chosen-select">
                                            <option selected="selected" value="Fredoka One">Fredoka One</option>
                                            <option value="Hammersmith One">Hammersmith One</option>
                                            <option value="Josefin Slab">Josefin Slab</option>
                                            <option value="Lato">Lato</option>
                                            <option value="Merriweather">Merriweather</option>
                                            <option value="Montserrat">Montserrat</option>
                                            <option value="Open Sans">Open Sans</option>
                                            <option value="Roboto">Roboto</option>
                                            <option value="Satisfy">Satisfy</option>
                                            <option value="Ubuntu">Ubuntu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group pull-md-right">
                                        <div class="input-group pick-a-color-markup" id="headline_color">
                                            <input value="ffffff" name="headline_color" class="pick-a-color form-control" type="hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="headline_text" id="image-designer-headline-text" rows="10" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12" id="secondary-text-block" style="display: none;">
                            <p class="head_tab"><?= lang('secondary_text') ?></p>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group ">
                                        <select id="secondary_font_select" class="chosen-select">
                                            <option selected="selected" value="Fredoka One">Fredoka One</option>
                                            <option value="Hammersmith One">Hammersmith One</option>
                                            <option value="Josefin Slab">Josefin Slab</option>
                                            <option value="Lato">Lato</option>
                                            <option value="Merriweather">Merriweather</option>
                                            <option value="Montserrat">Montserrat</option>
                                            <option value="Open Sans">Open Sans</option>
                                            <option value="Roboto">Roboto</option>
                                            <option value="Satisfy">Satisfy</option>
                                            <option value="Ubuntu">Ubuntu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group pull-md-right">
                                        <div class="input-group pick-a-color-markup" id="headline_color">
                                            <input value="ffffff" name="secondary_color" class="pick-a-color form-control" type="hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="secondary_text" id="image-designer-secondary-text" rows="10" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div id="image-designer-logo" style="display: none;">
                        <div class="row">
                            <div class="col-sm-5 preview" id="image-designer-logo">

                            </div>
                        </div>
                    </div>
                    <div class="row well-standart">
                        <div class="col-xs-12 text-left">
                            <div class="progressBar" style="display: none;">
                                <div class="progressLine" data-value="0"></div>
                            </div>
                            <button class="btn btn-add m-b20" id="image-designer-add-secondary-text" data-added="false">
                                <?= lang('add_secondary_text') ?>
                            </button>
                            <button class="btn btn-save fileSelect m-b20">
                                <?= lang('add_logo') ?>
                            </button>
                            <input class="uploadbtn inputFile" id="image-designer" multiple="" type="file">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 well-standart">
                    <div class="canvas-block">
                        <div class="canvas-container">
                            <canvas id="image-designer-canvas" width="512px" height="256px"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 custom-form m-tb20">
                            <label class="cb-radio regRoboto m-r10 checked">
                                <input name="bg_image_type" value="normal" checked="checked" type="radio">
                                <?= lang('normal') ?>
                            </label>
                            <label class="cb-radio regRoboto m-r10">
                                <input name="bg_image_type" value="blurred" type="radio">
                                <?= lang('blurred') ?>
                            </label>
                            <label class="cb-radio regRoboto m-r10">
                                <input name="bg_image_type" value="grayscale" type="radio">
                                <?= lang('black_and_white') ?>
                            </label>
                            <label class="cb-checkbox regRoboto">
                                <input name="bg_image_type_contrast" id="image-designer-bg-type-contrast" type="checkbox">
                                <?= lang('increased_contrast') ?>
                            </label>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="well_photo">
                            <div class="progressBar" style="display: none;">
                                <div class="progressLine" data-value="0"></div>
                            </div>
                            <button class="btn btn-upload-photo fileSelect">
                                <?= lang('upload_background_image') ?>
                            </button>
                            <input class="uploadbtn inputFile" id="image-designer-bg" multiple="" type="file">
                            <?php foreach($imageDesignerImages as $imageDesignerImage) : ?>
                                <img
                                    class="image-designer-bg-image"
                                    src="<?= base_url().$imageDesignerImage['thumbnail'] ?>"
                                    alt=""
                                    data-src="<?= base_url().$imageDesignerImage['image'] ?>"
                                    />
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-8 col-lg-6 attachment-block" id="photo-block" style="display: none;">
        <div class="well well-standart">
            <?php if(isset($isMedia) && $isMedia && $isMedia->type == 'image'): ?>
                <div class="preview">
                    <img class="img-close"
                         src="/public/images/im_prev_close.png">
                    <img class="img-preview"
                         src="<?= preg_split('|\.\./\.\.|', $isMedia->path)[1]; ?>">
                </div>
            <?php else: ?>
                <i class="fa fa-image"></i>
            <?php endif; ?>
            <button class="btn-save fileSelect">
                <?= lang('upload_photo') ?>
            </button>
            <div class="progressBar" style="display: none;">
                <div class="progressLine" data-value="0"></div>
            </div>
            <input class="form-control uploadbtn inputFile" type="file" multiple="">
        </div>
    </div>
    <div class="col-sm-12 col-md-8 col-lg-6 attachment-block" id="video-block" style="display: none;">
        <div class="well well-standart">
            <i class="fa fa-play"></i>
            <button class="btn-save fileSelect">
                <?= lang('upload_video') ?>
            </button>
            <div class="progressBar" style="display: none;">
                <div class="progressLine" data-value="0"></div>
            </div>
            <input class="form-control uploadbtn inputFile" id="videos" type="file" multiple="">
        </div>
    </div>
    <input type="hidden" name="image_name" value="">
    <input type="hidden" name="image_designer_data" value="">
</div>