<?php foreach($directories as $directory): ?>
    <li class="ui-menu-item"><?php echo $directory->$searchParam; ?></li>
<?php endforeach; ?>
