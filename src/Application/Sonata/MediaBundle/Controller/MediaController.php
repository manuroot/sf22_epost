<?php

namespace Application\Sonata\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\Sonata\MediaBundle\Entity\MediaRepository;
use Application\Sonata\MediaBundle\Entity\Media;
use Application\Sonata\MediaBundle\Form\MediaType;

/**
 * Media controller.
 *
 */
class MediaController extends Controller {
    /*     * 
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

    /**
     * Lists all Media entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('ApplicationSonataMediaBundle:Media')->myFindAll();
        $defaut_paginator_a = array('pagename' => 'page1', 'sortdir' => 'dir1', 'sortfield' => 'sort1');
        $paginationa = $this->createpaginator($query, 10, $defaut_paginator_a);
        return $this->render('ApplicationSonataMediaBundle:Media:index.html.twig', array(
                    'paginationa' => $paginationa,
                ));
    }

    /**
     * Creates a new Media entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new Media();
        $form = $this->createForm(new MediaType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('usermedia_show', array('id' => $entity->getId())));
        }

        return $this->render('ApplicationSonataMediaBundle:Media:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }

    /**
     * Displays a form to create a new Media entity.
     *
     */
    public function newAction() {
        $entity = new Media();
        $form = $this->createForm(new MediaType(), $entity);

        return $this->render('ApplicationSonataMediaBundle:Media:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }

    /**
     * Finds and displays a Media entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationSonataMediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationSonataMediaBundle:Media:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing Media entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationSonataMediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $editForm = $this->createForm(new MediaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationSonataMediaBundle:Media:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
    }

    /**
     * Edits an existing Media entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationSonataMediaBundle:Media')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Media entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new MediaType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('usermedia_edit', array('id' => $id)));
        }

        return $this->render('ApplicationSonataMediaBundle:Media:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
    }

    /**
     * Deletes a Media entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ApplicationSonataMediaBundle:Media')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Media entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('usermedia'));
    }

    /**
     * Creates a form to delete a Media entity by id.
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
