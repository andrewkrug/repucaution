<!--<div class="span12 box">
    <div class="header span12">
        <span>social mentions keyword phrases</span>
    </div>
    <div class="row-fluid">
        <div class="body span12">
            <form action="<?php /*echo site_url('settings/mention_keywords'); */?>" method="POST">
                <fieldset class="formBox">
                    <div class="control-group">
                        <div class="sectionTitle">
                            Track mentions of your company in social networks
                            <input class="line black-btn mentions_keywords_add_btn" type="button" value="+ Add keyword">
                        </div>
                        <?php /*$i = 1; */?>
                        <?php /*foreach ($keywords as $keyword): */?>
                            <?php /*$id = $keyword->id ? $keyword->id : 'new_' . $i; */?>
                            <div class="mentions_keywords_block">
                            <span class="num">
                                <?php /*echo $i; */?>.
                            </span>
                                <div class="section-box">
                                    <input type="text" class="long"
                                           name="keyword[<?php /*echo $id; */?>]"
                                           value="<?php /*echo HTML::chars($keyword->keyword); */?>"
                                        >
                                    <a href="javascript: void(0)" class="mentions_keywords_delete">
                                        <i class="delete"></i>
                                    </a>
                                    <div class="control ch-line">
                                        <input type="checkbox" id="keyword_exact_<?php /*echo $id */?>"
                                               name="exact[<?php /*echo $id; */?>]"
                                               <?php /*if ($keyword->exact): */?>checked="checked"<?php /*endif; */?>
                                            >
                                        <label for="keyword_exact_<?php /*echo $id */?>">Exact</label>
                                        <div class="include-exclude">
                                            <a href="javascript:void(0)" class="mentions_keywords_include_exclude">
                                                Include / Exclude
                                                <i class="arrow"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="include-exclude-box clearfix mentions_keywords_include_exclude_holder">
                                        <div class="control col">
                                            <label for="mentions_keywords_include_<?php /*echo $id */?>">Include +</label>
                                            <textarea id="mentions_keywords_include_<?php /*echo $id */?>"
                                                      name="include[<?php /*echo $id; */?>]"
                                                      placeholder="Comma-separated words..."><?php /*echo HTML::chars($keyword->get_other_fields('include', TRUE)); */?></textarea>
                                        </div>

                                        <div class="control col">
                                            <label for="mentions_keywords_exclude_<?php /*echo $id */?>">Exclude -</label>
                                            <textarea id="mentions_keywords_exclude_<?php /*echo $id */?>"
                                                      name="exclude[<?php /*echo $id; */?>]"
                                                      placeholder="Comma-separated words..."><?php /*echo HTML::chars($keyword->get_other_fields('exclude', TRUE)); */?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php /*$i += 1; */?>
                        <?php /*endforeach; */?>
                </fieldset>
                <fieldset class="buttBox clearfix">
                    <input class="black-btn" style="float:right; margin-right:20px;"
                           name="submit" type="submit" value="Save"
                        >
                </fieldset>
            </form>
        </div>
    </div>
</div>

<script id="keyword-template" type="text/x-handlebars-template">
    <div class="mentions_keywords_block">
        <span class="num">
            {{ index }}.
        </span>
        <div class="section-box">
            <input type="text" class="long" name="keyword[{{ id }}]" value="">
            <a href="javascript: void(0)" class="mentions_keywords_delete">
                <i class="delete"></i>
            </a>
            <div class="control ch-line">
                <input type="checkbox" id="keyword_exact_{{ id }}" name="exact[{{ id }}]" >
                <label for="keyword_exact_{{ id }}">Exact</label>
                <div class="include-exclude">
                    <a href="javascript:void(0)" class="mentions_keywords_include_exclude">
                        Include / Exclude
                        <i class="arrow"></i>
                    </a>
                </div>
            </div>
            <div class="include-exclude-box clearfix mentions_keywords_include_exclude_holder">
                <div class="control col">
                    <label for="mentions_keywords_include_{{ id }}">Include +</label>
                    <textarea id="mentions_keywords_include_{{ id }}"
                              name="include[{{ id }}]"
                              placeholder="Comma-separated words..."></textarea>
                </div>

                <div class="control col">
                    <label for="mentions_keywords_exclude_{{ id }}">Exclude -</label>
                    <textarea id="mentions_keywords_exclude_{{ id }}"
                              name="exclude[{{ id }}]"
                              placeholder="Comma-separated words..."></textarea>
                </div>
            </div>
        </div>
    </div>
