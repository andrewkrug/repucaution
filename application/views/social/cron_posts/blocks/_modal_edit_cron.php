<div class="modal fade" id="edit_cron_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <?php echo $this->template->block('_edit_cron', 'social/cron_posts/blocks/_edit_cron'); ?>
            </div>
            <div class="modal-footer clearfix">
                <div class="text-center">
                    <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel') ?></a>
                    <a href="" class="btn btn-save"><?= lang('save') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>