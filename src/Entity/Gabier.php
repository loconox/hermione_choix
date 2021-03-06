<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 30/10/2017
 * Time: 17:58
 */

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="gabier")
 */
class Gabier
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    protected $pseudo;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstName;

    /**
     * @var Choice[]
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="gabier", cascade={"persist", "merge"})
     */
    protected $choices;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $nbWantedLeg;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"=true})
     */
    protected $new = true;

    function __construct()
    {
        $this->choices = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @return \App\Entity\Choice[]
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param Choice $choice
     */
    public function addChoice(Choice $choice)
    {
        $this->choices->add($choice);
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getNbWantedLeg()
    {
        return $this->nbWantedLeg;
    }

    /**
     * @param mixed $nbWantedLeg
     */
    public function setNbWantedLeg($nbWantedLeg)
    {
        $this->nbWantedLeg = $nbWantedLeg;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->new;
    }

    /**
     * @param bool $new
     */
    public function setNew(bool $new)
    {
        $this->new = $new;
    }
}