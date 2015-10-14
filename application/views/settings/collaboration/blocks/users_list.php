<?php if ($users->exists()) :?>
    <table class="admin-users">
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php foreach($users as $user): ?>
            <?php $is_admin = $user->user_id === $c_user->id; ?>
            <tr>
                <td><?php echo $user->user_username; ?></td>
                <td><?php echo $user->user_email; ?></td>
                <td>
                    <ul class="admin-users-actions">
                        <?php if ( !($user->user_active == $user::STATUS_INVITE)): ?>
                            <li>
                                <?php if ($user->user_active): ?>
                                    <a class="block-user" href="<?php echo site_url('settings/collaboration/block/' . $user->user_id); ?>">
                                        Block
                                    </a>
                                <?php else: ?>
                                    <a class="unblock-user" href="<?php echo site_url('settings/collaboration/unblock/' . $user->user_id); ?>">
                                        Unblock
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                            <li>
                                <a class="delete-user" href="<?php echo site_url('settings/collaboration/delete/' . $user->user_id); ?>">Delete</a>
                            </li>

                        <?php if ($user->user_active == $user::STATUS_INVITE) :?>
                        <li>
                            <a class="reinvite-user" href="<?php echo site_url('settings/collaboration/inviteuser/' . $user->user_id); ?>">
                                Reinvite
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else :?>
    <h3>No results</h3>
<?php endif; ?>