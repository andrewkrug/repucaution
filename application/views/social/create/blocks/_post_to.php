<?php
/**
 * @var array $socials
 */
if(isset($social_post)) {
    $activeSocials = unserialize($social_post->post_to_socials);
} else {
    $activeSocials = $socials;
}
$inactiveSocials = Social_post::$socials;
foreach($inactiveSocials as $key => $inactiveSocial) {
    if(in_array($inactiveSocial, $activeSocials)) {
        unset($inactiveSocials[$key]);
    }
}
?>
<p class="text_color strong-size"><?= lang('post_to') ?>
    <span class="custom-form is-relative top-5 p-l10">
        <?php foreach($socials as $social) : ?>
            <label class="cb-checkbox regRoboto m-r10">
                <input
                    type="checkbox"
                    name="post_to_socials[]"
                    value="<?= $social ?>"
                    <?= (in_array($social, $activeSocials)) ? 'checked="checked"' : ''; ?>
                    >
                <cite class="ti-<?= $social ?>"></cite>
                <?= ucfirst($social) ?>
            </label>
        <?php endforeach; ?>
        <?php foreach($inactiveSocials as $inactiveSocial) : ?>
            <p>
                Please add <a href="<?= site_url('settings/socialmedia/'); ?>"><?= ucfirst($inactiveSocial) ?> account.</a>
            </p>
        <?php endforeach; ?>
    </span>
</p>