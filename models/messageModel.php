<?php

class messageModel extends Model{
    protected $_table = "messages";
    protected $_key = "IDMESSAGE";
    
    public function __construct() {
        parent::__construct();
    }
    /**
     * Obtient le message a envoye de la BD
     * @param type $type
     */
    public function getMessage($type = "0001") {
        $query = "SELECT * FROM messages WHERE TYPEMESSAGE = :type";
        return $this->row($query, ["type" => $type]);
    }
}
