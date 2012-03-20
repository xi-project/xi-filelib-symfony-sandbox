<?php

namespace Filelib\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Xi\Filelib\Renderer\SymfonyRenderer;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    
    public function clearAction()
    {
        // Luss filelib from DI container
        $filelib = $this->get('filelib');

        foreach ($filelib->getFileOperator()->findAll() as $file) {
            $filelib->getFileOperator()->delete($file);
        }
        
        return new Response('All is clear!');
    }
    
    
    public function indexAction()
    {
        // Luss filelib from DI container
        $filelib = $this->get('filelib');
        
        // We want to upload curious manatee image.
        $path = $this->get('kernel')->getRootDir() . "/data/uploads/curious-manatee.jpg";

        // Find root folder
        $folder = $filelib->folder()->findRoot();
        
        // Prepare file for upload
        $upload = $filelib->file()->prepareUpload($path);
        
        // Configure (optional) limiter to accept only images
        $limiter = new \Xi\Filelib\File\Upload\Limiter();
        $limiter->accept('image/');
        
        // If not accepted by limiter, deny upload.
        if (!$limiter->isAccepted($upload)) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, "File type '{$upload->getMimeType()}' is not allowed");
        }
                        
        // Upload prepared file to root folder with versioned profile. You can also use path if not using limiter!
        $file = $filelib->file()->upload($upload, $folder, 'versioned');
        
        
        return $this->render('FilelibDemoBundle:Default:index.html.twig', array(
            'fl' => $filelib,
            'file' => $file,
        ));
                
        return $this->render('FilelibDemoBundle:Default:index.html.twig');
        
    }
}
