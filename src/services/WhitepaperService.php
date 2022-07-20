<?php

namespace timvermaercke\whitepapers\services;

use craft\base\Component;
use Exception;
use timvermaercke\whitepapers\models\Download;
use timvermaercke\whitepapers\models\Whitepaper;
use timvermaercke\whitepapers\records\DownloadRecord;
use timvermaercke\whitepapers\records\WhitepaperRecord;

class WhitepaperService extends Component
{
    public function findAll()
    {
        return WhitepaperRecord::find()->orderBy('title asc')->all();
    }

    public function find($whitepaperId): ?Whitepaper
    {
        $whitepaperRecord = WhitepaperRecord::findOne($whitepaperId);
        if (!$whitepaperRecord) {
            return null;
        }

        return new Whitepaper($whitepaperRecord->toArray());
    }

    public function save(Whitepaper &$model)
    {
        if ($id = $model->id) {
            $record = WhitepaperRecord::findOne($id);
            if (!$record) {
                throw new Exception(`Can't find a record with ID "${id}"`);
            }
        } else {
            $record = new WhitepaperRecord();
        }

        if (!$model->validate()) {
            return false;
        }

        $record->title = $model->title;
        $record->assetId = $model->assetId;
        $record->save();

        return true;
    }

    public function remove(Whitepaper &$model)
    {
        $id = $model->id;
        $record = WhitepaperRecord::findOne($id);
        if (!$record) {
            throw new Exception(`Can't find a record with ID "${id}"`);
        }

        $record->softDelete();

        return true;
    }

    public function getDownloadsByWhitepaperId(int $whitepaperId)
    {
        $result = DownloadRecord::findAll(['whitepaperId' => $whitepaperId]);

        return $result;
    }

    public function saveDownload(Download &$model)
    {
        $record = new DownloadRecord();

        if (!$model->validate()) {
            return false;
        }

        $record->email = $model->email;
        $record->downloadedOn = $model->downloadedOn;
        $record->whitepaperId = $model->whitepaperId;
        $record->save();

        return true;
    }
}
