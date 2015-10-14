<?php if ( ! defined('BASEPATH')) die('No direct script access allowed');

class Migration_Set_superadmin extends CI_Migration {

    private $_table = 'users';
    private $_groups_table = 'users_groups';

    private $_first_name = 'super';
    private $_last_name = 'admin';
    private $_email = 'super@admin.com';
    private $_password = 'password';
    private $_groups = array(1); // ionauth default admin group

    public function up() {

        // remove all existing users from admin group
        $users = $this->ion_auth->users()->result();
        foreach ($users as $user) {
            $this->ion_auth->remove_from_group($this->_groups, $user->id);
        }
        
        $username = strtolower($this->_first_name) . ' ' . strtolower($this->_last_name);

        $additional_data = array(
            'first_name' => $this->_first_name,
            'last_name'  => $this->_last_name,
        );

        $this->ion_auth->register($username, $this->_password, $this->_email, $additional_data, $this->_groups);
    }

    public function down() {
        $sql = "SELECT * FROM " . $this->db->dbprefix . $this->_table . "
                 WHERE email = ?";
        $query = $this->db->query($sql, array($this->_email));

        if ($query->num_rows() > 0) {
            $row = $query->row(); 
            $this->ion_auth->delete_user($row->id);
        }
    }
}