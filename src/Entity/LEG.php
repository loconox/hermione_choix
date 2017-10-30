<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 30/10/2017
 * Time: 17:59
 */

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="leg")
 */
class LEG
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var Choice[]
     * @ORM\OneToMany(targetEntity="Choice", mappedBy="LEG", cascade={"persist", "merge"})
     */
    protected $choices;

    public function __construct()
    {
        $this->choices = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \App\Entity\Choice[]
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param \App\Entity\Choice $choice
     */
    public function addChoice($choice)
    {
        $this->choices->add($choice);
    }
}