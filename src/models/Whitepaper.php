<?php

namespace timvermaercke\whitepapers\models;

use craft\base\Model;
use timvermaercke\whitepapers\Whitepapers as Plugin;

class Whitepaper extends Model
{
    public $id;
    public $title;
    public $assetId;
    public $dateDeleted;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [
            ['title', 'assetId'],
            'required'
        ];

        return $rules;
    }

    public function getDownloads()
    {
        return Plugin::$instance->whitepapers->getDownloadsByWhitepaperId($this->id);
    }
}
