<?php
/**
 * @copyright (c) 2015, Galament
 * @author Petrov Aleksanr <burnb83@gmail.com> 
 * Date: 07.04.2015
 * Time: 4:13
 */

namespace burn\dbArchiver;

use yii\base\Model;
use yii\db\ActiveRecord;

class DbArchiver extends Model
{
    private $arcClass;

    /**
     * @param $arcClass ActiveRecord
     */
    public function _construct($arcClass)
    {
        $this->arcClass = $arcClass;
    }

    /**
     *
     */
    public function actionStart()
    {
        $this->archive($this->arcClass);
    }

    /**
     * @param $arcClass ActiveRecord
     */
    private function archive($arcClass)
    {
        $arcClass::getDb()
            ->createCommand("SELECT *
                INTO OUTFILE 'dump.sql'
                FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
                LINES TERMINATED BY '\n'
                FROM log_error
                WHERE log_time < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 MONTH))")
            ->execute();

    }
}