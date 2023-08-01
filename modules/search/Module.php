<?php

namespace modules\search;

// Import the classes
use Craft;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;
use yii\base\Module as BaseModule;

// Extend the base module
class Module extends BaseModule
{
    // Initialize the module
    public function init()
    {
        // Initialize the parent (BaseModule)
        parent::init();

        // Attach an event listener to the UrlManager::EVENT_REGISTER_SITE_URL_RULES event
        Event::on(
            UrlManager::class, 
            UrlManager::EVENT_REGISTER_SITE_URL_RULES, 
            function (RegisterUrlRulesEvent $event) {
                $event->rules['actions/search/search/search'] = 'search/search/search';
            }
        );
    }
}
