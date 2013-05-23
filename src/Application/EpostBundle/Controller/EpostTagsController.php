<?php

namespace Application\EpostBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Application\EpostBundle\Entity\EpostTags;
use Application\EpostBundle\Form\EpostTagsType;

/**
 * EpostTags controller.
 *
 */
class EpostTagsController extends Controller
{
    /**
     * Lists all EpostTags entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ApplicationEpostBundle:EpostTags')->findAll();

        return $this->render('ApplicationEpostBundle:EpostTags:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new EpostTags entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new EpostTags();
        $form = $this->createForm(new EpostTagsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tags_show', array('id' => $entity->getId())));
        }

        return $this->render('ApplicationEpostBundle:EpostTags:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new EpostTags entity.
     *
     */
    public function newAction()
    {
        $entity = new EpostTags();
        $form   = $this->createForm(new EpostTagsType(), $entity);

        return $this->render('ApplicationEpostBundle:EpostTags:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a EpostTags entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:EpostTags')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostTags entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationEpostBundle:EpostTags:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing EpostTags entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:EpostTags')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostTags entity.');
        }

        $editForm = $this->createForm(new EpostTagsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationEpostBundle:EpostTags:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing EpostTags entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:EpostTags')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostTags entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EpostTagsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tags_edit', array('id' => $id)));
        }

        return $this->render('ApplicationEpostBundle:EpostTags:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a EpostTags entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ApplicationEpostBundle:EpostTags')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EpostTags entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tags'));
    }

    /**
     * Creates a form to delete a EpostTags entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
