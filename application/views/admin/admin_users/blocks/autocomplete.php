<?php if( $users->exists()): ?>
        <?php foreach($users as $user): ?>
            <li class="ui-menu-item"><?php echo $user->username; ?></li>
        <?php endforeach; ?>
<?php endif; ?>