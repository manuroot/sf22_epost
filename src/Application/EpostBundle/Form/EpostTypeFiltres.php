<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\EpostBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Extension\Type\TextFilterType as TextFilterType;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;

use Doctrine\ORM\EntityRepository;


class EpostTypeFiltres extends AbstractType {


    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder
     
                ->add('name', 'filter_text', array(
                    'condition_pattern' => FilterOperands::OPERAND_SELECTOR,
                    'widget_addon' => array(
                        'icon' => 'user',
                        'type' => 'prepend'
                    ),))
               
   ->add('categorie', 'filter_entity', array(
                'class' => 'Application\EpostBundle\Entity\EpostCategories',
                'property' => 'name'
            ))
    
                ->add('createdAt', 'filter_date_range', array(
                     'label' => 'Date de Fin',
                    'left_date_options' => array(
                        'widget' => 'single_text'
                    /* 'time_widget' => 'single_text' */
                    ),
                    'right_date_options' => array(
                        'widget' => 'single_text'
                    /* 'time_widget' => 'single_text' */
                    ),
                ))
                 ->add('updatedAt', 'filter_date_range', array(
                     'label' => 'Date de Fin',
                    'left_date_options' => array(
                        'widget' => 'single_text'
                    /* 'time_widget' => 'single_text' */
                    ),
                    'right_date_options' => array(
                        'widget' => 'single_text'
                    /* 'time_widget' => 'single_text' */
                    ),
                ))
          /*  ->add('proprietaire', 'entity', array(
           'class' => 'Application\Sonata\UserBundle\Entity\User',
           'query_builder' => function(EntityRepository $em) {
                return $em->createQueryBuilder('u')
                                ->orderBy('u.username', 'ASC');
            },
             'property' => 'username',
             'multiple' => false,
            'required' => true,
            'label' => 'Proprietaire',
            'empty_value' => '--- Choisir une option ---'
        ))*/
              ->add('proprietaire', 'filter_entity', array(
                'class' => 'Application\Sonata\UserBundle\Entity\User',
                'property' => 'username'
            ));
        ;
    }

   

    public function getName() {
        return 'epost_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }

    /*
      public function getDefaultOptions(array $options)
      {
      return array(
      'validation_groups' => array('no_validation')
      );
      }

      public function getName()
      {
      return 'team_filter';
      } */
}
