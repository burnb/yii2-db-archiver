<?php
/**
 * @copyright (c) 2015, Petrov Aleksanr
 * @author Petrov Aleksanr <burnb83@gmail.com>
 * Date: 07.04.2015
 * Time: 4:13
 */

namespace burn\dbArchiver;

use yii;
use yii\base\Model;

/**
 * Class DbArchiver
 * @package burn\dbArchiver
 */
class DbArchiver extends Model
{
    /**
     * Dump path that set in component config
     * @var string
     */
    public $dumpPath;
    /**
     * ArchiveQuery object that set condition for archiving object
     * @var ArchiveQuery
     */
    private $query;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->query = new ArchiveQuery();
    }

    /**
     * Return ArchiveQuery for set archive condition
     * @return ArchiveQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Save selected in ArchiveQuery db data to file
     * It save to file on mysql server if select direct mode
     * or anywhere you set if not direct mode
     * @return bool|int false if not saved or number of saved row
     * @throws yii\db\Exception
     */
    public function actionSaveToFile()
    {
        $result = false;
        $query = $this->getQuery();
        $model = $query->getModel();
        if ($query->isDirect()) {
            $tableName = $model->tableName();
            $sql = "SELECT *
                    INTO OUTFILE ':filePath'
                    FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
                    LINES TERMINATED BY '\\n'
                    FROM :table
                    WHERE :condition";
            $result = $model
                ->getDb()
                ->createCommand($sql, [
                    ':filePath'  => $this->dumpPath . $tableName . "_" . date('d_m_Y_h_i_s') . ".sql",
                    ':table'     => $tableName,
                    ':condition' => $this->getDateCondition()
                ])
                ->execute();
        } else {
            $data = $model::find()
                ->where($this->getDateCondition())
                ->asArray()
                ->all();
            if ($data) {

            }
        }

        return $result;
    }

    /**
     * Restore data from file by file path
     * it can be in different format depending on archive method @see ArchiveQuery::setDirect()
     * @param string $filePath
     * @return bool|int false if not restore or number of restore row
     * @throws yii\db\Exception
     */
    public function actionRestoreFromFile($filePath)
    {
        $result = false;
        $query = $this->getQuery();
        $model = $query->getModel();
        if ($query->isDirect()) {
            $sql = "LOAD DATA INFILE ':filePath'
                    INTO TABLE :table
                    FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
                    LINES TERMINATED BY '\\n'";
            $result = $model
                ->getDb()
                ->createCommand($sql, [
                    ':filePath' => $filePath,
                    ':table'    => $model->tableName()
                ])
                ->execute();
        } else {
            $data = [];
            $sql = \Yii::$app->getDb()->createCommand()->batchInsert($model->tableName(), $model->fields(), $data)->getSql();
            $sql .= 'ON DUPLICATE KEY UPDATE ';
            $values = [];
            foreach ($model->fields() as $column) {
                $values[] = "{$column} = VALUES({$column})";
            }
            $sql .= implode($values, ', ');
            $result = \Yii::$app->getDb()->createCommand($sql)->execute();
        }

        return $result;
    }

    /**
     * Final action archive db data to file
     * and delete archived data
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
     * Return crop condition for db data which to be archive
     * Crop sets by date column @see ArchiveQuery::setDateColumn()
     * it can be in Unix timestamp or datetime format @see ArchiveQuery::isMkTimeDateColumn()
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