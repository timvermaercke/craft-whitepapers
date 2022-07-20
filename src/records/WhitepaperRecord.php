<?php

namespace timvermaercke\whitepapers\records;

use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;
use yii\db\ActiveQueryInterface;

class WhitepaperRecord extends ActiveRecord
{
    use SoftDeleteTrait;

    public static $tableName = '{{%timvermaercke_whitepapers}}';

    public static function tableName()
    {
        return self::$tableName;
    }

    public function getDownloads(): ActiveQueryInterface
    {
        return $this->hasMany(DownloadRecord::class, ['whitepaperId' => 'id']);
    }
}
