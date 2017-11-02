<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 30/10/2017
 * Time: 23:03
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class LegController extends Controller
{
    /**
     * @Route("/leg", name="legIndexPage")
     */
    public function indexAction()
    {
        $legs = $this->get('doctrine.orm.default_entity_manager')
            ->getRepository('App:LEG')
            ->findAll();

        return $this->render('Leg/index.html.twig', ['legs' => $legs]);
    }

    /**
     * @param $id
     * @Route("/leg/{id}", name="legShowPage")
     */
    public function legAction($id)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        /** @var \App\Entity\LEG $leg */
        $leg = $em->getRepository('App:LEG')->find($id);

        if (!$leg) {
            throw $this->createNotFoundException('Leg not found');
        }

        $choices = [];
        foreach ($leg->getChoices() as $choice) {
            $priority = $choice->getPriority();
            if (!isset($choices[$priority])) {
                $choices[$priority] = [];
            }
            $choices[$priority][] = $choice;
        }

        $repo = $em->getRepository('App:Gabier');
        $qb = $repo->createQueryBuilder('g');
        $qb->join('g.choices', 'c');
        $qb->andWhere(
            $qb->expr()->andX(
                $qb->expr()->eq('c.LEG', ':leg'),
                $qb->expr()->eq('c.validated', ':validated')
            )
        );
        $qb->setParameter('leg', $leg);
        $qb->setParameter('validated', true);
        $validated = $qb->getQuery()->execute();

        return $this->render(
            "Leg/show.html.twig",
            [
                'leg' => $leg,
                'choices' => $choices,
                'validated' => $validated,
            ]
        );
    }

}