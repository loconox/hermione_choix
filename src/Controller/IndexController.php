<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 31/10/2017
 * Time: 09:10
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends Controller
{

    /**
     * @Route("/", name="indexPage")
     */
    public function indexAction()
    {
        $legCount = $this->getDoctrine()->getRepository('App:LEG')
            ->createQueryBuilder('l')
            ->select('COUNT(l)')
            ->getQuery()
            ->getSingleScalarREsult();

        $gabierCount = $this->getDoctrine()->getRepository('App:Gabier')
                         ->createQueryBuilder('g')
                         ->select('COUNT(g)')
                         ->getQuery()
                         ->getSingleScalarREsult();


        $choiceCount = count($this->getDoctrine()->getRepository('App:Choice')->findAll());

        return $this->render('index.html.twig', [
            'legCount' => $legCount,
            'gabierCount' => $gabierCount,
            'choiceCount' => $choiceCount,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/about", name="aboutPage")
     */
    public function aboutAction()
    {
        return $this->render('about.html.twig');
    }

}