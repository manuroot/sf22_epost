<?php

namespace Application\EpostBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EpostRollType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('link')
          //  ->add('createdAt')
         //   ->add('updatedAt')
         //   ->add('isvisible')
            ->add('proprietaire')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\EpostBundle\Entity\EpostRoll'
        ));
    }

    public function getName()
    {
        return 'application_epostbundle_epostrolltype';
    }
}
