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
                <td data-th="<?= lang('username') ?>"><?php echo $user->username; ?></td>
                <td data-th="<?= lang('email') ?>"><?php echo $user->email; ?></td>
                <td data-th="<?= lang('actions') ?>">
                    <ul>
                        <?php if ($user->active == $user::STATUS_INVITE) :?>
                            <li>
                                <a class="reinvite-user" href="<?php echo site_url('admin/manage_accounts/inviteuser/' . $user->id); ?>">
                                    <?= lang('reinvite') ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ( !$is_admin && !isset($managerAccount)): ?>

                                <?php if ($user->active != $user::STATUS_INVITE) :?>
                                    <?php if ($user->active) : ?>
                                        <li>
                                            <a class="block-user" href="<?php echo site_url('admin/admin_users/block/' . $user->id); ?>">
                                                <?= lang('block') ?>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li>
                                            <a class="unblock-user" href="<?php echo site_url('admin/admin_users/unblock/' . $user->id); ?>">
                                                <?= lang('unblock') ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <?php if ($user->isInvitedAndNotActual()) :?>
                                        <li>
                                            <a class="reinvite-user" href="<?php echo site_url('admin/manage_accounts/inviteuser/' . $user->id); ?>">
                                                <?= lang('reinvite') ?>
                                            </a>
                                        </li>
                                    <?php endif;?>
                                <?php endif; ?>

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
                        <?php if (!$is_admin && !isset($managerAccount)): ?>
                            <li>
                                <a class="delete-user" href="<?php echo site_url('admin/admin_users/delete/' . $user->id); ?>"><?= lang('delete') ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if (isset($managerAccount)) :?>
                            <li>
                                <a class="remove-user" href="<?php echo site_url('admin/manage_accounts/removeuser/'.$user->id.'/'.$managerAccount); ?>"><?= lang('remove') ?></a>
                            </li>
                        <?php endif;?>

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