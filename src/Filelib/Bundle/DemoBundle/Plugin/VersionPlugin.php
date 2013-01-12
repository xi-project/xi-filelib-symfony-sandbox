<?php

namespace Filelib\Bundle\DemoBundle\Plugin;

use Xi\Filelib\File\File;
use Xi\Filelib\Plugin\Image\VersionPlugin as BaseVersionPlugin;

/**
 * Versions an image
 */
class VersionPlugin extends BaseVersionPlugin
{
    protected $commands = array();

    public function setCommands($commands)
    {
        $this->commands = $commands;
    }

    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Creates and stores version
     *
     * @param File $file
     */
    public function createVersions(File $file)
    {
        $data = $file->getData();
        $commands = $this->getCommands();

        $commands['scale']['parameters'] = $data['plugin.testplugin'];

        $ih = $this->getImageMagickHelper();
        $ih->setCommands($commands);

        // Todo: optimize
        $retrieved = $this->getStorage()->retrieve($file->getResource())->getPathname();

        $img = $ih->createImagick($retrieved);

        $ih->execute($img);

        $tmp = $this->getTempDir() . '/' . uniqid('', true);
        $img->writeImage($tmp);

        return array($this->getIdentifier() => $tmp);
    }

    public function areSharedVersionsAllowed()
    {
        return false;
    }
}
