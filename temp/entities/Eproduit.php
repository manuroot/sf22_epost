<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\EservicesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */

/**
 * Changements
 *
 * @ORM\Table(name="eproduits")
 * @ORM\Entity(repositoryClass="Application\EservicesBundle\Repository\EproduitRepository")
 * @UniqueEntity(fields="name", message="Ce produit existe déjà...")
 * @Vich\Uploadable
 */
class Eproduit {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Get id
     *
     * @return integer 
     */

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Eservice", mappedBy="produits",cascade={"persist"})
     */
    private $services;

    /**
     * @Assert\File(
     * maxSize="5M",
     * mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName")
     *
     * @var File $image
     */
    protected $image;

    /**
     * @ORM\Column(type="string", length=255, name="image_name", nullable=true)
     *
     * @var string $imageName
     */
    protected $imageName;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var \Application\Sonata\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\OrderBy({"username" = "ASC"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proprietaire", referencedColumnName="id")
     * })
     */
    private $proprietaire;

    /**
     * @var \EproduitCategories
     *
     * @ORM\ManyToOne(targetEntity="EproduitCategories")
     * @ORM\OrderBy({"nom" = "ASC"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie", referencedColumnName="id")
     * })
     */
    private $categorie;

    /**
     * @var \EserviceStatus
     *
     * @ORM\ManyToOne(targetEntity="EproduitStatus", inversedBy="eproduit"))
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_status", referencedColumnName="id")
     *  })
     */
    private $idStatus;

     /**
* @var \Doctrine\Common\Collections\Collection
*
* @ORM\OneToMany(targetEntity="EproduitHistory", mappedBy="produit", cascade={"remove"})
*/
    private $history;


    public function getId() {
        return $this->id;
    }

    public function __toString() {
        return $this->getName();
    }

    /**
     *  Set nom
     *
     * @param string $fileName
     * @return CertificatsCenter
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->history = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Eservice
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set services
     *
     * @param \Application\EservicesBundle\Entity\Eservice $services
     * @return Eproduit
     */
    public function setServices(\Application\EservicesBundle\Entity\Eservice $services = null) {
        $this->services = $services;

        return $this;
    }

    /**
     * Get services
     *
     * @return \Application\EservicesBundle\Entity\Eservice 
     */
    public function getServices() {
        return $this->services;
    }

    /**
     * Add services
     *
     * @param \Application\EservicesBundle\Entity\Eservice $services
     * @return Eproduit
     */
    public function addService(\Application\EservicesBundle\Entity\Eservice $services) {
        $this->services[] = $services;

        return $this;
    }

    /**
     * Remove services
     *
     * @param \Application\EservicesBundle\Entity\Eservice $services
     */
    public function removeService(\Application\EservicesBundle\Entity\Eservice $services) {
        $this->services->removeElement($services);
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Message
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Set imageName
     *
     * @param string $imageName
     * @return Message
     */
    public function setImageName($imageName) {
        $this->imageName = $imageName;
        return $this;
    }

    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName() {
        return $this->imageName;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Message
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = new \DateTime();
//$this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        if (!isset($this->updatedAt))
            $this->updatedAt = new \DateTime();
        return $this->updatedAt;
    }

    /**
     * Set proprietaire
     *
     * @param \Application\Sonata\UserBundle\Entity\User $proprietaire
     * @return Eproduit
     */
    public function setProprietaire(\Application\Sonata\UserBundle\Entity\User $proprietaire = null) {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    /**
     * Get proprietaire
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getProprietaire() {
        return $this->proprietaire;
    }

    /**
     * Set project
     *
     * @param \Application\EservicesBundle\Entity\CertificatsProjet $project
     * @return CertificatsCenter
     */
    public function setCategorie(\Application\EservicesBundle\Entity\EproduitCategories $categorie = null) {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Application\EservicesBundle\Entity\CertificatsProjet 
     */
    public function getCategorie() {
        return $this->categorie;
    }

    /**
     * Set idStatus
     *
     * @param \Application\EservicesBundle\Entity\EserviceStatus $idStatus
     * @return Eservice
     */
    public function setIdStatus(\Application\EservicesBundle\Entity\EproduitStatus $idStatus = null) {
        $this->idStatus = $idStatus;

        return $this;
    }

    /**
     * Get idStatus
     *
     * @return \Application\EservicesBundle\Entity\EproduitStatus 
     */
    public function getIdStatus() {
        return $this->idStatus;
    }

    /**
* Add history
*
* @param \Desarrolla2\Bundle\BlogBundle\Entity\PostHistory $history
* @return Post
*/
    public function addHistory(\Desarrolla2\Bundle\BlogBundle\Entity\PostHistory $history) {
        $this->history[] = $history;

        return $this;
    }

    /**
* Remove history
*
* @param \Desarrolla2\Bundle\BlogBundle\Entity\PostHistory $history
*/
    public function removeHistory(\Desarrolla2\Bundle\BlogBundle\Entity\PostHistory $history) {
        $this->history->removeElement($history);
    }

    /**
* Get history
*
* @return \Doctrine\Common\Collections\Collection
*/
    public function getHistory() {
        return $this->history;
    }
}