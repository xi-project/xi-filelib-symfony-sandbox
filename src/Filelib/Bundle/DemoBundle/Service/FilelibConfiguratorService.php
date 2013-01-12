<?php

namespace Filelib\Bundle\DemoBundle\Service;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Xi\Filelib\Event\IdentifiableEvent;
use Xi\Filelib\File\File;

class FilelibConfiguratorService implements EventSubscriberInterface
{

    /**
     * Returns an array of subscribed events
     *
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return array(
            'xi_filelib.identitymap.before_add' => 'onIdentityMapAdd',
        );
    }

    public function onIdentityMapAdd(IdentifiableEvent $event)
    {
        $obj = $event->getIdentifiable();

        if (!$obj instanceof File) {
            return;
        }

        $data = $obj->getData();

        $width = rand(200, 800);
        $height = rand(200, 800);
        $data['plugin.testplugin'] = array($width, $height, false);

        $width2 = rand(200, 800);
        $height2 = rand(200, 800);
        $data['plugin.selfishplugin'] = array($width2, $height2, false);
    }
}
