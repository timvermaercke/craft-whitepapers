<?php

namespace timvermaercke\whitepapers\migrations;

use craft\db\Migration;
use timvermaercke\whitepapers\records\DownloadRecord;
use timvermaercke\whitepapers\records\WhitepaperRecord;

class Install extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            WhitepaperRecord::$tableName,
            [
                'id' => $this->primaryKey(),
                'title' => $this->string()->null(),
                'assetId' => $this->string()->null(),
                'dateDeleted' => $this->dateTime()->null(),
            ]
        );

        $this->createTable(
            DownloadRecord::$tableName,
            [
                'id' => $this->primaryKey(),
                'whitepaperId' => $this->integer()->notNull(),
                'email' => $this->string()->null(),
                'downloadedOn' => $this->dateTime()->null(),
            ]
        );

        $this->addForeignKey(null, DownloadRecord::$tableName, ['whitepaperId'], WhitepaperRecord::$tableName, ['id'], 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTableIfExists(DownloadRecord::$tableName);
        $this->dropTableIfExists(WhitepaperRecord::$tableName);
    }
}
