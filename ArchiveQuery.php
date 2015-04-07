<?php
/**
 * @copyright (c) 2015, Galament
 * @author Petrov Aleksanr <burnb83@gmail.com>
 * Date: 07.04.2015
 * Time: 5:07
 */

namespace burn\dbArchiver;

use yii\base\Model;
use yii\db\ActiveRecord;

class ArchiveQuery extends Model
{
    private $model,
        $direct = false,
        $dateColumn = 'created_at',
        $mkTimeDateColumn = false,
        $olderThen = '1 month';

    /**
     * @return ActiveRecord
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $modelClassName
     * @return $this
     */
    public function setArchivedModel($modelClassName)
    {
        $this->model = \Yii::createObject($modelClassName);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDirect()
    {
        return $this->direct;
    }

    /**
     * @param boolean $direct
     * @return $this
     */
    public function setDirect($direct)
    {
        $this->direct = $direct;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateColumn()
    {
        return $this->dateColumn;
    }

    /**
     * @param string $dateColumn
     * @return $this
     */
    public function setDateColumn($dateColumn)
    {
        $this->dateColumn = $dateColumn;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMkTimeDateColumn()
    {
        return $this->mkTimeDateColumn;
    }

    /**
     * @param boolean $mkTimeDateColumn
     * @return $this
     */
    public function setMkTimeDateColumn($mkTimeDateColumn)
    {
        $this->mkTimeDateColumn = $mkTimeDateColumn;

        return $this;
    }

    /**
     * @return string
     */
    public function getOlderThen()
    {
        return $this->olderThen;
    }

    /**
     * @param string $olderThen
     * @return $this
     */
    public function setOlderThen($olderThen)
    {
        $this->olderThen = $olderThen;

        return $this;
    }
}