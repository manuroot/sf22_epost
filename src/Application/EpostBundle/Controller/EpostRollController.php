<?php

namespace Application\EpostBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\EpostBundle\Entity\EpostRoll;
use Application\EpostBundle\Form\EpostRollType;

/**
 * EpostRoll controller.
 *
 */
class EpostRollController extends Controller {
    /* ====================================================================
     * 
     *  CREATION DU PAGINATOR
     * 
      =================================================================== */

    //$defaut_paginator=array('pagename'=>'page1','sort'=>'sort1','sortfield'=>'sort1');
    private function createpaginator($query, $num_perpage = 5, $defaut_paginator = null) {

        $paginator = $this->get('knp_paginator');

        $sortDirectionParameterName = 'sortDirectionParameterName';
        $sortFieldParameterName = 'sortFieldParameterName';
        $pagename = 'page'; // Set custom page variable name
        // Ajouter des controles

        if (is_array($defaut_paginator)) {
            $pagename = $defaut_paginator['pagename'];
            $sortDirectionParameterName = $defaut_paginator['sortdir'];
            $sortFieldParameterName = $defaut_paginator['sortfield'];
        }

        $page = $this->get('request')->query->get($pagename, 1); // Get custom page variable

        $pagination = $paginator->paginate(
                $query, $page, $num_perpage, array('pageParameterName' => $pagename,
            "sortDirectionParameterName" => $sortDirectionParameterName,
            'sortFieldParameterName' => $sortFieldParameterName)
        );

        $pagination->setTemplate('ApplicationEpostBundle:pagination:twitter_bootstrap_pagination.html.twig');
        return $pagination;
    }

    /* ====================================================================
     * 
     *  RECUP USER_ID ET GROUP_ID
     * 
     * =================================================================== */

    private function getuserid() {


        $em = $this->getDoctrine()->getManager();
        $user_security = $this->container->get('security.context');
        // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
        if ($user_security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->get('security.context')->getToken()->getUser();
            $user_id = $user->getId();
            $group = $user->getIdgroup();
            if (isset($group)) {
                $group_id = $group->getId();
            } else {
                $group_id = 0;
            }
        } else {
            $user_id = 0;
            $group_id = 0;
        }


        // }else {
        return array($user_id, $group_id);
        //   }
    }

    /**
     * Lists all EpostRoll entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ApplicationEpostBundle:EpostRoll')->findAll();

        return $this->render('ApplicationEpostBundle:EpostRoll:index.html.twig', array(
                    'entities' => $entities,
                ));
    }

    public function indexgrouperollAction() {

        $em = $this->getDoctrine()->getManager();
        list($user_id, $group_id) = $this->getuserid();

        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'epostroll');
        $query = $em->getRepository('ApplicationEpostBundle:EpostRoll')->getMyPager(array(
            'group' => $group_id,
                ));
        //  $query = $em->getRepository('ApplicationEpostBundle:Epost')->myFindOtherAll($user_id, $group_id);
        $paginationa = $this->createpaginator($query, 5);
        return $this->render('ApplicationEpostBundle:EpostRoll:index_group.html.twig', array(
                    'paginationa' => $paginationa,
                ));
    }

    /**
     * Creates a new EpostRoll entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new EpostRoll();
        list($user_id, $group_id) = $this->getuserid();
        $em = $this->getDoctrine()->getManager();
        if ($user_id == 0) {
            throw $this->createNotFoundException('Ce user n\'est pas connecté.');
        }

        $entity_user = $em->getRepository('ApplicationSonataUserBundle:User')->find($user_id);

        $entity->setProprietaire($entity_user);

        $form = $this->createForm(new EpostRollType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('epostroll_show', array('id' => $entity->getId())));
        }

        return $this->render('ApplicationEpostBundle:EpostRoll:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }

    /**
     * Displays a form to create a new EpostRoll entity.
     *
     */
    public function newAction() {
        $entity = new EpostRoll();


        list($user_id, $group_id) = $this->getuserid();
        $em = $this->getDoctrine()->getManager();
        if ($user_id == 0) {
            throw $this->createNotFoundException('Ce user n\'est pas connecté.');
        }

        $entity_user = $em->getRepository('ApplicationSonataUserBundle:User')->find($user_id);

        $entity->setProprietaire($entity_user);


        $form = $this->createForm(new EpostRollType(), $entity);

        return $this->render('ApplicationEpostBundle:EpostRoll:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }

    /**
     * Finds and displays a EpostRoll entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:EpostRoll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostRoll entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationEpostBundle:EpostRoll:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing EpostRoll entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:EpostRoll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostRoll entity.');
        }

        $editForm = $this->createForm(new EpostRollType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationEpostBundle:EpostRoll:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
    }

    /**
     * Edits an existing EpostRoll entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:EpostRoll')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostRoll entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EpostRollType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('epostroll_edit', array('id' => $id)));
        }

        return $this->render('ApplicationEpostBundle:EpostRoll:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
    }

    /**
     * Deletes a EpostRoll entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ApplicationEpostBundle:EpostRoll')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EpostRoll entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('epostroll'));
    }

    /**
     * Creates a form to delete a EpostRoll entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}
