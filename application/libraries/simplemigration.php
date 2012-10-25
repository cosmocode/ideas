<?php

class SimpleMigration {

    private $ci;

    private $migrationdir = 'sql'; //FIXME make configurable

    public function __construct(){
        $this->ci =& get_instance();

        if(!$this->ci->db->table_exists('migration')){
            $this->init();
        }

        $migrations = $this->getMigrations();
        foreach($migrations as $version => $file){
            $this->applyMigration($version,$file);
        }
    }

    /**
     * Initialize the migration table
     */
    private function init(){
        $sql = "CREATE TABLE IF NOT EXISTS migration (
                    version INT PRIMARY KEY,
                    dt  DATETIME
                )";
        $this->ci->db->query($sql);
        log_message('debug','initialized migration table');
    }

    /**
     * Return the current version of the database
     *
     * @return int version
     */
    public function currentVersion() {
        $query = $this->ci->db->query('SELECT IFNULL(0,max(version)) as version FROM migration');
        $result = $query->row();
        $version = (int) $result->version;

        return $version;
    }

    /**
     * @return array list of migrations left to apply
     */
    private function getMigrations(){
        $files = glob($this->migrationdir.'/*.sql');

        // gather list of migration files
        $migrations = array();
        foreach($files as $file){
            $version = (int) basename($file,'.sql');

            if(!$version){
                log_message('error','bad migration file '.$file);
            }else{
                $migrations[$version] = $file;
            }
        }

        // check which are left to apply
        $query = $this->ci->db->query('SELECT version FROM migration ORDER BY version');
        $result = $query->result();
        if($result) foreach($result as $row){
            if(isset($migrations[$row->version])){
                // this migration is already applied
                unset($migrations[$row->version]);
            }else{
                // we don't have a file for this!?
                log_message('error','no migration file for version '.$row->version);
            }
        }

        ksort($migrations);
        return $migrations;
    }

    /**
     * Apply a migration file
     *
     * @param $version
     * @param $file
     */
    private function applyMigration($version, $file){
        $this->ci->db->trans_start();
        $content = file_get_contents($file);
        $queries = preg_split('/[.+;][\s]*\n/', $content, -1, PREG_SPLIT_NO_EMPTY);
        foreach($queries as $query){
            $this->ci->db->simple_query($query);
        }
        $this->ci->db->query("INSERT INTO migration SET version = ?, dt = NOW()",array($version));
        $this->ci->db->trans_complete();
    }

}