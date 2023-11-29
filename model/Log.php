<?php

class Log extends CRUD
{
    protected $table = 'journal_de_bord';
    protected $fillable = ['user_id', 'action', 'details', 'ip_address', 'user_agent'];

    public function recordAction($action, $details, $userId = null)
    {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ];

        return $this->insert($data);
    }

    public function selectAllByRecentDate()
    {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY timestamp DESC";
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }


  
}

