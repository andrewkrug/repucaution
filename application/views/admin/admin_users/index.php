
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('manage_customers') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <?php echo $this->template->block('users', 'admin/admin_users/blocks/users.php', array(
        'users' => $users,
        'c_user' => $c_user
    ));
    ?>
</div>

