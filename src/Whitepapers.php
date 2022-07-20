<?php

namespace timvermaercke\whitepapers;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Fields;
use craft\services\Gc;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use timvermaercke\whitepapers\fields\WhitepaperField;
use timvermaercke\whitepapers\records\WhitepaperRecord;
use timvermaercke\whitepapers\services\WhitepaperService;
use yii\base\Event;

class Whitepapers extends Plugin
{
    public static $plugin;
    public static self $instance;
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = false;
    public bool $hasCpSection = true;

    public function init()
    {
        parent::init();

        self::$instance = $this->getInstance();
        self::$plugin = $this;

        $this->setComponents([
            'whitepapers' => WhitepaperService::class,
        ]);

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['whitepapers/new'] = 'whitepapers/admin/edit';
            $event->rules['whitepapers/<whitepaperId:\d+>'] = 'whitepapers/admin/edit';
            $event->rules['whitepapers/<whitepaperId:\d+>/export'] = 'whitepapers/admin/export';
            $event->rules['whitepapers/save'] = 'whitepapers/admin/save';
        });

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            $variable = $event->sender;
            $variable->set('whitepapers', WhitepaperService::class);
        });

        Event::on(
            Gc::class,
            Gc::EVENT_RUN,
            function() {
                Craft::$app->gc->hardDelete(WhitepaperRecord::$tableName);
            }
        );

        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = WhitepaperField::class;
        });
    }
}
