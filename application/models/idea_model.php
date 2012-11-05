<?php

class Idea_model extends CI_Model {

    /**
     * List ideas
     *
     * @todo offset/limit
     * @param bool   $all    Include closed issues?
     * @param string $order  Sort by top, new
     * @param int    $limit  limit results to this number
     * @param int    $offset start offset
     * @param int    &$found will be filled with the number of all results
     * @return array the results
     */
    public function fetch($all = false, $order = 'top', $limit = 0, $offset = 0, &$found = null) {
        if($all) {
            $where = '';
        } else {
            $where = "AND status = 0";
        }

        if($order == 'new') {
            $orderby = 'created DESC';
        } else {
            $orderby = 'votes DESC, created DESC';
        }

        if($limit) {
            $limitby = 'LIMIT '.((int) $limit).' OFFSET '.((int) $offset);
        } else {
            $limitby = '';
        }

        $sql   = "SELECT SQL_CALC_FOUND_ROWS A.*, IFNULL(SUM(B.vote),0) as votes, C.fullname
                  FROM idea A LEFT JOIN vote B
                                     ON A.id = B.idea
                              LEFT JOIN user C
                                     ON A.login = C.login
                 WHERE 1=1
                       $where
              GROUP BY A.id
              ORDER BY $orderby
                       $limitby";
        $query = $this->db->query($sql);

        if($limit) {
            $q     = $this->db->query('SELECT FOUND_ROWS() AS found');
            $found = $q->row()->found;
        }

        return $query->result();
    }

    /**
     * Execute a fulltext search
     *
     * @param string $search search query
     * @param int    $limit  limit results to this number
     * @param int    $offset start offset
     * @param int    &$found will be filled with the number of all results
     * @return mixed
     */
    public function search($search, $limit = 0, $offset = 0, &$found = null) {
        if($limit) {
            $limitby = 'LIMIT '.((int) $limit).' OFFSET '.((int) $offset);
        } else {
            $limitby = '';
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                       A.*,
                       IFNULL(SUM(B.vote),0) as votes,
                       C.fullname,
                       MATCH(A.title, A.description) AGAINST (? WITH QUERY EXPANSION) AS score
                  FROM idea A LEFT JOIN vote B
                                     ON A.id = B.idea
                              LEFT JOIN user C
                                     ON A.login = C.login
                 WHERE MATCH(A.title, A.description) AGAINST (? WITH QUERY EXPANSION)
              GROUP BY A.id
              ORDER BY score DESC
                       $limitby";

        $query = $this->db->query($sql, array($search, $search));

        if($limit) {
            $q     = $this->db->query('SELECT FOUND_ROWS() AS found');
            $found = $q->row()->found;
        }

        return $query->result();
    }

    /**
     * Get a single idea
     *
     * @param $ideaID
     * @return object a single DB result
     */
    public function get($ideaID) {
        $sql   = "SELECT A.*, IFNULL(SUM(B.vote),0) as votes, C.fullname
                  FROM idea A LEFT JOIN vote B
                                     ON A.id = B.idea
                              LEFT JOIN user C
                                     ON A.login = C.login
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
     * @param int    $status
     */
    public function setStatus($ideaID, $status) {
        $sql = "UPDATE idea
                   SET status = ?
                 WHERE id = ?";
        $this->db->query($sql, array($status, $ideaID));
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
     * @param array  $ideaIDs list of ideas to check
     * @param string $login the current user for personal vote choices
     * @return mixed
     */
    public function votes($ideaIDs, $login = '') {
        $ids   = array_map('intVal', (array) $ideaIDs);
        $sql   = "SELECT A.idea,
                       SUM(A.vote) AS votes,
                       MAX(B.vote) AS myvote
                  FROM vote A LEFT JOIN vote B
                    ON A.idea = B.idea
                   AND A.login = B.login
                   AND B.login = ?
                 WHERE A.idea IN (".join(',', $ids).")
              GROUP BY A.idea";
        $query = $this->db->query($sql, array($login));
        return $query->result();
    }
}