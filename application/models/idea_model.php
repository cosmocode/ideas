<?php

class Idea_model extends CI_Model {

    /**
     * List ideas
     *
     * @todo offset/limit
     * @param bool $all   Include closed issues?
     * @param string $order Sort by top, new
     * @return array the results
     */
    public function fetch($all = false, $order = 'top') {
        if($all){
            $where = '';
        } else {
            $where = "AND status = ''";
        }

        if($order == 'new'){
            $orderby = 'created DESC';
        }else{
            $orderby = 'votes DESC, created DESC';
        }

        $sql   = "SELECT A.*, IFNULL(SUM(B.vote),0) as votes, C.fullname
                  FROM idea A LEFT JOIN vote B
                                     ON A.id = B.idea
                              LEFT JOIN user C
                                     ON A.login = C.login
                 WHERE 1=1
                       $where
              GROUP BY A.id
              ORDER BY $orderby";
        $query = $this->db->query($sql);

        return $query->result();
    }

    /**
     * Get a single idea
     *
     * @param $ideaID
     * @return object a single DB result
     */
    public function get($ideaID) {
        $sql   = "SELECT A.*, IFNULL(SUM(B.vote),0) as votes
                  FROM idea A LEFT JOIN vote B
                    ON A.id = B.idea
                 WHERE A.id = ?
              GROUP BY A.id";
        $query = $this->db->query($sql, array($ideaID));

        return $query->row();
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $login
     * @return int the new idea ID
     */
    public function add($title, $description, $login) {
        $sql = "INSERT INTO idea
                   SET title = ?,
                       description = ?,
                       login = ?,
                       created = NOW()
                   ";
        $this->db->query($sql, array($title, $description, $login));

        return $this->db->insert_id();
    }

    /**
     * Update an existing idea
     *
     * Authentication has to be checked before hand!
     *
     * @param int    $ideaID
     * @param string $title
     * @param string $description
     */
    public function change($ideaID, $title, $description) {
        $sql = "UPDATE idea
                   SET title = ?,
                       description = ?
                 WHERE id = ?
                   ";
        $this->db->query($sql, array($title, $description, $ideaID));
    }

    /**
     * Update the status of an idea
     *
     * Authentication has to be checked before hand!
     *
     * @param int    $ideaID
     * @param string $state
     * @param string $login
     */
    public function status($ideaID, $state, $login) {

    }

    /**
     * Cast a vote
     *
     * @param int    $ideaID
     * @param string $login
     * @param int    $vote
     * @return int Number of votes for this idea
     */
    public function vote($ideaID, $login, $vote) {
        $vote = (int) $vote;
        if($vote < 0) $vote = -1;
        if($vote > 0) $vote = 1;

        $sql = "REPLACE INTO vote
                    SET idea = ?,
                        login = ?,
                        vote  = ?";
        $this->db->query($sql, array($ideaID, $login, $vote));

        $sql   = "SELECT SUM(vote) as votes
                  FROM vote
                 WHERE idea = ?";
        $query = $this->db->query($sql, array($ideaID));

        $row = $query->row();
        return (int) $row->votes;
    }

    /**
     * Return vote and personal vote counts for the given ideas
     *
     * @param array $ideaIDs list of ideas to check
     * @param string $login the current user for personal vote choices
     * @return mixed
     */
    public function votes($ideaIDs, $login=''){
        $ids = array_map('intVal', (array) $ideaIDs);
        $sql = "SELECT A.idea,
                       SUM(A.vote) AS votes,
                       MAX(B.vote) AS myvote
                  FROM vote A LEFT JOIN vote B
                    ON A.idea = B.idea
                   AND A.login = B.login
                   AND B.login = ?
                 WHERE A.idea IN (".join(',',$ids).")
              GROUP BY A.idea";
        $query = $this->db->query($sql, array($login));
        return $query->result();
    }
}