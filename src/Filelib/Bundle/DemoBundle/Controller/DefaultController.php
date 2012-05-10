<?php

namespace Filelib\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Xi\Filelib\Renderer\SymfonyRenderer;

use Symfony\Component\HttpFoundation\Response;

use Xi\Filelib\File\DefaultFileOperator;

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
        $folder = $filelib->getFolderOperator()->findRoot();

        // Prepare file for upload
        $upload = $filelib->file()->prepareUpload($path);



        // Configure (optional) limiter to accept only images
        $limiter = new \Xi\Filelib\File\Upload\Limiter();
        $limiter->accept('image/');

        // If not accepted by limiter, deny upload.
        if (!$limiter->isAccepted($upload)) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, "File type '{$upload->getMimeType()}' is not allowed");
        }


        $op = $filelib->getFileOperator();
        // $op->setCommandStrategy(DefaultFileOperator::COMMAND_UPLOAD, DefaultFileOperator::STRATEGY_ASYNCHRONOUS);

        /*
        for ($x = 1; $x <= 1; $x++) {
            $file = $filelib->file()->upload($upload, $folder, 'versioned');
        }
         */
        // Upload prepared file to root folder with versioned profile. You can also use path if not using limiter!

        /*
        var_dump($file);

        die();
        */

        $file = $op->upload($upload, $folder, 'versioned');

        return $this->render('FilelibDemoBundle:Default:index.html.twig', array(
            'fl' => $filelib,
            'file' => $file,
        ));

        return $this->render('FilelibDemoBundle:Default:index.html.twig');

    }


    public function testSuiteAction()
    {
        // Luss filelib from DI container
        $filelib = $this->get('filelib');


        $root = $filelib->getFolderOperator()->findRoot();

        $fitem = $filelib->getFolderOperator()->getInstance();
        $fitem->setName('tussi');
        $fitem->setParentId($root->getId());

        $filelib->getFolderOperator()->create($fitem);

        $fitem2 = $filelib->getFolderOperator()->getInstance();
        $fitem2->setName('lussi');
        $fitem2->setParentId($fitem->getId());
        $filelib->getFolderOperator()->create($fitem2);

        $fitem->setName('mustekasetti');
        $filelib->getFolderOperator()->update($fitem);

        $iter = new \DirectoryIterator($this->get('kernel')->getRootDir() . '/data/uploads');


        $uploaded = array();

        foreach ($iter as $key => $file) {
            if ($file->isFile()) {

                if ($key % 2 == 1) {
                    $folder = $fitem;
                } else {
                    $folder = $fitem2;
                }

                $uploaded[] = $filelib->getFileOperator()->upload($file->getRealPath(), $folder, 'versioned');
            }
        }


        $fitem2->setName('lusauttaja');
        $filelib->getFolderOperator()->update($fitem2);

        foreach($uploaded as $file) {

            $file->setName(strrev($file->getName()));
            $filelib->getFileOperator()->update($file);
        }

        $filelib->getFolderOperator()->delete($fitem);

        return new Response('Test suite completed');

    }


}
