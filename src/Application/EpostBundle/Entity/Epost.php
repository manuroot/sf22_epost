<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\EpostBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\ArrayCollection;

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
 * @ORM\Table(name="epost_main")
 * @ORM\Entity(repositoryClass="Application\EpostBundle\Repository\EpostRepository")
 * @UniqueEntity(fields="name", message="Ce post existe déjà...")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Epost {

    //  protected $comments = array();
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
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     * @Assert\Length(
     *      min = "5",
     *      max = "100",
     *      minMessage = "Your name must be at least {{ limit }} characters length |
     *  Au minimum {{ limit }} caracteres",
     *      maxMessage = "Your first name cannot be longer than than {{ limit }} characters length |
     *  Au maximum {{ limit }} caracteres"
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var string
      /* @Assert\Length(min = 10,max=1500)
     * @ORM\Column(name="resume", type="text", nullable=false)
     */
    private $resume;

    /**
     * @Assert\File(
     * maxSize="5M",
     * mimeTypes={"image/png", "image/jpeg", "image/pjpeg","image/gif"}
     * )
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName")
     *
     * @var File $image
     */
    protected $image;
  // @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media",cascade={"persist"})
    
    /**
     * @var Application\Sonata\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media",cascade={"persist"})
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="imageMedia", referencedColumnName="id")
     * })
     */
     protected $imageMedia;
  
    /**
     * @ORM\Column(type="string", length=255, name="image_name", nullable=true)
     *
     * @var string $imageName
     */
    protected $imageName;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var \Application\Sonata\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User",inversedBy="epost")
     * @ORM\OrderBy({"username" = "ASC"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proprietaire", referencedColumnName="id")
     * })
     */
    private $proprietaire;

    /**
     * @var \EpostCategories
     *
     * @ORM\ManyToOne(targetEntity="EpostCategories")
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie", referencedColumnName="id")
     * })
     */
    private $categorie;

    /**
     * @var \EpostStatus
     *
     * @ORM\ManyToOne(targetEntity="EpostStatus", inversedBy="epost"))
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_status", referencedColumnName="id")
     *  })
     */
    private $idStatus;

    /**
     * @ORM\OneToMany(targetEntity="EpostComments", mappedBy="epost",cascade={"remove", "persist"})
     */
    private $comments;

    
     /**
     * @var string
     *
     * @ORM\Column(name="comments_count", type="integer", length=10, nullable=true)
     */
    private $commentscount;

    
    /**
     * @ORM\OneToMany(targetEntity="EpostCommentsThread", mappedBy="epost",cascade={"remove", "persist"})
     */
    private $commentsthread;
    
    
    /**
     * @ORM\OneToMany(targetEntity="EpostNotes", mappedBy="epost",cascade={"remove", "persist"})
     */
    private $notes;

    /**
     * @ORM\OneToOne(targetEntity="EpostGlobalNotes", cascade={"persist", "merge", "remove"}, inversedBy="epostnote")
     * @ORM\JoinColumn(name="globalnotes_id", referencedColumnName="id")
     */
    private $globalnote;

    /**
     * @orm\Column(type="boolean", name="is_visible",nullable=true))
     */
    private $isvisible;

    /**
     * @orm\Column(name="comments_disabled",type="boolean",nullable=true)
     */
    private $commentsDisabled;

    /**
     * @var datetime $commentsCloseAt
     *
     * @ORM\Column(name="comments_closeat", type="datetime",nullable=true)
     */
    protected $commentsCloseAt;

    //protected $commentsDisabled = true;
    //  protected $commentsCloseAt;

    /**
     * @ORM\Column(type="string")
     */
    protected $slug;

    /**
     * Tags for post
     *
     * @var ArrayCollection
     * @Assert\NotBlank()
     * @ORM\ManyToMany(targetEntity="Application\EpostBundle\Entity\EpostTags",inversedBy="posts")
     * @ORM\JoinTable(name="epost_posts_tags",
     * joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     * inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    private $tags;

    // @ORM\Column(type="text")
    //protected $tags;

    
    
    // @ORM\Column(type="text")
     // protected $tagstxt;
    
    
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
        $this->setSlug($this->name);
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
        //   $this->history = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new ArrayCollection();
         $this->commentscount = 0;
        $this->notes = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->globalnote=new \Application\EpostBundle\Entity\EpostGlobalNotes();
        $this->isvisible = true; // Default value for column is_visible
        // enable commentaires: disable: 
        $this->commentsDisabled = false;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Epost
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
     * Set description
     *
     * @param string $resume
     * @return Epost
     */
    public function setResume($resume) {
        $this->resume = $resume;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getResume() {
        return $this->resume;
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
    public function setUpdatedAt() {
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
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue() {
        $this->setUpdatedAt(new \DateTime());
       // $this->setUpdatedAt(new \DateTime());
        $this->setSlug($this->getName());
      //  echo "TEST=" .  $this->xslugify('é'); // --> "e"
        // reclaculer la note globale ??
    }

    public function prePersist() {
        /*   if (!$this->getPublicationDateStart()) {
          $this->setPublicationDateStart(new \DateTime);
          } */

        $this->setCreatedAt(new \DateTime);
        $this->setUpdatedAt(new \DateTime);
        $this->setGlobalnote(new \Application\EpostBundle\Entity\EpostGlobalNotes);
        // note globale a 0 dans table epostglobalnote
        
    }

    /* public function preUpdate()
      {
      $this->setUpdatedAt(new \DateTime);
      } */

    /**
     * Set proprietaire
     *
     * @param \Application\Sonata\UserBundle\Entity\User $proprietaire
     * @return Epost
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
     * @param \Application\EpostBundle\Entity\CertificatsProjet $project
     * @return CertificatsCenter
     */
    public function setCategorie(\Application\EpostBundle\Entity\EpostCategories $categorie = null) {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Application\EpostBundle\Entity\CertificatsProjet 
     */
    public function getCategorie() {
        return $this->categorie;
    }

    /**
     * Set idStatus
     *
     * @param \Application\EpostBundle\Entity\EpostStatus $idStatus
     * @return Epost
     */
    public function setIdStatus(\Application\EpostBundle\Entity\EpostStatus $idStatus = null) {
        $this->idStatus = $idStatus;

        return $this;
    }

    /**
     * Get idStatus
     *
     * @return \Application\EpostBundle\Entity\EpostStatus 
     */
    public function getIdStatus() {
        return $this->idStatus;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Epost
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Remove comments
     *
     * @param \Application\EpostBundle\Entity\EpostComments $comments
     */
    public function removeComment(\Application\EpostBundle\Entity\EpostComments $comments) {
        $this->comments->removeElement($comments);
        if ($this->commentscount > 0)
        $this->commentscount= $this->commentscount - 1;
        else 
           $this->commentscount= 0;
            
    }

    /**
     * Add comments
     *
     * @param \Application\EpostBundle\Entity\EpostComments $comments
     * @return Epost
     */
    public function addComment(\Application\EpostBundle\Entity\EpostComments $comments) {
        $this->comments[] = $comments;
      //  echo 'c=' . count($this->comments) . "<br>";
       // exit(1);
        $this->setCommentscount(count($this->comments));
        //$this->commentscount=  count($this->comments) + 1;
        return $this;
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments() {
        return $this->comments;
    }

    /**
     * Get comments count
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommentsNobbre() {
        return count($this->comments);
    }
    /**
     * Add notes
     *
     * @param \Application\EpostBundle\Entity\EpostNotes $notes
     * @return Epost
     */
    public function addNote(\Application\EpostBundle\Entity\EpostNotes $notes) {
        $this->notes[] = $notes;

        return $this;
    }

    /**
     * Remove notes
     *
     * @param \Application\EpostBundle\Entity\EpostNotes $notes
     */
    public function removeNote(\Application\EpostBundle\Entity\EpostNotes $notes) {
        $this->notes->removeElement($notes);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotes() {
        return $this->notes;
    }

    /**
     * Set tags to post
     *
     * @param $tags Tags collection
     *
     * @return void
     */
    public function setTags($tags) {
        $this->tags = $tags;
    }

    /**
     * Get all tags
     *
     * @return ArrayCollection
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * Add tags
     *
     * @param \Application\EpostBundle\Entity\EpostTags $tags
     * @return Epost
     */
    public function addTag(\Application\EpostBundle\Entity\EpostTags $tags) {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Application\EpostBundle\Entity\EpostTags $tags
     */
    public function removeTag(\Application\EpostBundle\Entity\EpostTags $tags) {
        $this->tags->removeElement($tags);
    }

    /**
     * Set globalnote
     *
     * @param \Application\EpostBundle\Entity\EpostGlobalNotes $globalnote
     * @return Epost
     */
    public function setGlobalnote(\Application\EpostBundle\Entity\EpostGlobalNotes $globalnote = null) {
        $this->globalnote = $globalnote;

        return $this;
    }

    /**
     * Get globalnote
     *
     * @return \Application\EpostBundle\Entity\EpostGlobalNotes 
     */
    public function getGlobalnote() {
        return $this->globalnote;
    }

    /**
     * Set isvisible
     *
     * @param boolean $isvisible
     * @return Epost
     */
    public function setIsvisible($isvisible) {
        $this->isvisible = $isvisible;

        return $this;
    }

    /**
     * Get isvisible
     *
     * @return boolean 
     */
    public function getIsvisible() {
        return $this->isvisible;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentsDisabled($commentsDisabled) {
        $this->commentsDisabled = $commentsDisabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentsDisabled() {
        return $this->commentsDisabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentsCloseAt(\DateTime $commentsCloseAt = null) {
        $this->commentsCloseAt = $commentsCloseAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentsCloseAt() {
        return $this->commentsCloseAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentsDefaultStatus($commentsDefaultStatus) {
        $this->commentsDefaultStatus = $commentsDefaultStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentsDefaultStatus() {
        return $this->commentsDefaultStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function getYear() {
        return $this->getcreatedAt()->format('Y');
    }

    /**
     * {@inheritdoc}
     */
    public function getMonth() {
        return $this->getcreatedAt()->format('m');
    }

    /**
     * {@inheritdoc}
     */
    public function getDay() {
        return $this->getcreatedAt()->format('d');
    }

    public function slugify($text) {
        // replace non letter or digits by -
         $text=$this->createSlug($text); 

        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

        // trim
        $text = trim($text, '--');

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
         // echo "pas de fonction iconv";  exit(1);
        }
        
     

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Epost
     */
    public function setSlug($slug) {
        $this->slug = $this->slugify($slug);

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug() {
        return $this->slug;
    }

    
      /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return Post
     */
    public function setImageMedia(\Application\Sonata\MediaBundle\Entity\Media $imageMedia = null)
    {
        $this->imageMedia = $imageMedia;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getImageMedia()
    {
        return $this->imageMedia;
    }

   

    /**
     * Add commentsthread
     *
     * @param \Application\EpostBundle\Entity\EpostCommentsThread $commentsthread
     * @return Epost
     */
    public function addCommentsthread(\Application\EpostBundle\Entity\EpostCommentsThread $commentsthread)
    {
        $this->commentsthread[] = $commentsthread;
    
        return $this;
    }

    /**
     * Remove commentsthread
     *
     * @param \Application\EpostBundle\Entity\EpostCommentsThread $commentsthread
     */
    public function removeCommentsthread(\Application\EpostBundle\Entity\EpostCommentsThread $commentsthread)
    {
        $this->commentsthread->removeElement($commentsthread);
    }

    /**
     * Get commentsthread
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommentsthread()
    {
        return $this->commentsthread;
    }

    /**
     * Set commentscount
     *
     * @param integer $commentscount
     * @return Epost
     */
    public function setCommentscount($commentscount)
    {
        $this->commentscount = $commentscount;

        return $this;
    }

    /**
     * Get commentscount
     *
     * @return integer 
     */
    public function getCommentscount()
    {
      return $this->commentscount;
      //return (count($this->comments));
    }
    
    
     // @ORM\PreRemove
     // Release all the children on remove
     
   /* public function preRemove()
    {
        foreach($this->children as $child)
            $child->setParent(null);
    }*/
    
    public function xslugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

  // trim
  $text = trim($text, '-');

  // transliterate
  if (function_exists('iconv'))
  {
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  }

  // lowercase
  $text = strtolower($text);

  // remove unwanted characters
  $text = preg_replace('#[^-\w]+#', '', $text);

  if (empty($text))
  {
    return 'n-a';
  }

  return $text;
}


public function createSlug($string) {

    $table = array(
            'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
            'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
            'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-'
    );

    // -- Remove duplicated spaces
   // $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

    // -- Returns the slug
    return strtolower(strtr($string, $table));


}

  }
