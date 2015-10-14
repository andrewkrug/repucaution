<!--<div class="span12 box">
    <div class="header span12">
        <span>keyword phrases</span>
    </div>
    <div class="row-fluid">
        <div class="body span12">
            <form action="<?php /*echo site_url('settings/keywords'); */?>" method="POST">
                <fieldset class="formBox special">
                    <div class="title"><span class="dop">Set Company Address for search engine tracking. <br />Start typing your Company Name and autocomplete will show available results.</span></div>
                    <div class="control-group" style="margin-bottom: 30px;">
                        <label for="address" class="control-label keyword">
                            Company name + address (Google Places)
                            <?php /*if (isset($errors['address'])): */?>
                                <span class="message-error"><?php /*echo $errors['address']; */?></span>
                            <?php /*endif; */?>
                        </label>
                        <div class="controls">
                            <input type="text" name="address" id="address" value="<?php /*echo $address_name; */?>">
                            <input type="hidden" name="address_id" id="address_id" value="<?php /*echo $address_id; */?>">
                            <?php /*if ($address_name): */?>
                                <span class="title keyword_clear"><a class="dop" href="javascript: void(0)" tabindex="-1">Clear</a></span>
                            <?php /*endif; */?>
                        </div>
                    </div>
                    <div class="title"><span class="dop">Choose <?php /*echo $keywords_count; */?> keyword phrases for search engine tracking</span></div>
                    <?php /*for($i = 1; $i <= $keywords_count; $i++): */?>
                        <div class="control-group">
                            <label for="keyword_<?php /*echo $i; */?>" class="control-label keyword">
                                Keyword phrase <?php /*echo $i; */?>
                                <?php /*if (isset($errors['keywords'][$i-1])): */?>
                                    <span class="message-error"><?php /*echo $errors['keywords'][$i-1]['keyword']; */?></span>
                                <?php /*endif; */?>
                            </label>
                            <div class="controls">
                                <input type="text" name="keywords[<?php /*echo $i; */?>]" id="keyword_<?php /*echo $i; */?>" value="<?php /*if (isset($keywords_names[$i-1])) echo $keywords_names[$i-1]; */?>">
                                <?php /*if (isset($keywords_names[$i-1])): */?>
                                    <span class="title keyword_clear"><a class="dop" href="javascript: void(0)" tabindex="-1">Clear</a></span>
                                <?php /*endif; */?>
                            </div>
                        </div>
                    <?php /*endfor; */?>
                </fieldset>
                <fieldset class="buttBox"><input class="black-btn" type="submit" value="Save"></fieldset>
            </form>
        </div>
    </div>
</div>

<script id="clear-template" type="text/x-handlebars-template">
    <span class="title keyword_clear"><a class="dop" href="javascript: void(0)" tabindex="-1">Clear</a></span>
</script>
-->
<h4 class="head_tab">Google Places Keywords</h4>
<form action="<?php echo site_url('settings/keywords'); ?>" method="POST">
    <p class="black strong-size">Set Company Address for search engine tracking. Start typing your Company Name and autocomplete will show available results.</p>
    <div class="row">
        <div class="col-xs-12">
            <p class="text_color strong-size">Company name + address (Google Places)</p>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <input class="form-control m-b10"  name="address" id="address" value="<?php echo $address_name; ?>"/>
                <input type="hidden" name="address_id" id="address_id" value="<?php echo $address_id; ?>">
                <i class="fa fa-times clear"></i>
                <?php if (isset($errors['address'])): ?>
                <span class="message-error"><?php echo $errors['address']; ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <p class="black strong-size">Choose 10 keyword phrases for search engine tracking</p>
    <div class="row">
        <?php for($i = 1; $i <= $keywords_count; $i++): ?>
        <div class="col-sm-4">
            <p class="text_color strong-size">Keyword phrase <?php echo $i; ?>
                <?php if (isset($errors['keywords'][$i-1])): ?>
                    <span class="message-error"><?php echo $errors['keywords'][$i-1]['keyword']; ?></span>
                <?php endif; ?>
            </p>
            <div class="form-group">
                <input class="form-control m-b10"name="keywords[<?php echo $i; ?>]" id="keyword_<?php echo $i; ?>" value="<?php if (isset($keywords_names[$i-1])) echo $keywords_names[$i-1]; ?>"/>
                <i class="fa fa-times clear"></i>
            </div>
        </div>
        <?php endfor; ?>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button class="btn btn-save m-tb20 pull-right">Save</button>
        </div>
    </div>
</form>