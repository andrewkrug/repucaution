<div class="row">
    <div class="col-xs-12">
        <ul class="nav nav-tabs settings_tab">

            <?php foreach ($activity_tabs as $social => $data): ?>
                <li class="setting_item auto<?php if($this->router->fetch_method() === $social): ?> active<?php endif; ?>">
                    <a class="setting_link" href="<?php echo site_url('social/activity/' . $social); ?>">
                        <i class="ti-<?php echo $social; ?>"></i>
                        <?php echo $data['title']; ?>
                    </a>
                </li>
            <?php endforeach; ?>

        </ul>
    </div>
</div>