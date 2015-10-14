<?php if ($users->exists()) :?>
    <table class="responsive-table">
        <thead class="table_head">
        <tr>
            <th><?= lang('username') ?></th>
            <th><?= lang('email') ?></th>
            <th><?= lang('actions') ?></th>
        </tr>
        </thead>
        <?php foreach($users as $user): ?>
            <?php $is_admin = $user->user_id === $c_user->id; ?>
            <tr>
                <td data-th="<?= lang('username') ?>"><?php echo $user->user_username; ?></td>
                <td data-th="<?= lang('email') ?>"><?php echo $user->user_email; ?></td>
                <td data-th="<?= lang('actions') ?>">
                    <ul>
                        <?php if ( !$is_admin && !isset($managerAccount)): ?>
                            <li>
                                <?php if ($user->user_active): ?>
                                    <a class="block-user" href="<?php echo site_url('admin/admin_users/block/' . $user->user_id); ?>">
                                        <?= lang('block') ?>
                                    </a>
                                <?php else: ?>
                                    <a class="unblock-user" href="<?php echo site_url('admin/admin_users/unblock/' . $user->user_id); ?>">
                                        <?= lang('unblock') ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                            <?php if ($group == 'managers') :?>
                                <li>
                                    <a class="manage-users" href="<?php echo site_url('admin/manage_accounts/account/' . $user->user_id); ?>">
                                        <?= lang('manage') ?>
                                    </a>
                                </li>
                            <?php else :?>
                                <li>
                                    <a class="view-profile" href="<?php echo site_url('admin/admin_users/profile/' . $user->user_id); ?>">
                                        <?= lang('profile') ?>
                                    </a>
                                </li>
                            <?php endif;?>
                        <?php endif; ?>
                        <?php if (!$is_admin && !isset($managerAccount)): ?>
                            <li>
                                <a class="delete-user" href="<?php echo site_url('admin/admin_users/delete/' . $user->user_id); ?>"><?= lang('delete') ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($managerAccount)) :?>
                            <li>
                                <a class="remove-user" href="<?php echo site_url('admin/manage_accounts/removeuser/'.$user->user_id.'/'.$managerAccount); ?>"><?= lang('remove') ?></a>
                            </li>
                        <?php endif;?>
                        <?php if ($user->user_active == $user::STATUS_INVITE) :?>
                        <li>
                            <a class="reinvite-user" href="<?php echo site_url('admin/manage_accounts/inviteuser/' . $user->user_id); ?>">
                                <?= lang('reinvite') ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else :?>
    <div class="col-xs-12">
        <p class="large-size m-t20 p-b10 b-Bottom text_color">
            <?= lang('no_users') ?>
        </p>
    </div>
<?php endif?>