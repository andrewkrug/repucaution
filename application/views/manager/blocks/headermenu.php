<div class="manager-user-dropdown">
    <form id="manager-login-as" action="<?php echo site_url('manager/manager_route/login');?>" method="POST">
        <?php echo form_dropdown('user', $users, array($currentId)); ?>
    </form>
</div>

