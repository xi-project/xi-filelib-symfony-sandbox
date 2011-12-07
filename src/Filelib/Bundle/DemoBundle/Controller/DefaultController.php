<?php

namespace Filelib\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction()
    {
                
        // Luss filelib from DI container
        $filelib = $this->get('filelib');
        
        // We want to upload curious manatee image.
        $path = $this->get('kernel')->getRootDir() . "/../data/uploads/curious-manatee.jpg";
        
        // Accept only images
        $filelib->file()->getUploader()->accept('image/');

        // Find root folder
        $folder = $filelib->folder()->findRoot();
                
        // Upload file to root folder with versioned profile
        $file = $filelib->file()->upload($path, $folder, 'versioned');
        
        return $this->render('FilelibDemoBundle:Default:index.html.twig', array(
            'fl' => $filelib,
            'file' => $file,
            'mini_url' => $filelib->file()->getUrl($file, array('version' => 'mini')),
            'thumb_url' => $filelib->file()->getUrl($file, array('version' => 'thumb')),
            'cinemascope_url' => $filelib->file()->getUrl($file, array('version' => 'cinemascope')),
            'cropped_url' => $filelib->file()->getUrl($file, array('version' => 'cropped')),
        ));
    }
}
