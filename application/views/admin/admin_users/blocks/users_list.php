<?php if (($users && $users->exists())) :?>
<table class="responsive-table admin-users">
    <thead class="table_head">
        <tr>
            <th><?= lang('username') ?></th>
            <th><?= lang('email') ?></th>
            <th><?= lang('action') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($users as $user): ?>
        <?php $is_admin = $user->id === $c_user->id; ?>
        <tr>
            <td data-th="<?= lang('username') ?>"><?php echo $user->username; ?></td>
            <td data-th="<?= lang('email') ?>"><?php echo $user->email; ?></td>
            <td data-th="<?= lang('action') ?>">
                <ul>
                    <?php if ( ! $is_admin): ?>
                        <li>
                            <?php if ($user->active): ?>
                                <a class="block-user" href="<?php echo site_url('admin/admin_users/block/' . $user->id); ?>">
                                    <?= lang('block') ?>
                                </a>
                            <?php else: ?>
                                <a class="unblock-user" href="<?php echo site_url('admin/admin_users/unblock/' . $user->id); ?>">
                                    <?= lang('unblock') ?>
                                </a>
                            <?php endif; ?>
                        </li>
                        <?php if ($group == 'managers') :?>
                            <li>
                                <a class="manage-users" href="<?php echo site_url('admin/manage_accounts/account/' . $user->id); ?>">
                                    <?= lang('manage') ?>
                                </a>
                            </li>
                        <?php else :?>
                            <li>
                                <a class="view-profile" href="<?php echo site_url('admin/admin_users/profile/' . $user->id); ?>">
                                    <?= lang('profile') ?>
                                </a>
                            </li>
                        <?php endif;?>
                    <?php endif; ?>
                    <?php if ( ! $is_admin): ?>
                        <li>
                            <a class="delete-user" href="<?php echo site_url('admin/admin_users/delete/' . $user->id); ?>"><?= lang('delete') ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else:?>
    <div class="col-xs-12">
        <p class="large-size m-t20 p-b10 b-Bottom text_color">
            <?= lang('no_users') ?>
        </p>
    </div>
<?php endif?>