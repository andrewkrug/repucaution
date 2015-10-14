<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Insert_default_features extends CI_Migration {

    private $_table = 'features';
    private $_plans_relation_table = 'plans_features';

    public function up() {

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_table . "` (`name`, `countable`) VALUES
                    ('Twitter Integration', 1),
                    ('Facebook Integration', 1),
                    ('Youtube Integration', 1);";

        $this->db->query($sql);

        $sql = "INSERT INTO `" . $this->db->dbprefix . $this->_plans_relation_table 
            . "` (`plan_id`, `feature_id`, `count`) VALUES
                    (1, 1, 0),
                    (1, 2, 0),
                    (1, 3, 0);";

        $this->db->query($sql);
    }

    public function down() {

    }

}