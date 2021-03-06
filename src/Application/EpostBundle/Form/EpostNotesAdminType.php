<?php

namespace Application\EpostBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EpostNotesAdminType extends AbstractType
{
      private $UserId;
    private $EpostId;

    public function __construct($userid = null, $epostid = null) {

        $this->UserId = $userid;
        $this->EpostId = $epostid;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         /* ->add('user',null,array( 'disabled' => true,'label'=>'Utilisateur'))
                     ->add('isComment',null,array('label'=>"Demande de Pret",'required' => false))
       
     */    
         $user_id = $this->UserId;
        $epost_id = $this->EpostId;
     
        $builder
   
      ->add('note', 'genemu_jqueryrating', array(
     'number' => 5,
            'configs' => array(
               
                'readOnly' => false
            )
        ))
        
     
            ->add('user',null,array( 'label'=>'Utilisateur'))
            ->add('epost',null,array( 'label'=>'Post'))
    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            
            'data_class' => 'Application\EpostBundle\Entity\EpostNotes'
        ));
    }

    public function getName()
    {
        return 'application_epostbundle_epostnotestype';
        
    }
}
