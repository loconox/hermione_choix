<?php
namespace App\Repository;

use App\Entity\Gabier;
use App\Entity\LEG;
use Doctrine\ORM\EntityRepository;

class LEGRepository extends EntityRepository
{
    /**
     * @param LEG $leg
     * @param Gabier[] $gabiers
     *
     * @return string
     */
    public function exportCSV($id)
    {
        /** @var \App\Entity\LEG $leg */
        $leg = $this->find($id);

        if ( ! $leg) {
            return null;
        }

        $repo = $this->_em->getRepository('App:Gabier');
        $qb   = $repo->createQueryBuilder('g');
        $qb->join('g.choices', 'c');
        $qb->andWhere(
            $qb->expr()->andX(
                $qb->expr()->eq('c.LEG', ':leg'),
                $qb->expr()->eq('c.validated', ':validated')
            )
        );
        $qb->setParameter('leg', $leg);
        $qb->setParameter('validated', true);
        $gabiers = $qb->getQuery()->execute();

        $delim = ";";

        $csv = '"LEG '.$leg->getId().' '.$leg->getName()."\"\n";
        $line = [
            '"Pseudo"',
            '"Prénom"',
            '"Nom"',
        ];

        $csv .= implode($delim, $line)."\n";

        foreach ($gabiers as $gabier) {
            $line = [
                '"'.$gabier->getPseudo().'"',
                '"'.$gabier->getFirstName().'"',
                '"'.$gabier->getLastName().'"',
            ];
            $csv .= implode($delim, $line)."\n";
        }

        return $csv;
    }
}