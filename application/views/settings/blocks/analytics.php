<!--<div class="span12 box">
    <div class="header span12">
        <span>Analytics details</span>
    </div>
    <div class="row-fluid">
        <div class="body span12">
            <form class="ga_form">
                <?php /*if ( ! $access_token->token2): */?>
                    <fieldset class="formBox">
                        <div class="title">Google Analytics <br/><span class="dop">Connect your Google Analytics account to get statistics</span></div>
                    </fieldset>
                    <fieldset class="buttBox">
                        <input class="black-btn" type="submit" value="Connect" id="connect">
                    </fieldset>
                <?php /*else: */?>
                    <fieldset class="buttBox pull-right" style="margin: 15px 30px 0px 0px">
                        <a class="black-btn ga_logout" href="<?php /*echo site_url('settings/analytics/logout'); */?>">
                            Logout
                        </a>
                    </fieldset>
                    <?php /*if ($access_token->instance_id): */?>
                        <fieldset class="formBox">
                            <div class="title">Google Analytics <br/>
                                <?php /*if ( ! empty($account_info)): */?>
                                    <span class="dop">
                                        Account:
                                        <strong onclick="return false;">
                                            <?php /*echo HTML::chars($account_info['account_name']); */?>
                                        </strong>
                                    </span>
                                    <br />
                                    <span class="dop">
                                        Webproperty:
                                        <strong onclick="return false;">
                                            <?php /*echo HTML::chars($account_info['webproperty_name']); */?>
                                        </strong>
                                    </span>
                                    <br />
                                    <span class="dop">
                                        Profile:
                                        <strong onclick="return false;">
                                            <?php /*echo HTML::chars($account_info['profile_name']); */?>
                                        </strong>
                                    </span>
                                    <br />
                                <?php /*endif; */?>
                            </div>
                        </fieldset>
                        <fieldset class="buttBox">
                            <a class="black-btn pull-right" href="<?php /*echo site_url('settings/analytics/accounts'); */?>">
                                Change
                            </a>
                        </fieldset>
                    <?php /*else: */?>
                        <fieldset class="formBox">
                            <div class="title">
                                Google Analytics
                                <br/>
                                <span class="dop">Select your Google Analytics profile to get statistics</span>
                            </div>
                        </fieldset>
                        <fieldset class="buttBox">
                            <a class="blue-btn" href="<?php /*echo site_url('settings/analytics/accounts'); */?>">
                                Select
                            </a>
                        </fieldset>
                    <?php /*endif; */?>

                <?php /*endif; */?>
            </form>
        </div>
    </div>
</div>-->
<h4 class="head_tab">Analytics Details</h4>
<div class="row">
    <div class="col-sm-6">
        <p class="text_color strong-size">Google Analytics </p>
        <p class="black smallText">
            <?php if ( ! $access_token->token2): ?>
                Connect your Google Analytics account to get statistics
            <?php else:?>
                Select your Google Analytics profile to get statistics
            <?php endif;?>
        </p>
    </div>

    <div class="col-sm-6">
        <div class="pull-sm-right">
            <?php if ( ! $access_token->token2): ?>
                <button class="btn btn-add">Connect</button>
            <?php else:?>
                <button class="btn btn-add">Logout</button>
            <?php endif;?>
        </div>
    </div>


</div>
<!--<div class="row">
    <div class="col-xs-12">
        <div class="b-Top clearfix m-b10 m-t20 p-t20">
            <div class="pull-sm-right">
                <button class="btn btn-save">Select</button>
            </div>
        </div>
    </div>
</div>-->