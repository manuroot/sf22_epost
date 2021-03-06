<?php

namespace Application\EpostBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\MinLength;

/**
 * EpostNotes
 *
 * @ORM\Table(name="epost_globalnotes")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Application\EpostBundle\Repository\EpostGlobalNotesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class EpostGlobalNotes {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
      /**
     * @var integer
     * @ORM\Column(name="globalnote", type="integer", length=3, nullable=false)
     * @Assert\Length(
     *      min = "5",
     *      max = "10",
     *      minMessage = "Your name must be at least {{ limit }} characters length |
     *  Au minimum {{ limit }} caracteres",
     *      maxMessage = "Your first name cannot be longer than than {{ limit }} characters length |
     *  Au maximum {{ limit }} caracteres"
     * )
     */
    private $globalnote;

    /**
    * @ORM\OneToOne(targetEntity="Epost", mappedBy="globalnote")
    */
    private $epostnote;

    public function getId() {
        return $this->id;
    }

    public function __construct() {
        //  $this->projets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->globalnote = 0;
    }

    public function __toString() {
        $note=   $this->globalnote;
        return  "$note";
    }

    /**
     * Set globalnote
     *
     * @param integer $note
     * @return EpostNotes
     */
    public function setGlobalnote($note) {
        $this->globalnote = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return integer 
     */
    public function getGlobalnote() {
        return $this->globalnote;
    }


    

    /**
     * Set epostnote
     *
     * @param \Application\EpostBundle\Entity\Epost $epostnote
     * @return EpostGlobalNotes
     */
    public function setEpostnote(\Application\EpostBundle\Entity\Epost $epostnote = null)
    {
        $this->epostnote = $epostnote;
    
        return $this;
    }

    /**
     * Get epostnote
     *
     * @return \Application\EpostBundle\Entity\Epost 
     */
    public function getEpostnote()
    {
        return $this->epostnote;
    }
}