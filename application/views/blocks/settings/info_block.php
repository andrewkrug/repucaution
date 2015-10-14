<?php
/**
 * @var bool $need_info
 */
?>
<?php if($need_info): ?>
    <div class="validate blue m-b10">
        <div class="validateRow">
            <div class="validateCell">
                <i class="note">!</i>
            </div>
            <div class="validateCell">
                <div class="pull-left">
                    <p>
                        <?= lang('info_about_profile') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div></div>
<?php endif ?>
