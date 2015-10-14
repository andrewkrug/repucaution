<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('admins_management') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <a href="" class="invite-action"><?= lang('invite') ?></a>
        </div>
    </div>
    <?php echo $this->template->block(
        'users',
        'admin/manage_admins/blocks/users.php',
        array(
            'users' => $users,
            'c_user' => $c_user,
            'group' => $group
        ));
    ?>
</div>
<div id="invite-block" class="modal fade" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4 id="myModalLabel" class="head_tab"><?= lang('enter_email') ?></h4>
                <div class="form-group col-xs-12">
                    <input type="text" name="email" class="form-control invite-email">
                </div>
            </div>
            <div class="modal-footer clearfix">
                <div class="pull-right">
                    <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel') ?></a>
                    <button type="button" id="invite-btn" class="btn btn-save"><?= lang('invite') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>