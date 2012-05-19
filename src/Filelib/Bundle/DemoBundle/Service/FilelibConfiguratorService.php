<?php

namespace Filelib\Bundle\DemoBundle\Service;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Xi\Filelib\Event\FilelibEvent;
use Xi\Filelib\Event\FileEvent;

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
            'file.instantiate' => 'onFileInstantiate',
            'file.upload' => 'onFileInstantiate',
        );
    }


    public function onFileInstantiate(FileEvent $event)
    {
        $file = $event->getFile();

        $data = $file->getData();

        $width = rand(200, 800);
        $height = rand(200, 800);

        $data['plugin.testplugin'] = array($width, $height, false);

    }


}
