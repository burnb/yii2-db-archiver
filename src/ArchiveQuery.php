<?php
/**
 * @copyright (c) 2015, Petrov Aleksanr
 * @author Petrov Aleksanr <burnb83@gmail.com>
 * Date: 07.04.2015
 * Time: 5:07
 */

namespace burn\dbArchiver;

use yii\base\Model;
use yii\console\Exception;
use yii\db\ActiveRecord;

/**
 * Class ArchiveQuery
 * @package burn\dbArchiver
 */
class ArchiveQuery extends Model
{
    /**
     * @var ActiveRecord object that will be archived
     */
    private $model;
    /**
     * Archiving mode
     * If set true it archive db data by direct MySQL command(fast for big table, but save archive file only on MySQL server)
     * If set false archive db through PHP(big resource usage)
     * @see setDirect()
     * @see isDirect()
     * @var bool
     */
    private $direct = false;
    /**
     * Name of date column by that cut archiving data
     * @see setDateColumn()
     * @see getDateColumn()
     * @var string
     */
    private $dateColumn = 'created_at';
    /**
     * Set is date column in Unix timestamp format or in datetime
     * @see setMkTimeDateColumn()
     * @see isMkTimeDateColumn()
     * @var bool
     */
    private $mkTimeDateColumn = false;
    /**
     * Cut date param-condition how old data will be archived
     * @see getOlderThen()
     * @see setOlderThen()
     * @var string
     */
    private $olderThen = '1 month';

    /**
     * Return ActiveRecord object that will be archived
     * @return ActiveRecord
     * @throws Exception
     */
    public function getModel()
    {
        if (isset($this->model)) {
            return $this->model;
        }
        throw new Exception('Model class not set!');
    }

    /**
     * Set new model object by class name that will be archived
     * @param string $modelClassName
     * @return $this
     */
    public function setArchivedModel($modelClassName)
    {
        $this->model = \Yii::createObject($modelClassName);

        return $this;
    }

    /**
     * Check is archive mode direct or not
     * @return boolean
     */
    public function isDirect()
    {
        return $this->direct;
    }

    /**
     * Set archiving mode
     * If set true it archive db data by direct MySQL command(fast for big table, but save archive file only on MySQL server)
     * If set false archive db through PHP(big resource usage)
     * @param boolean $direct
     * @return $this
     */
    public function setDirect($direct)
    {
        $this->direct = $direct;

        return $this;
    }

    /**
     * Return name of date column by that cut archiving data
     * @return string
     */
    public function getDateColumn()
    {
        return $this->dateColumn;
    }

    /**
     * Set name of date column by that cut archiving data
     * @param string $dateColumn
     * @return $this
     */
    public function setDateColumn($dateColumn)
    {
        $this->dateColumn = $dateColumn;

        return $this;
    }

    /**
     * Return setting is date column in Unix timestamp format
     * @return boolean
     */
    public function isMkTimeDateColumn()
    {
        return $this->mkTimeDateColumn;
    }

    /**
     * Set setting is date column in Unix timestamp format or in datetime
     * @param boolean $mkTimeDateColumn
     * @return $this
     */
    public function setMkTimeDateColumn($mkTimeDateColumn)
    {
        $this->mkTimeDateColumn = $mkTimeDateColumn;

        return $this;
    }

    /**
     * Return cut date param-condition how old data will be archived
     * @return string
     */
    public function getOlderThen()
    {
        return $this->olderThen;
    }

    /**
     * Set cut date param-condition how old data will be archived
     * @param string $olderThen
     * @return $this
     */
    public function setOlderThen($olderThen)
    {
        $this->olderThen = $olderThen;

        return $this;
    }
}