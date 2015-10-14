<?php
/**
 * User: Dred
 * Date: 22.02.13
 * Time: 17:15
 */

/**
 * Class Directory_Parser_Yelp
 *
 * Example url:
 * @link http://www.yelp.com/biz/taylors-sausage-oakland
 *
 * @version 0.1 (27.02.2013)
 */

class Directory_Parser_Yelp extends Directory_Parser {

    protected $review_date_format = 'Y-m-d';

    /**
     * @var int count of review on site-page
     */
    protected $reviewOnPage = 40;

    /*protected $curl_roxies = array(
        '116.112.66.102:808',
        '68.117.55.177:42357',
        '173.213.96.229:3127',
        '186.92.32.47:8080',
        '109.224.6.170:8080',
        '182.48.107.220:9000',
        '182.99.127.29:80',
        '211.144.76.58:9000',
        '190.39.130.150:8080',
        '97.78.180.102:51537',
        '88.150.136.18:1080',
    );*/

    public function get_reviews()
    {
        $this->is_url_set();
        $reviews = array();

        //sorting by date
        $this->url.='?sort_by=date_desc';
        $content = $this->_request($this->url);
        $html = str_get_html($content);

        $revs = $html->find('ul.reviews div[itemprop=review]');
        $countOnPage = count($revs);
        if($html != '' && $countOnPage){
            //count all reviews
            $span = $html->find('ul.feed-language-filters li.selected span.count');
            $count = $span[0]->innertext;
            //count existed reviews
            $exrev = $this->exist_reviews();
            //count next iteration of parse reviews
            $iter = ceil(($count - $exrev)/$this->reviewOnPage)-1;

            $i=0;
            while($iter-$i>=0 ){
                if($i>0){
                    $start = $this->reviewOnPage*$i;
                    $url = $this->url."&start=".$start;
                    $content = $this->_request($url);
                    $html = str_get_html($content);
                }
                if(!$html==''){
                    $revs = $html->find('ul.reviews div[itemprop=review]');
                    foreach($revs as $element){
                        $reviews[] = $this->parse_one_review($element);

                    }
                    $html->clear();
                    unset($html);
                }
                $i++;
            }

        }

        return $reviews;
    }

    protected function _get_uniq_id($element) {
        $id = $element->{'data-review-id'};
        return $id;
    }

    protected function _get_rank($element) {
        $rank_element = $element->find('.review-content meta[itemprop=ratingValue]', 0);
        if(empty($rank_element)) {
            return null;
        }

        return floatval($rank_element->content);
    }

    protected function _get_posted_date($element) {
        $date_element = $element->find('.review-content meta[itemprop=datePublished]', 0);
        if(empty($date_element)) {
            return null;
        }
        $date = $date_element->content;

        return $this->_timestamp_from_date($date);
    }

    protected function _get_text($element) {
        $text_element = $element->find('p[itemprop="description"]', 0);
        if(empty($text_element)) {
            return null;
        }

        return $this->_prepare_text($text_element->innertext);
    }

    protected function _get_author($element) {
        $author_element = $element->find('.user-name', 0);
        if(empty($author_element)) {
            return null;
        }

        return $this->_prepare_text($author_element->innertext);
    }

    public function valid_url($url) {
        $pattern = '/http:\/\/[www]?[a-z]*\.yelp\.[a-z.]*\/biz\//';
        preg_match($pattern, trim($url), $match);

        return (empty($match[0])) ? false : $match[0];

    }


    public function findUrl(){
        return 'http://www.yelp.com';
    }
}