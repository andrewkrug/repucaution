<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Change_industries  extends CI_Migration {

    private $_table_rss_industries = 'rss_industries';
    private $_table_rss_feeds = 'rss_feeds';

    public function up() {

        $this->dbforge->drop_column($this->_table_rss_industries, 'user_id');

        $sql = "DROP INDEX rss_feeds_link_category_id_UNIQUE ON " . $this->db->dbprefix 
            . $this->_table_rss_feeds . ";";
        $this->db->query($sql);

        $fields = array(
            'category_id' => array(
                'name' => 'industry_id',
                'type' => 'INT',
                'null' => TRUE,
                'unsigned' => TRUE,
            ),
        );

        $this->dbforge->modify_column($this->_table_rss_feeds, $fields);

        $sql = "CREATE UNIQUE INDEX rss_feeds_link_industry_id_UNIQUE ON " 
            . $this->db->dbprefix . $this->_table_rss_feeds . "(link ASC, industry_id ASC);";
        $this->db->query($sql);

        // insert test industries
        $industries = array(
            array('1', 'Apple Top 10 Songs'),
            array('2', 'HP News'),
            array('3', 'Oracle Corporate News'),
            array('4', 'Forbes Popular Stories'),
        );
        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table_rss_industries . "` (`id`, `name`) VALUES ";
        foreach($industries as $key => $value) {
            $sql .= '("' . implode('","', $value) . '"),';
        }
        $sql = substr($sql, 0, -1);
        $this->db->query($sql);

        // insert test feeds for industries
        $feeds = array(
            array('1', 'Apple Top 10 Songs', 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/ws/RSS/topsongs/limit=10/xml', '1'),
            array('2', 'HP News', 'http://www.hp.com/hpinfo/news.xml', '2'),
            array('3', 'Oracle Corporate News', 'http://www.oracle.com/ocom/groups/public/@ocom/documents/webcontent/196280.xml', '3'),
            array('4', 'Forbes Popular Stories', 'http://www.forbes.com/fast/feed', '4'),
        );

        $sql = "INSERT INTO `" . $this->db->dbprefix 
            . $this->_table_rss_feeds . "` (`id`, `title`, `link`, `industry_id`) VALUES ";
        foreach($feeds as $key => $value) {
            $sql .= '("' . implode('","', $value) . '"),';
        }
        $sql = substr($sql, 0, -1);
        $this->db->query($sql);
    }

    public function down() {

    }

}