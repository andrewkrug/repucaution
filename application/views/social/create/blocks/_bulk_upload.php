<?php
/**
 * @var bool $need_bulk_upload_notification
 */
?>
<div class="row">
    <div class="col-xs-12 m-t10">
        <button class="btn" id="bulk_upload" data-show-modal="<?= $need_bulk_upload_notification ?>"><?= lang('bulk_upload') ?></button>
        <input type="file" id="bulk_upload_file" style="display: none;"/>
        <a class="link" href="<?= base_url() ?>/public/theme/files/CSV%20Example.csv"><?= lang('download_example_file') ?></a>
    </div>
</div>

<div class="modal fade" id="bulk_upload_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4 class="head_tab"><?= lang('bulk_upload_info_header') ?></h4>
                <?= lang('bulk_upload_info_text') ?>
                <div class="custom-form">
                    <label class="cb-checkbox regRoboto m-r10">
                        <input type="checkbox" id="show_bulk_upload_notification">
                        <?= lang('show_bulk_upload_notification_label') ?>
                    </label>
                </div>
            </div>
            <div class="modal-footer clearfix">
                <div class="text-center">
                    <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel') ?></a>
                    <a class="btn btn-save" id="bulk_upload_modal_button"><?= lang('bulk_upload') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>