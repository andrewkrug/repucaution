<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_147Drop_theme_tables extends CI_Migration
{


    public function up()
    {

        $this->dbforge->drop_table('themes_templates_tags');
        $this->dbforge->drop_table('themes_userdata');
        $this->dbforge->drop_table('themes_templates');
        $this->dbforge->drop_table('themes_layouts');
        $this->dbforge->drop_table('themes_themes');
        $this->dbforge->drop_table('tags');
    }

    public function down()
    {
    }
}
