<?php

/**
 * Description of Authentication
 *
 * @author Jeffy Shih <bee.me@ninthday.info>
 * @version 1.1
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

    /**
     * 確認使用者的姓名和聯絡 Email 是不是已經有完整
     * 
     * @param Object $userData Google Data Object
     * @return boolean
     * @since 1.1 (2015-09-12)
     * @access public
     */
    public function isDoneProfile($userData)
    {
        $sql = "SELECT * FROM `info_user` WHERE `gid` = :gid AND `gaccount` = :gaccount";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':gid', $userData->id, \PDO::PARAM_STR);
        $stmt->bindParam(':gaccount', $userData->email, \PDO::PARAM_STR);
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            $rs = $stmt->fetch(\PDO::FETCH_ASSOC);
            return ($rs['name'] != '' and $rs['email'] != '');
        } else {
            return false;
        }
    }

    /**
     * 更新使用者資訊
     * 
     * @param string $cname 中文名稱
     * @param string $user_email 聯絡用電子郵件信箱
     * @param object $userData Google Data Object
     * @throws \Exception
     * @since 1.1 (2015-09-12)
     * @access public
     */
    public function updateUser($cname, $user_email, $userData)
    {
        $sql = "UPDATE `info_user` SET `name`=:cname,`email`=:uemail WHERE `gid`=:gid";
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':cname', $cname, \PDO::PARAM_STR);
            $stmt->bindParam(':uemail', $user_email, \PDO::PARAM_STR);
            $stmt->bindParam(':gid', $userData->id, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
    }

    /**
     * 儲存由 Google API取回來的資訊
     * 
     * @param object $userData Google Data Object
     * @throws \Exception
     * @access private
     * @since 1.0
     */
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
