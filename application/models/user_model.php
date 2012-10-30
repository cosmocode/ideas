<?php

class User_model extends CI_Model {

    /**
     * @var object current User
     */
    public $current = null;

    public function __construct() {
        // if session exists, try a session login here
        if($this->session->userdata('magic')) $this->session_login($this->session->userdata('magic'));
    }

    /**
     * Logs out a user by removing her session magic
     *
     * @return bool always true
     */
    public function logout() {
        $this->session->unset_userdata('magic');
        return true;
    }

    /**
     * Login a user via Active Directory
     *
     * @param $login
     * @param $password
     * @return bool
     */
    public function login($login, $password) {
        $this->config->load('adldap', true);

        require_once APPPATH.'third_party/adLDAP/adLDAP.php';
        $adldap = new adLDAP($this->config->item('adldap'));

        $authok = $adldap->user()->authenticate($login, $password);
        if(!$authok) {
            log_message('error', 'adLDAP: '.$adldap->getLastError());
            return false;
        }

        // fetch user data
        $userinfo = $adldap->user()->info($login);
        $groups   = $adldap->user()->groups($login);

        // arrays are weird
        $email  = '';
        $name   = $login;
        $groups = array();
        if(isset($userinfo[0]['email'][0])) $email = $userinfo[0]['email'][0];
        if(isset($userinfo[0]['displayname'][0])) $name = $userinfo[0]['displayname'][0];

        $role = 0;

        // create magic token
        $magic = sha1(rand());
        $this->session->set_userdata('magic', $magic);

        // todo calculate role from group memberships

        // create/update user info mirror
        $sql = "REPLACE INTO user
                    SET login = ?,
                        email = ?,
                        fullname = ?,
                        role = ?,
                        magic = ?";
        $this->db->query(
            $sql,
            array(
                 $login,
                 $email,
                 $name,
                 $role,
                 $magic
            )
        );

        return true;
    }

    /**
     * Silently login a user by his magic session ID
     *
     * @param $magic
     */
    public function session_login($magic) {
        $sql   = "SELECT * FROM user WHERE magic = ?";
        $query = $this->db->query($sql, array($magic));
        $user  = $query->row();
        if($user) $this->current = $user;
    }

    /**
     * Return a certain user from the local mirror
     *
     * @param $login
     * @return object
     */
    public function get($login) {
        $sql   = "SELECT * FROM user WHERE login = ?";
        $query = $this->db->query($sql, array($login));
        return $query - row();
    }

}