<div class="main-container">
    <div class="info-container">

         <form class="control user-dropdown" id="add-user" method="POST" action="<?php echo site_url('admin/manage_accounts/adduser');?>">
            <?php echo form_dropdown('user', $freeusers); ?>
            <input type="hidden" name="manager" value="<?php echo($managerAccount);?>">
            <input type="submit" class="bind-user-account btn" value="<?= lang('add') ?>">
         </form>
        <?php echo $this->template->block('users', 'admin/manage_accounts/blocks/users.php', array(
            'users' => $users,
            'c_user' => $c_user,
            'group' => $group,
            'managerAccount' => $managerAccount
        ));
        ?>
    </div>
</div>