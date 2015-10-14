<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Install_themes extends CI_Migration {

	public function up()
	{

        $this->db->query("CREATE TABLE `".$this->db->dbprefix."themes_themes` (
                          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                          `name` VARCHAR(100) NOT NULL,
                          `is_active` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                          `date_installed` INT UNSIGNED NOT NULL,
                          `config_data` TEXT NOT NULL,
                          PRIMARY KEY (`id`),
                          UNIQUE INDEX `name_UNIQUE` (`name` ASC));");

        $this->db->query("CREATE TABLE `".$this->db->dbprefix."themes_layouts` (
                          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `theme_id` int(10) unsigned NOT NULL,
                          `name` varchar(100) NOT NULL,
                          `is_active` smallint(5) unsigned NOT NULL DEFAULT '1',
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `theme_id_UNIQUE` (`theme_id`,`name`),
                          KEY `FK_LAYOUTS_TO_THEMES_idx` (`theme_id`),
                          CONSTRAINT `FK_LAYOUTS_TO_THEMES` FOREIGN KEY (`theme_id`)
                          REFERENCES `".$this->db->dbprefix."themes_themes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)");

        $this->db->query("CREATE TABLE `".$this->db->dbprefix."themes_templates` (
                          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                          `layout_id` INT UNSIGNED NOT NULL,
                          `name` VARCHAR(100) NOT NULL,
                          `is_active` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
                          `html` MEDIUMTEXT NOT NULL,
                          PRIMARY KEY (`id`),
                          UNIQUE INDEX `layout_id_UNIQUE` (`layout_id` ASC, `name` ASC),
                          CONSTRAINT `FK_TEMPLATES_TO_LAYOUTS`
                            FOREIGN KEY (`layout_id`)
                            REFERENCES `".$this->db->dbprefix."themes_layouts` (`id`)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE);");

        $this->db->query("CREATE TABLE `".$this->db->dbprefix."themes_userdata` (
                          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                          `template_id` int(10) unsigned NOT NULL,
                          `user_id` int(11) NOT NULL,
                          `tab_id` varchar(45) NOT NULL,
                          `node_identity` varchar(100) NOT NULL,
                          `value_type` enum('array','string','integer','float') NOT NULL DEFAULT 'string',
                          `value` text NOT NULL,
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `template_id_UNIQUE` (`template_id`,`user_id`,`tab_id`,`node_identity`),
                          CONSTRAINT `FK_USERDATA_TO_TEMPLATES` FOREIGN KEY (`template_id`) REFERENCES `".$this->db->dbprefix."themes_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                        )");

        $this->db->query("CREATE TABLE `".$this->db->dbprefix."themes_templates_tags` (
                          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
                          `tag_id` INT UNSIGNED NOT NULL ,
                          `template_id` INT UNSIGNED NOT NULL ,
                          PRIMARY KEY (`id`) ,
                          INDEX `FK_THEMES_TEMPLATES_TO_TAGS_idx` (`tag_id` ASC) ,
                          INDEX `FK_THEMES_TEMPLATES_TO_TEMPLATES_idx` (`template_id` ASC) ,
                          UNIQUE INDEX `tag_id_UNIQUE` (`tag_id` ASC, `template_id` ASC) ,
                          CONSTRAINT `FK_THEMES_TEMPLATES_TO_TAGS`
                            FOREIGN KEY (`tag_id` )
                            REFERENCES `".$this->db->dbprefix."tags` (`id` )
                            ON DELETE CASCADE
                            ON UPDATE CASCADE,
                          CONSTRAINT `FK_THEMES_TEMPLATES_TO_TEMPLATES`
                            FOREIGN KEY (`template_id` )
                            REFERENCES `".$this->db->dbprefix."themes_templates` (`id` )
                            ON DELETE CASCADE
                            ON UPDATE CASCADE);");
	}

	public function down()
	{
        $this->dbforge->drop_table('themes_templates_tags');
        $this->dbforge->drop_table('themes_userdata');
        $this->dbforge->drop_table('themes_templates');
        $this->dbforge->drop_table('themes_layouts');
        $this->dbforge->drop_table('themes_themes');
	}
}
