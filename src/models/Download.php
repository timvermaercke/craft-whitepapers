<?php

namespace timvermaercke\whitepapers\models;

use craft\base\Model;

class Download extends Model
{
    public $id;
    public $whitepaperId;
    public $email;
    public $downloadedOn;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['email'], 'email'];
        $rules[] = [['whitepaperId', 'downloadedOn', 'email'], 'required'];

        return $rules;
    }
}
