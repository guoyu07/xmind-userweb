<?php

/**
 * Description of UserLog
 *
 * @author Jeffy Shih <bee.me@ninthday.info>
 * @version 1.1
 * @copyright (c) 2014, Jeffy Shih
 */

namespace ninthday\XMind;

class UserLog
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

    public function getDateList($days)
    {
        $date_list = array();
        for ($i = 0; $i < $days; $i++) {
            $date_list[] = date('Y-m-d', strtotime('-' . strval($i) . ' days'));
        }
        return $date_list;
    }

    public function getUserLogByDate($userID, $UHID, $assign_date)
    {
        $rtn = array();
        $sql = 'SELECT `logID`, `saveTime`,  `receiveCount` FROM `upload_log` '
                . 'WHERE `userID` = :uid AND `UHID` = :uhid AND `uploadTime` > :begintime AND `uploadTime` < :endtime';
        $begin_time = $assign_date . ' 00:00:00';
        $end_time = $assign_date . ' 23:59:59';
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':uid', $userID, \PDO::PARAM_INT);
            $stmt->bindParam(':uhid', $UHID, \PDO::PARAM_INT);
            $stmt->bindParam(':begintime', $begin_time, \PDO::PARAM_STR);
            $stmt->bindParam(':endtime', $end_time, \PDO::PARAM_STR);
            $stmt->execute();

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($rtn, $row);
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        return $rtn;
    }

    public function getUserModelByUID($userID)
    {
        $rtn = array();
        $sql = 'SELECT `UHID`, `Model`, `AndroidVersion` FROM `user_hardware` WHERE `userID`= :uid ORDER BY `UHID` DESC';
        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':uid', $userID, \PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                array_push($rtn, $row);
            }
        } catch (\PDOException $exc) {
            throw new \Exception($exc->getMessage());
        }
        return $rtn;
    }

    public function __destruct()
    {
        $this->dbh = null;
    }

}
