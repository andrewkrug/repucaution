<?php if (!($users && $users->exists())): ?>
    <h5>No users</h5>

<?php else: ?>
    <div class="wrap-users-list">
   <?php
        echo $this->template->block('users_list', 'settings/collaboration/blocks/users_list', array(
                                                                                                    'users' => $users,
                                                                                                    'c_user' => $c_user,
                                                                                                    'group' => $group,


        ));
    ?>
   </div>
<?php endif; ?>
