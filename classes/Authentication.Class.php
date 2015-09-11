<?php

/**
 * Description of Authentication
 *
 * @author Jeffy Shih <bee.me@ninthday.info>
 * @version 1.0
 * @copyright (c) 2014, Jeffy Shih
 */

namespace ninthday\niceToolbar;

class Authentication
{

    private $dbh = null;

    /**
     * 建構子包含連線設定
     * @param \ninthday\niceToolbar\myPDOConn $pdoConn myPDOConn object
     */
    public function __construct(\ninthday\niceToolbar\myPDOConn $pdoConn)
    {
        $this->dbh = $pdoConn->dbh;
    }

    public function isExistandActived($userData)
    {
        $sql = "SELECT * FROM `info_user` WHERE `gid` = :gid AND `gaccount` = :gaccount";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':gid', $userData->id, \PDO::PARAM_STR);
        $stmt->bindParam(':gaccount', $userData->email, \PDO::PARAM_STR);
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            $rs = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $rs['inactive'] != 0;
        } else {
            $this->saveUser($userData);
            return false;
        }
    }

    private function saveUser($userData)
    {
        $sql = "INSERT INTO `info_user` (`gid`, `gname`, `gaccount`, `glink`) "
                . "VALUES (:gid, :gname, :gaccount, :glink);";
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':gid', $userData->id, \PDO::PARAM_STR);
            $stmt->bindParam(':gname', $userData->name, \PDO::PARAM_STR);
            $stmt->bindParam(':gaccount', $userData->email, \PDO::PARAM_STR);
            $stmt->bindParam(':glink', $userData->link, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
    }

    public function __destruct()
    {
        $this->dbh = null;
    }

}
