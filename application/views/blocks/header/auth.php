<?php
/**
 * @var bool $paymentsEnabled
 */
?>
<div class="navbar navbar-fixed-top header">
    <div class="logo">
        <a href="<?php echo base_url(); ?>" class="logo_link">
            Repucaution
        </a>
    </div>
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
        <?php if(!isset($showHeaderLinks) || $showHeaderLinks): ?>
            <li class="user-nav_item">
                <a class="user-nav_link signIn" href="<?php echo site_url('auth/login'); ?>">
                    SIGN IN
                </a>
            </li>
            <?php if($paymentsEnabled): ?>
                <li class="user-nav_item">
                    <a class="user-nav_link signIn" href="<?php echo site_url('auth/plans'); ?>">
                        SIGN UP
                    </a>
                </li>
            <?php else: ?>
                <li class="user-nav_item">
                    <a class="user-nav_link signIn" href="<?php echo site_url('auth/register'); ?>">
                        SIGN UP
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
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