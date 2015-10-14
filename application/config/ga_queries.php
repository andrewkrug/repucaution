<?php

$config['queries'] = array(

  // ALL TRAFFIC SOURCES
    'web' => array(
        'chart' => array(
            'ga:sessions' => array(
                'dimensions' => 'ga:year,ga:month,ga:day',
                'max-results' => 366
            )
        ),
        'table' => array(
           'ga:sessions,ga:visitors,ga:bounceRate,ga:percentNewVisits,ga:avgSessionDuration' => array(
               'max-results' => 10,
           )
        ),
        'headers' => array(
            'visits', 'unique_visits', 'bounce_rate', 'new_visits_percent', 'average_visit_duration'
        ),
        'values' => array(
            'int', 'int', 'percent', 'float', 'time',
        )
    ),

    // SEARCH
    'search' => array(
        'caption' => lang('top_keywords'),
        'table' => array(
           'ga:sessions,ga:avgSessionDuration,ga:percentNewVisits,ga:bounceRate' => array(
               'dimensions' => 'ga:keyword',
               'max-results' => 10,
               'sort' => '-ga:sessions',
               'filters' => 'ga:keyword!=(not set)',
           )
        ),
        'headers' => array(
            'keyword', 'visits', 'average_visit_duration', '%_new_visits', 'bounce_rate',
        ),
        'values' => array(
            'string', 'int', 'time', 'percent', 'percent',
        ),
    ),

    // REFERRAL
    'referral' => array(
        'caption' => lang('top_websites'),
        'table' => array(
           'ga:sessions,ga:avgSessionDuration,ga:percentNewVisits,ga:bounceRate' => array(
               'dimensions' => 'ga:source',
               'max-results' => 10,
               'sort' => '-ga:sessions',
               'filters' => 'ga:medium==referral'
           )
        ),
        'headers' => array(
            'source', 'visits', 'average_visit_duration', '%_new_visits', 'bounce_rate'
        ),
        'values' => array(
            'string', 'int', 'time', 'percent', 'percent'
        )
    ),

    // DIRECT
    'direct' => array(
        'caption' => lang('top_pages'),
        'table' => array(
           'ga:sessions,ga:avgSessionDuration,ga:percentNewVisits,ga:bounceRate' => array(
               'dimensions' => 'ga:landingPagePath',
               'max-results' => 10,
               'sort' => '-ga:sessions',
               'filters' => 'ga:source==(direct)',
           )
        ),
        'headers' => array(
            'page', 'visits', 'average_visit_duration', '%_new_visits', 'bounce_rate'
        ),
        'values' => array(
            'string', 'int', 'time', 'percent', 'percent'
        ),
    ),

    // ADWORDS
    'adwords' => array(
        'caption' => lang('top_keywords'),
        'table' => array(
           'ga:sessions,ga:avgSessionDuration,ga:percentNewVisits,ga:bounceRate' => array(
               'dimensions' => 'ga:keyword,ga:medium',
               'max-results' => 10,
               'sort' => '-ga:sessions',
               'filters' => 'ga:medium==cpa,ga:medium==cpc,ga:medium==cpm,ga:medium==cpp,ga:medium==cpv,ga:medium==ppc',
           )
        ),
        'headers' => array(
            'source', 'visits', 'average_visit_duration', '%_new_visits', 'bounce_rate'
        ),
        'values' => array(
            'string', 'int', 'time', 'percent', 'percent'
        )
    ),

    // SOCIAL
    'social' => array(
        'caption' => lang('social'),
        'table' => array(
            'ga:sessions,ga:avgSessionDuration,ga:percentNewVisits,ga:bounceRate' => array(
                'dimensions' => 'ga:source',
                'max-results' => 10,
                'sort' => '-ga:sessions',
                'filters' => 'ga:hasSocialSourceReferral==Yes'
            )
        ),
        'headers' => array(
            'source', 'visits', 'average_visit_duration', '%_new_visits', 'bounce_rate'
        ),
        'values' => array(
            'string', 'int', 'time', 'percent', 'percent'
        )
    ),
);