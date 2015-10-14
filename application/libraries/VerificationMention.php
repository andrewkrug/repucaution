<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dred
 * Date: 10/24/12
 * Time: 5:17 PM
 * To change this template use File | Settings | File Templates.
 */
class VerificationMention {

    /**
     * list of include keywords
     *
     * @var $include_words
     */
    private $include_words;

    /**
     * list of exclude keywords
     *
     * @var $exclude_words
     */
    private $exclude_words;

    /**
     * exact match flg
     *
     * @var $exact
     */
    private $exact;

    /**
     * almost exact match flg
     * case and spec symbols insensitive
     *
     * @var $almost_exact
     */
    // private $almost_exact;

    /**
     * list of keywords
     *
     * @var $keywords
     */
    private $keywords;

    /**
     * construct
     *
     * @param array $rule
     */
    public function __construct($rule = array()){
        $this->setRule($rule);
    }


    /**
     * Mention checks on defined rules
     *
     * @param $input_text
     * @return bool
     */
    public function verificate($input_text){
        return (
            $this->_step_1_check_keywords_in_text($input_text) &&
            $this->_step_2_includes_check($input_text) &&
            $this->_step_3_exclude_check($input_text)
        );
    }


    /**
     * verifies the presence of keywords in the text
     *
     * @param $text
     * @return bool
     */
    private function _step_1_check_keywords_in_text($text){
        if( $this->exact ){
               return $this->__step_1_exact_check($text);
        }
        /* else if ( $this->almost_exact ) {
                return $this->__step_1_almost_exact_check($text);
        }*/
        return $this->__step_1_not_exact_check($text);
    }


    /**
     * verifies the presence of include keywords(addition) in the text
     *
     * @param $text
     * @return bool
     */
    private function _step_2_includes_check($text){
        return $this->_all_words_in_text($text, $this->include_words);
    }

    /**
     * verifies the absence of exclude keywords(addition) in the text
     *
     * @param $text
     * @return bool
     */
    private function _step_3_exclude_check($text){
        return !$this->_one_of_words_in_text($text, $this->exclude_words);
    }


    /**
     * branch: for exact match keywords
     *
     * @param $text
     * @return bool
     */
    private function __step_1_exact_check($text){
        return ( mb_strstr($text, $this->keywords ) !== false );
    }

    /**
     * branch: for ealmost exact match keywords
     * case and special symbols insensitive
     * 
     * @param $text
     * @return bool
     */

    /*private function __step_1_almost_exact_check($text) {
        return ( mb_stristr( $this->_clear_text($text), $this->_clear_text($this->keywords)) !== false );
    }*/

    /**
     * branch: for not exact match keywords
     *
     * @param $text
     * @return bool
     */
    private function __step_1_not_exact_check($text){
        $words = $this->_clear_text($this->keywords);
        $words = explode(' ', $words);
        return $this->_all_words_in_text($text, $words);
    }


    /**
     * checks that all the keywords in the text
     *
     * @param $text
     * @param $words
     * @return bool
     */
    private function _all_words_in_text($text, $words){
        foreach($words as $_word){
            if ( ! $this->exact) {
                return mb_stristr(strtolower($text), strtolower($_word)) !== false;
            } else {
                if( mb_stristr($text, $_word) === false  ){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * checks that one of keywords in the text
     *
     * @param $text
     * @param $words
     * @return bool
     */
    private function _one_of_words_in_text($text, $words){

        foreach($words as $_word){
            if( mb_stristr($text, $_word) !== false  ){
                return TRUE;
            }
        }

        return FALSE;
    }


    /**
     * Set mention's validation rule
     *
     * @param array $rule array('keywords' => string, 'exact' => bool, 'almost_exact' => bool, 'include' => string|array, 'exclude' => string|array)
     * @return VerificationMention
     * @throws Exception
     */
    public function setRule(array $rule){

        $this->clean();

        if( empty($rule['keywords']) ){
            throw new Exception( __CLASS__.': Rule must include keyword!' );
        }

        $this->setKeywords($rule['keywords']);

        // set exact option
        if( isset($rule['exact']) ){
            $this->setExact($rule['exact']);
        }

        // set almost exact option
        /*if( isset($rule['almost_exact']) ){
            $this->setAlmostExact($rule['almost_exact']);
        }*/

        // set include words
        if( !empty($rule['include']) ){
            $this->setIncludeWords( $rule['include'] );
        }

        // set exclude words
        if( !empty($rule['exclude']) ){
            $this->setExcludeWords($rule['exclude']);
        }

        return $this;
    }

    /**
     * Set exact flag
     *
     * @param $exact
     * @return VerificationMention
     */
    public function setExact($exact) {
        $this->exact = (bool)$exact;
        return $this;
    }

    /**
     * Set almost exact flag
     *
     * @param $almost_exact
     * @return VerificationMention
     */
    /*public function setAlmostExact($almost_exact) {
        $this->almost_exact = (bool)$almost_exact;
        return $this;
    }*/

    /**
     * Set Exclude words
     *
     * @param string|array $exclude_words
     * @return VerificationMention
     */
    public function setExcludeWords($exclude_words) {
        switch( gettype($exclude_words) ){
            case "string":
                $this->exclude_words = array( $exclude_words );
                break;
            case "array":
                $this->exclude_words = $exclude_words;
                break;
        }

        return $this;
    }

    /**
     * Set Include words
     *
     * @param string|array $include_words
     * @return VerificationMention
     */
    public function setIncludeWords($include_words) {
        switch( gettype($include_words) ){
            case "string":
                $this->include_words = array( $include_words );
                break;
            case "array":
                $this->include_words = $include_words;
                break;
        }

        return $this;
    }

    /**
     * Set kaywords
     *
     * @param string $keywords
     */
    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }

    /**
     * Clean all class variables
     *
     * @return VerificationMention
     */
    public function clean(){
        $this->include_words = array();
        $this->exclude_words = array();
        $this->exact = false;
        return $this;
    }

    /**
     * Reserves in the text only letters, numbers and spaces
     *
     *
     * @param $text
     * @return mixed
     */
    private function _clear_text($text){
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        $text = preg_replace('/\s{2,}/', ' ', $text);
        return $text;
    }

}
