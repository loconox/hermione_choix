<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 30/10/2017
 * Time: 17:59
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="choice")
 */
class Choice
{
    /**
     * @var int
     * @ORM\Column(type="string")
     */
    protected $priority;

    /**
     * @var LEG
     * @ORM\ManyToOne(targetEntity="LEG", inversedBy="choices", cascade={"persist", "merge"})
     * @ORM\Id()
     */
    protected $LEG;

    /**
     * @var \App\Entity\Gabier
     * @ORM\ManyToOne(targetEntity="Gabier", inversedBy="choices", cascade={"persist", "merge"})
     * @ORM\Id()
     */
    protected $gabier;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    protected $validated = false;

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return \App\Entity\LEG
     */
    public function getLEG()
    {
        return $this->LEG;
    }

    /**
     * @param \App\Entity\LEG $LEG
     */
    public function setLEG($LEG)
    {
        $this->LEG = $LEG;
    }

    /**
     * @return \App\Entity\Gabier
     */
    public function getGabier(): Gabier
    {
        return $this->gabier;
    }

    /**
     * @param \App\Entity\Gabier $gabier
     */
    public function setGabier(Gabier $gabier)
    {
        $this->gabier = $gabier;
    }

    /**
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->validated;
    }

    /**
     * @param bool $validated
     */
    public function setValidated(bool $validated)
    {
        $this->validated = $validated;
    }
}