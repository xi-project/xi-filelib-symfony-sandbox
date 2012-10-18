<?php

namespace Filelib\Bundle\DemoBundle\Plugin;

use Imagick;
use Xi\Filelib\Configurator;
use Xi\Filelib\File\File;
use Xi\Filelib\Plugin\VersionProvider\AbstractVersionProvider;
use Xi\Filelib\Plugin\Image\ImageMagickHelper;

/**
 * Versions an image
 *
 */
class SelfishVersionPlugin extends AbstractVersionProvider
{

    protected $providesFor = array('image');

    protected $imageMagickHelper;

    /**
     * @var File extension for the version
     */
    protected $extension;


    protected $commands = array();

    public function __construct($options = array())
    {
        parent::__construct($options);
    }


    public function setCommands($commands)
    {
        $this->commands = $commands;
    }


    public function getCommands()
    {
        return $this->commands;
    }



    /**
     * Returns ImageMagick helper
     *
     * @return ImageMagickHelper
     */
    public function getImageMagickHelper()
    {
        return new ImageMagickHelper();
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

        $tmp = $this->getFilelib()->getTempDir() . '/' . uniqid('', true);
        $img->writeImage($tmp);

        return array($this->getIdentifier() => $tmp);
    }

    public function getVersions()
    {
        return array($this->identifier);
    }

    /**
     * Sets file extension
     *
     * @param string $extension File extension
     * @return VersionProvider
     */
    public function setExtension($extension)
    {
        $extension = str_replace('.', '', $extension);
        $this->extension = $extension;
        return $this;
    }

    /**
     * Returns the plugins file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    public function getExtensionFor($version)
    {
        return $this->getExtension();
    }


    public function areSharedVersionsAllowed()
    {
        return false;
    }


    public function isSharedResourceAllowed()
    {
        return false;
    }


}