</script>
-->
<h4 class="head_tab">Social Mentions Keyword Phrases</h4>
<div class="row custom-form">
    <div class="col-xs-12">
        <p class="text_color strong-size pull-sm-left p-t5">
            Track mentions of your company in social networks
        </p>
        <button id="add_mention" class="btn btn-add pull-sm-right">+ Add keyword</button>
    </div>
    <div class="col-xs-12">
        <div class="row hidden insert_block">
            <div class="col-sm-7">
                <div class="form-group">
                    <input class="form-control m-b10" name="keyword[]" value="">
                    <i class="cb-remove"></i>
                    <div class="clearfix">
                        <label class="cb-checkbox text-size pull-sm-left">
                            <input type="checkbox" id="keyword_exact_" name="exact[]">
                            Exact
                        </label>
                        <div class="pull-sm-right">
                            <a href="" class="link show_block">Include / Exclude</a>
                        </div>
                    </div>
                    <div class="toggle_block row">
                        <div class="col-sm-6">
                            <p class="text_color">Include +</p>
                            <div class="form-group">
                                <textarea class="form-control" placeholder="Comma-separated words..."
                                          id="mentions_keywords_include_"
                                          name="include[]"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <p class="text_color">Exclude -</p>
                            <div class="form-group">
                                <textarea class="form-control" placeholder="Comma-separated words..."
                                          id="mentions_keywords_exclude_"
                                          name="exclude[]"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 m-t20">
        <form id="mention_form" action="<?php echo site_url('settings/mention_keywords'); ?>" method="POST">
        <?php $i = 1; ?>
        <?php foreach ($keywords as $keyword): ?>
        <?php $id = $keyword->id ? $keyword->id : 'new_' . $i; ?>
        <div class="row past_block">
            <div class="col-sm-7">
                <div class="form-group">
                    <input class="form-control m-b10" name="keyword[<?php echo $id; ?>]"
                           value="<?php echo HTML::chars($keyword->keyword); ?>">
                    <i class="cb-remove"></i>
                    <div class="clearfix">
                        <label class="cb-checkbox text-size pull-sm-left">
                            <input type="checkbox" name="exact[<?php echo $id; ?>]"
                                   <?php if ($keyword->exact): ?>checked="checked"<?php endif; ?>
                                >
                            Exact
                        </label>
                        <div class="pull-sm-right">
                            <a href="" class="link show_block">Include / Exclude</a>
                        </div>
                    </div>
                    <div class="toggle_block row">
                        <div class="col-sm-6">
                            <p class="text_color">Include +</p>
                            <div class="form-group">
                                <textarea class="form-control" placeholder="Comma-separated words..."
                                          id="mentions_keywords_include_<?php echo $id ?>"
                                          name="include[<?php echo $id; ?>]"><?php echo HTML::chars($keyword->get_other_fields('include', TRUE)); ?></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <p class="text_color">Exclude -</p>
                            <div class="form-group">
                                <textarea class="form-control" placeholder="Comma-separated words..."
                                          id="mentions_keywords_exclude_<?php echo $id ?>"
                                          name="exclude[<?php echo $id; ?>]"><?php echo HTML::chars($keyword->get_other_fields('exclude', TRUE)); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $i++;?>
        <?php endforeach;?>
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-save m-tb20 pull-right">Save</button>
                </div>
            </div>

        </form>
    </div>
</div>
