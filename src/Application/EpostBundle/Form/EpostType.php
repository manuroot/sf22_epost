<?php

namespace Application\EpostBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;

class EpostType extends AbstractType {

     private $Username;

    public function __construct($username = null) {

        $this->Username = $username;
    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
               /* ->add('resume', 'textarea', array(
                    // 'help_label' => '(Max: 200 car.)',
                    'label' => 'Resumé du Post',
                        /* 'attr' => array(
                          'cols' => "60",
                          ) */
               /* ))*/
                
                ->add('resume', 'textarea', array(
                    'label' => 'Resumé du Post',
                   /* 'attr' => array(
                        'cols' => "40",
                        'rows' => "10",
                        'class' => 'tinymce',
                        ))*/))

                // cyclic dependance ??
                /*  ->add('name', 'genemu_jqueryautocomplete_entity', array(
                  'widget_addon' => array(
                  'icon' => 'pencil',
                  'type' => 'prepend'
                  ),
                  'class' => 'Application\EpostBundle\Entity\Epost',
                  'property' => 'name',
                  'configs' => array(
                  'minLength' => 0,
                  ),
                  )) */
                ->add('name', null, array('label' => 'Nom',
                   'widget_addon' => array(
                        'icon' => 'pencil',
                        'type' => 'prepend'
                        )
                    )
                     )
                ->add('tags')
                //  ->add('name',null,array('label'=>'Nom du Post'))
                
                ->add('description', 'ckeditor', array(
                    'config_name' => 'my_config',
                ))
                
                /*->add('imageMedia', 'sonata_type_model_list', array(
        'required' => false
    ), array(
        'link_parameters' => array(
            'context' => 'default',
            'provider' => 'sonata.media.provider.image'
        )
    )
);*/
                
             //   ->add('imageMedia', 'sonata_type_model', array(), array('edit' => 'list'));
      ->add('imageMedia', 'sonata_media_type', array(
     'provider' => 'sonata.media.provider.image',
     'context'  => 'default',
             'required'=>false,
));
             


                // ->add('services')
         //     $builder->add('imageMedia');
            /*   if (isset($this->Username)) {
            $username = $this->Username;
            $builder->add('imageMedia', 'entity', array(
                //'class' => 'Application\EservicesBundle\Entity\CertificatsProjet',
                'class' => 'ApplicationSonataMediaBundle:Media',
                'query_builder' => function(EntityRepository $em) use ($username) {
                    return $em->createQueryBuilder('u')
                                    //    ->leftJoin('u.produit', 'a')
                                    //  ->leftJoin('a.proprietaire', 'v')
                                    ->where('u.authorName = :proprietaire')
                                    ->setParameter('proprietaire', $username)
                                    ->orderBy('u.authorName', 'ASC');
                },
                'property' => 'name',
                'multiple' => false,
                'required' => false,
                'label' => 'Media',
                'empty_value' => '--- Choisir une option ---'
            ));
        } else {
            $builder->add('imageMedia');
        }*/
             /*   ->add('imageMedia', 'sonata_type_model_list', array('required' => false),
                    array('link_parameters'=>array('context'=>'default',
                   'provider'=>'sonata.media.provider.image')))
           */
     
               /* $builder->add('imageMedia', 'sonata_media_type', array('required' => false,
                    'cascade_validation' => true,
                    'context' => 'default',
                    'provider' => 'sonata.media.provider.image'
                ));*/
                //  ->add('imageMedia')
             /*   ->add('image', 'file', array(
                    'data_class' => 'Symfony\Component\HttpFoundation\File\File',
                    'property_path' => 'image',
                    'required' => false,
                ))*/
                $builder->add('isvisible', null, array('label' => "Post Actif"))
                ->add('commentsDisabled', null, array('label' => "Fermer les Commentaires"));

        $builder->add('categorie', 'entity', array(
                    //'class' => 'Application\EpostBundle\Entity\CertificatsProjet',
                    'class' => 'ApplicationEpostBundle:EpostCategories',
                    'query_builder' => function(EntityRepository $em) {
                        return $em->createQueryBuilder('u')
                                ->orderBy('u.name', 'ASC');
                    },
                    'property' => 'name',
                    'multiple' => false,
                    'required' => true,
                    'label' => 'Categorie',
                    'empty_value' => '--- Choisir une option ---'
                ))
                ->add('idStatus', null, array('label' => 'Status'))
                ->add('commentsCloseAt', 'datetime', array(
                    'label' => 'Date Fermeture des Commentaires',
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'yyyy-MM-dd HH:mm',
                   'widget_addon' => array(
                        'icon' => 'time',
                        'type' => 'prepend'
                    ), 'required' => false,
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Application\EpostBundle\Entity\Epost'
        ));
    }

    public function getName() {
        return 'application_eposttype';
    }

}
