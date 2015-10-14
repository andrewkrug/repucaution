
<?php if($flashes = $this->template->getFlashes()): ?>
    <?php foreach($flashes as $flash): ?>
        <div class="notification <?=
            ($flash['type'] == 'success') ?
                'notify_good' :
                (($flash['type'] == 'error') ?
                    'notify_bad' :
                    'notify_'.$flash['type'])
        ?>">
            <div class="container">
                <p class="notify_text">
                    <?php if($flash['type'] == 'success') : ?>
                        <i class="fa fa-check"></i>
                    <?php elseif($flash['type'] == 'error') : ?>
                        <i class="fa fa-exclamation-triangle"></i>
                        <?= lang('warring') ?>:
                    <?php endif; ?>
                    <?= $flash['message']; ?>
                    <i class="fa fa-remove close_block"></i>
                </p>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<script>
    $(document).ready(function(){
        $('.notification').delay(2000).slideUp();
    })
</script>