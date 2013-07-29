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

    /**
     * Creates and stores version
     *
     * @param File $file
     */
    public function createVersions(File $file)
    {

        $data = $file->getData();

        $replacementParameters = $data['plugin.testplugin'];
        $command = $this->imageMagickHelper->getCommand(3);
        $command->setParameters($replacementParameters);

        // Todo: optimize
        $retrieved = $this->getStorage()->retrieve($file->getResource());

        $img = $this->imageMagickHelper->createImagick($retrieved);

        $this->imageMagickHelper->execute($img);

        $tmp = $this->getTempDir() . '/' . uniqid('', true);
        $img->writeImage($tmp);

        return array(
            $this->getIdentifier() => $tmp
        );
    }

    public function areSharedVersionsAllowed()
    {
        return false;
    }
}
