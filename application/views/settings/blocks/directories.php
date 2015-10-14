<!--<div class="span12 box">
    <div class="header span12">
        <span>Directory Settings</span>
    </div>
    <div class="row-fluid">
        <div class="body span12">
            <form action="" method="POST">
                <?php /*if($receive_emails):*/?>
                    <fieldset class="formBox">
                        <div class="sectionTitle no-margin">
                            <input type="checkbox" name="email_notify" id="email_notify" autocomplete="off" <?php /*if($is_notified): */?>checked<?php /*endif;*/?> />
                            I want to receive new reviews by email

                        </div>
                    </fieldset>
                <?php /*endif;*/?>


                <fieldset class="formBox special">
                    <?php /*foreach($directories as $_directory):*/?>

                        <div class="section <?php /*if( end($directories->all) === $_directory) echo 'last'; */?>">
                            <div class="control-group">
                                <label class="control-label" ><?php /*echo $_directory->name;*/?><?php /*if( method_exists($parsers[$_directory->id],'autocomplete')): */?><span class="small-font">&nbsp;(type business name)</span><?php /*endif;*/?></label>
                                <div class="controls <?php /*echo $_directory->cssClass();*/?>">
                                    <input type="text" name="directory[<?php /*echo $_directory->id;*/?>]" value="<?php /*if(!empty($user_directories[$_directory->id]['link'])) echo $user_directories[$_directory->id]['link']; */?>">

                                </div>
                                <?php /*if( method_exists($parsers[$_directory->id],'findUrl')): */?>
                                    <a target="_blank" href="<?php /*echo $parsers[$_directory->id]->findUrl();*/?>" class="find">Find Url</a>
                                <?php /*endif;*/?>

                            </div>
                        </div>
                    <?php /*endforeach;*/?>

                </fieldset>
                <fieldset class="buttBox"><input class="black-btn" type="submit" value="Save"></fieldset>
            </form>
        </div>
    </div>
</div>-->
<h4 class="head_tab">Directory Settings</h4>
<form action="<?php echo site_url('settings/directories');?>" method="POST">
<div class="row">

    <?php if($receive_emails):?>
        <div class="col-xs-12 custom-form m-b10">
            <label class="cb-checkbox">
                <input type="checkbox" name="email_notify" autocomplete="off" <?php if($is_notified): ?>checked<?php endif;?> />
                I want to receive new reviews by email
            </label>
        </div>
    <?php endif;?>
</div>
<div class="row">
    <?php foreach($directories as $_directory):?>
    <div class="col-sm-4 <?php if( end($directories->all) === $_directory) echo 'last'; ?>">
        <p class="text_color strong-size"><?php echo $_directory->name;?><?php if( method_exists($parsers[$_directory->id],'autocomplete')): ?><span class="small-font">&nbsp;(type business name)</span><?php endif;?></p>
        <div class="form-group <?php echo $_directory->cssClass();?>">
            <input class="form-control" name="directory[<?php echo $_directory->id;?>]" value="<?php if(!empty($user_directories[$_directory->id]['link'])) echo $user_directories[$_directory->id]['link']; ?>"/>
        </div>
    </div>
    <?php endforeach;?>

</div>
<div class="row">
    <div class="col-xs-12">
        <button class="btn btn-save m-tb20 pull-right" type="submit">Save</button>
    </div>
</div>
</form>