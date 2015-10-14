<?php if (!($users && $users->exists())): ?>
    <div class="row">
        <div class="col-xs-12">
            <p class="large-size m-t20 p-b10 b-Bottom text_color">
                <?= lang('no_users') ?>
            </p>
        </div>
    </div>

<?php else: ?>

    <?php
        echo $this->template->block('search_users', 'admin/admin_users/blocks/search_user', array(
                                                                                                'limit' => $limit,
                                                                                                'group' => $group,
        ));
    ?>
    <div class="row wrap-users-list">
   <?php
        $blockName = ($searchList) ? 'search_users_list' : 'users_list';
        echo $this->template->block('users_list', 'admin/manage_accounts/blocks/'.$blockName, array(
                                                                                                    'users' => $users,
                                                                                                    'c_user' => $c_user,
                                                                                                    'group' => $group,


        ));
    ?>
   </div>
    <?php echo $this->template->block('pagination', 'admin/admin_users/blocks/pagination', array('page' => $page)); ?>
<?php endif; ?>
