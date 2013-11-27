<?php

namespace Filelib\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Xi\Filelib\Command;
use Xi\Filelib\FileLibrary;
use Xi\Filelib\Publisher\Publisher;
use Xi\Filelib\File\Upload\FileUpload;

class DefaultController extends Controller
{

    public function clearAction()
    {
        $filelib = $this->getFilelib();

        foreach ($filelib->getFileOperator()->findAll() as $file) {
            $filelib->getFileOperator()->delete($file);
        }

        return new Response('All is clear!');
    }


    public function indexAction()
    {
        $filelib = $this->getFilelib();

        // We want to upload curious manatee image.
        $path = $this->get('kernel')->getRootDir() . "/data/uploads/west_indian_manatee_and_nursing_calf_crystal_river_florida.jpg";

        // Find root folder
        $folder = $filelib->getFolderOperator()->createByUrl('images/of/manatees');

        $upload = new FileUpload($path);

        // Configure (optional) limiter to accept only images
        $limiter = new \Xi\Filelib\File\Upload\Limiter();
        $limiter->accept('image/');

        // If not accepted by limiter, deny upload.
        if (!$limiter->isAccepted($upload)) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, "File type '{$upload->getMimeType()}' is not allowed");
        }


        $op = $filelib->getFileOperator();

        $file = $op->upload($upload, $folder, 'default');

        $this->getPublisher()->publish($file);

        return $this->render('FilelibDemoBundle:Default:index.html.twig', array(
            'fl' => $filelib,
            'file' => $file,
        ));

        return $this->render('FilelibDemoBundle:Default:index.html.twig');

    }




    public function selfishAction()
    {
        $filelib = $this->getFilelib();

        // We want to upload curious manatee image.
        $path = $this->get('kernel')->getRootDir() . "/data/uploads/west_indian_manatee_and_nursing_calf_crystal_river_florida.jpg";

        // Find root folder
        $folder = $filelib->getFolderOperator()->findByUrl('images/of/manatees');

        // Prepare file for upload
        $upload = $filelib->getFileOperator()->prepareUpload($path);

        $op = $filelib->getFileOperator();

        $file = $op->upload($upload, $folder, 'selfish');

        return $this->render('FilelibDemoBundle:Default:selfish.html.twig', array(
            'fl' => $filelib,
            'file' => $file,
        ));

        return $this->render('FilelibDemoBundle:Default:selfish.html.twig');

    }



    public function asyncUploadTestAction()
    {
        $filelib = $this->getFilelib();

        $iter = new \DirectoryIterator($this->get('kernel')->getRootDir() . '/data/uploads');

        $folder = $filelib->getFolderOperator()->findRoot();

        $uploaded = array();

        $filelib->getFileOperator()->setCommandStrategy(DefaultFileOperator::COMMAND_UPLOAD, Command::STRATEGY_ASYNCHRONOUS);

        for ($x = 1; $x <= 10; $x++) {
            foreach ($iter as $key => $file) {
                if ($file->isFile()) {
                    $filelib->getFileOperator()->upload($file->getRealPath(), $folder, 'versioned');
                }
            }
        }

        return new Response('Some uploads were created, sir');
    }


    public function copyTestAction()
    {
        $filelib = $this->getFilelib();

        // We want to upload curious manatee image.
        $path = $this->get('kernel')->getRootDir() . "/data/uploads/curious-manatee.jpg";

        // Find root folder
        $folder = $filelib->getFolderOperator()->findRoot();

        // Prepare file for upload
        $upload = $filelib->getFileOperator()->prepareUpload($path);

        $file = $filelib->getFileOperator()->upload($upload, $folder, 'versioned');
        $file2 = $filelib->getFileOperator()->upload($upload, $folder, 'selfish');

        $file3 = $filelib->getFileOperator()->copy($file, $folder);
        $file4 = $filelib->getFileOperator()->copy($file2, $folder);

        echo "<pre>";
        var_dump($file);
        var_dump($file3);

        echo "<hr />";
        var_dump($file2);
        var_dump($file4);
        die();

    }


    public function testSuiteAction()
    {
        $filelib = $this->getFilelib();

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



    public function videoTestAction()
    {
        $filelib = $this->getFilelib();

        // We want to upload curious manatee image.
        $path = $this->get('kernel')->getRootDir() . "/../vendor/xi-filelib/tests/data/hauska-joonas.mp4";

        // Find root folder
        $folder = $filelib->getFolderOperator()->findRoot();

        // Prepare file for upload
        $upload = $filelib->getFileOperator()->prepareUpload($path);


        $op = $filelib->getFileOperator();

        $op->setCommandStrategy(DefaultFileOperator::COMMAND_UPLOAD, Command::STRATEGY_ASYNCHRONOUS);

        $file = $op->upload($upload, $folder, 'versioned');

        return new Response('Video upload was pooped to the queue');

    }

    /**
     * @return FileLibrary
     */
    protected function getFilelib()
    {
        return $this->get('xi_filelib');
    }

    /**
     * @return Publisher
     */
    protected function getPublisher()
    {
        return $this->get('xi_filelib.publisher');
    }


}
