<div>
    You have been invited to special subscription plan of <?php echo $sitename;?>:</div><br/>
    <h2><?php echo $plan->name; ?></h2>
    <?php $features = $plan->getAttachedFeatures();?>
    <ul>
    <?php foreach ($features as $feature) :?>
        <li><?php echo $feature->getFeature()->name;?></li>
    <?php endforeach; ?>
    </ul><br/>
    If you are currently our member, please proceed to <a href="<?php echo $auth_link;?>">log in page</a>.
    Otherwise <a href="<?php echo $register_link;?>">register a new account</a>.
</div>