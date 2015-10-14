<div class="navbar navbar-fixed-top header">
    <div class="logo">
        <a href="<?php echo base_url(); ?>" class="logo_link">
            Repucaution
        </a>
    </div>
    <button class="btn btn-menu">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <div class="head_nav">
        <ul class="user-nav">
            <li class="user-nav_item">
                <a class="user-nav_link" href="http://repucaution.com/knowledge-base/">
                    Knowledge Base
                </a>
            </li>
            <li class="user-nav_item">
                <a class="user-nav_link" href="http://repucaution.com/guide">
                    User's Guide
                </a>
            </li>
            <li class="user-nav_item">
                <a class="user-nav_link" href="http://smintly.com/#plans">
                    Try for FREE
                </a>
            </li>
            <li class="user-nav_item">
                <a class="user-nav_link" href="http://repucaution.com/affiliate">
                    Affiliate Programm
                </a>
            </li>
            <li class="user-nav_item">
                <a class="user-nav_link nav_link">
                    <?php echo $c_user->username;?>
                </a>
                <ul class="sub_menu clearfix">
                    <li class="user-nav_item">
                        <a class="user-nav_link" href="<?php echo site_url('auth/logout'); ?>"><?= lang('logout') ?></a>
                    </li>
                </ul>
            </li>
            <li class="user-nav_item">
                <select id="user_active_language" class="chosen-select m-t7">
                    <?php foreach($available_languages as $key => $available_language) : ?>
                        <option
                            value="<?= $key ?>"
                            <?= ($available_language == $default_language) ? 'selected="selected"' : '' ?>
                            >
                            <?= ucfirst($available_language) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>
        </ul>
    </div>
    <i class="fa fa-qrcode collapse-button"></i>
</div>