<?php

namespace timvermaercke\whitepapers\records;

use craft\db\ActiveRecord;

class DownloadRecord extends ActiveRecord
{
    public static $tableName = '{{%timvermaercke_whitepaper_downloads}}';

    public static function tableName()
    {
        return self::$tableName;
    }
}
