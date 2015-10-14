
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="head_title"><?= lang('analytics_piwik') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <div class="tab-content settings_content">
                <div class="tab-pane active clearfix" id="web-traffic">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="date_range">
                                <div class="reportrange" >
                                    <i class="fa fa-calendar"></i>
                                    <span></span>
                                    <b class="caret"></b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ga_data">


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script id="web-template" type="text/x-handlebars-template">
    <div class="row">
        <div id="content">

        </div>
    </div>
</script>

<script id="data-template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-sm-4">
            <div class="traffic_block">
                <p class="gray_text">
                    <i class="ti-user"></i>
                    <span class="gray_text_big">{{visits}}</span> <?= lang('visits') ?>
                </p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="traffic_block">
                <p class="green_text">
                    <i class="ti-alarm-clock"></i>
                    <span class="green_text_big">{{unique_visits}}</span> <?= lang('unique_visits') ?>
                </p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="traffic_block">
                <p class="pink_text">
                    <i class="ti-time"></i>
                    <span class="pink_text_big">{{average_visit_duration}}</span> <?= lang('average_visit_duration') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8">
            <div class="diagrammBox traffic_shedule" id="chartbox">

            </div>
        </div>
        <div class="col-sm-4">
            <div class="traffic_block">
                <p class="blue_text">
                    <span class="blue_text_big">{{bounce_rate}}</span> <?= lang('bounce_rate') ?>
                </p>
                <div class="progress-pie" data-percent="{{bounce_rate}}">
                    <div class="ppc-progress">
                        <div class="ppc-progress-fill"></div>
                    </div>
                    <div class="ppc-percents">
                        <div class="pcc-percents-wrapper"><span>%</span></div>
                    </div>
                </div>
            </div>
        </div>
<!--        <div class="col-sm-4">-->
<!--            <div class="traffic_block">-->
<!--                <p class="light_blue_text">-->
<!--                    <span class="light_blue_text_big">{{new_visits_percent}}%</span> --><?//= lang('new_visits') ?>
<!--                </p>-->
<!--                <div class="progress-pie" data-percent="{{new_visits_percent}}">-->
<!--                    <div class="ppc-progress">-->
<!--                        <div class="ppc-progress-fill"></div>-->
<!--                    </div>-->
<!--                    <div class="ppc-percents">-->
<!--                        <div class="pcc-percents-wrapper"><span>%</span></div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</script>

<script id="alert-template" type="text/x-handlebars-template">
    <div class="notification notify_bad">
        <div class="container">
            <p class="notify_text"><i class="fa fa-bad"></i>{{message}}</p>
            <i class="fa fa-remove close_block"></i>
        </div>
    </div>
</script>


