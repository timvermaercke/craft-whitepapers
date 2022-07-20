<?php

namespace timvermaercke\whitepapers\fields;

use craft\base\ElementInterface;
use craft\base\Field;
use timvermaercke\whitepapers\Whitepapers;
use yii\helpers\VarDumper;

class WhitepaperField extends Field
{
    public $whitepaper;

    public static function displayName(): string
    {
        return \Craft::t('whitepapers', 'whitepaper');
    }

    public static function hasContentColumn(): bool
    {
        return true;
    }

    public function normalizeValue(mixed $value, ?ElementInterface $element = null): mixed
    {
        return $value ?? '';
    }

    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        $value = $element[$this->handle];

        $options = [
            '' => \Craft::t('whitepapers', 'no_whitepaper'),
        ];
        $whitepapers = Whitepapers::$instance->whitepapers->findAll();
        foreach ($whitepapers as $item) {
            $options[$item->id] = $item->title;
        }

        return \Craft::$app->view->renderTemplate('_includes/forms/select', [
            'name' => $this->handle,
            'options' => $options,
            'value' => $value,
        ]);
    }
}
