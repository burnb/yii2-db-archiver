Yii2 DB Archiver.
===================
Archive old data from db to file by Yii2 ActiveRecord models. 
For big tables allow direct file export from mysql database(fast archiving but file save only on mysql server).

Usage:
------
At first set in main app config dbArchiver component like this:
```
    'components' => [
            'dbArchiver' => [
                'class'    => 'burn\dbArchiver\DbArchiver',
                'dumpPath' => '/home/'
            ]
        ],
```
Example console action, using DbArchiver component for large table with direct archiving and date column in Unix timestamp format:
```
  /**
   * @throws \yii\base\InvalidConfigException
   */
  public function actionArchiveErrorsLog()
  {
      /** @var \burn\dbArchiver\DbArchiver $dbArchiver */
      $dbArchiver = \Yii::$app->get('dbArchiver');
      $dbArchiver
          ->getQuery()
          ->setArchivedModel(LogError::className())
          ->setDirect(true)
          ->setDateColumn('log_time')
          ->setMkTimeDateColumn(true);
      if (!$dbArchiver->actionArchive()) {
          \Yii::error(LogError::tableName() . ' table not archived!');
          echo 'Something went wrong! '. LogError::tableName() . ' table not archived!';
      }
  }
```
Example of restore console action:
```
    /**
    * @throws \yii\base\InvalidConfigException
    */
    public function actionRestoreErrorsLog($filename)
    {
        /** @var \burn\dbArchiver\DbArchiver $dbArchiver */
        $dbArchiver = \Yii::$app->get('dbArchiver');
        $dbArchiver
            ->getQuery()
            ->setArchivedModel(LogError::className())
            ->setDirect(true);
        if (!$dbArchiver->actionRestoreFromFile($filename)) {
            \Yii::error(LogError::tableName() . ' table not restored!');
            echo 'Something went wrong! '. LogError::tableName() . ' table not restored!';
        }
    }
```


