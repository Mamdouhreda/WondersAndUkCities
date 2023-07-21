<?php

namespace modules\search;

use Craft;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public function init()
    {
        parent::init();

        Event::on(
            UrlManager::class, 
            UrlManager::EVENT_REGISTER_SITE_URL_RULES, 
            function (RegisterUrlRulesEvent $event) {
                $event->rules['actions/search/search/search'] = 'search/search/search';
            }
        );
    }
}
    