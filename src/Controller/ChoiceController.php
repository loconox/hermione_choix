<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 31/10/2017
 * Time: 22:22
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ChoiceController extends Controller
{
    /**
     * @Route("/choice/{gabierId}/{leg}/{value}", name="validateChoicePage", requirements={"value"="\d"})
     */
    public function validateAction($gabierId, $leg, $value)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var \App\Entity\Choice $choice */
        $choice = $em->getRepository('App:Choice')->find(['gabier' => $gabierId, 'LEG' => $leg]);

        if (!$choice) {
            throw $this->createNotFoundException('Choice not found');
        }

        $choice->setValidated($value == 1);
        $em->flush();

        return $this->render('Gabier/_choice.html.twig', ['choice' => $choice]);
    }

}