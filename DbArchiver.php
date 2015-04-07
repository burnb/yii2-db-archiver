<?php
/**
 * @copyright (c) 2015, Galament
 * @author Petrov Aleksanr <burnb83@gmail.com>
 * Date: 07.04.2015
 * Time: 4:13
 */

namespace burn\dbArchiver;

use yii;
use yii\base\Model;

class DbArchiver extends Model
{
    public $dumpPath;
    private $query;

    public function init()
    {
        $this->query = new ArchiveQuery();
    }

    /**
     * @return ArchiveQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     *
     * @return bool|int
     * @throws yii\db\Exception
     */
    public function actionSaveToFile()
    {
        $query = $this->getQuery();
        if ($query->isDirect()) {
            $sql = "SELECT *
                    INTO OUTFILE '" . $this->dumpPath . $query->getModel()->tableName() . "_" . date('d_m_Y_h_i_s') . ".sql'
                    FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
                    LINES TERMINATED BY '\\n'
                    FROM " . $query->getModel()->tableName() . "
                    WHERE " . $this->getDateCondition();
            $result = $query
                        ->getModel()
                        ->getDb()
                        ->createCommand($sql)
                        ->execute();
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * @param $filePath
     * @return bool|int
     * @throws yii\db\Exception
     */
    public function actionRestoreFromFile($filePath)
    {
        $query = $this->getQuery();
        if ($query->isDirect()) {
            $sql = "LOAD DATA INFILE '" . $filePath . "'
                    INTO TABLE " . $query->getModel()->tableName() . "
                    FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
                    LINES TERMINATED BY '\\n'";
            $result = $query
                ->getModel()
                ->getDb()
                ->createCommand($sql)
                ->execute();
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function actionArchive()
    {
        if ($this->actionSaveToFile()) {
            $result = $this
                ->getQuery()
                ->getModel()
                ->deleteAll($this->getDateCondition());
            if ($result) {
                printf('Archive table "' . $this->getQuery()->getModel()->tableName() . "\" success.\r\n");

                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    private function getDateCondition()
    {
        $query = $this->getQuery();
        $cropDate = ($query->isMkTimeDateColumn())
            ? "UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL " . $query->getOlderThen() . "))"
            : "DATE_SUB(NOW(), INTERVAL " . $query->getOlderThen() . ")";

        return $query->getDateColumn() . " < " . $cropDate;
    }
}