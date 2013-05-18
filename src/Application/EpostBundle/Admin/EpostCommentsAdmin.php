<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\EpostBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Formatter\Pool as FormatterPool;
use Sonata\NewsBundle\Model\CommentManagerInterface;
// IMPORTANT:
use Knp\Menu\ItemInterface as MenuItemInterface;

class EpostCommentsAdmin extends Admin {

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var Pool
     */
    protected $formatterPool;
    protected $commentManager;

    public function getTemplate($name) {
        switch ($name) {
            case 'edit':
                return 'ApplicationEpostBundle::base_edit_form.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper) {

        $showMapper
                //    ->add('comment')
                ->add('user', null, array('label' => 'User'))
                    ->add('isComment', null, array('label'=>'Commentaire','editable' => true))
                ->add('approved', null, array('label'=>'Approuvé','editable' => true))
                
                ->add('created')
                ->add('updated')
        ;
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
                ->addIdentifier('id')
                ->add('user', null, array('label' => 'User'))
                    ->add('isComment', null, array('label'=>'Commentaire','editable' => true))
                ->add('approved', null, array('label'=>'Approuvé','editable' => true))
                ->add('created')
                ->add('updated')

        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper) {
        //   $commentClass = $this->commentManager->getClass();

        /*  parent::configureFormFields($formMapper); */
        /*  $formMapper->remove('rawContent'); */

        $formMapper
                ->with('General')
                ->add('comment')
                ->add('user')
                ->add('created')
                ->add('updated')
                ->add('isComment')
                 ->add('approved', null, array('label'=>'Approuvé'))
                ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {

        $datagridMapper
                ->add('user', null, array('label' => 'User'))
                ->add('isComment', null, array('label'=>'Commentaire','editable' => true))
                        ->add('approved', null, array('label'=>'Approuvé','editable' => true))
                ->add('created')
                ->add('updated')
        ;
    }

}
