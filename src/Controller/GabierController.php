<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 31/10/2017
 * Time: 08:08
 */

namespace App\Controller;


use App\Entity\Gabier;
use App\Form\Type\GabierType;
use App\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/gabier/{id}", name="gabierShowPage", requirements={"id"="\d+"})
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

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @Route("/gabier/new", name="gabierNewPage")
     */
    public function newAction(Request $request)
    {
        $gabier = new Gabier();

        $form = $this->createForm(GabierType::class, $gabier);
        $form->add(
            'submit',
            SubmitType::class,
            ['label' => 'Créer', 'attr' => ['class' => 'btn-success']]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($gabier->getPseudo() == null) {
                $this->setPseudo($gabier);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($gabier);
            $em->flush();

            return new RedirectResponse(
                $this->generateUrl('gabierShowPage', ['id' => $gabier->getId()])
            );
        }

        return $this->render(
            'Gabier/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    private function setPseudo(Gabier $gabier)
    {
        $pseudo = Util::slugify(
            $gabier->getFirstName().'-'.$gabier->getLastName()
        );
        $repo = $this->getDoctrine()->getRepository('App:Gabier');
        $qb = $repo->createQueryBuilder('g');
        $qb->andWhere(
            $qb->expr()->like('g.pseudo', ':like')
        );
        $qb->setParameter('like', sprintf('%s%%', $pseudo));
        $otherGabiers = $qb->getQuery()->execute();

        if ($otherGabiers) {
            $inc = 0;
            foreach ($otherGabiers as $otherGabier) {
                if (0 < preg_match(
                        sprintf('/%s-(\d*)/', preg_quote($pseudo, '/')),
                        $otherGabier->getPseudo(),
                        $matches
                    )) {
                    if ($inc < $matches[1]) {
                        $inc = $matches[1];
                    }
                }

            }
            $pseudo = $pseudo.'-'.($inc + 1);

        }
        $gabier->setPseudo($pseudo);
    }
}