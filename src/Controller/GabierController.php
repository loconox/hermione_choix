<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 31/10/2017
 * Time: 08:08
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class GabierController extends Controller
{

    /**
     * @Route("/gabier/", name="gabierIndexPage")
     */
    public function indexAction()
    {
        $repo = $this->getDoctrine()->getRepository('App:Gabier');
        $gabiers = $repo->findAll();

        return $this->render('Gabier/index.html.twig', ['gabiers' => $gabiers]);
    }

    /**
     * @Route("/gabier/{id}", name="gabierShowPage")
     */
    public function showAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('App:Gabier');
        $gabier = $repo->find($id);

        if (!$gabier) {
            throw $this->createNotFoundException('Gabier not found');
        }

        return $this->render('Gabier/show.html.twig', ['gabier' => $gabier]);
    }
}