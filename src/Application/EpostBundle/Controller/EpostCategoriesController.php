<?php

namespace Application\EpostBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Application\EpostBundle\Entity\EpostCategories;
use Application\EpostBundle\Form\EpostCategoriesType;

/**
 * EpostCategories controller.
 *
 */
class EpostCategoriesController extends Controller
{
    /**
     * Lists all EpostCategories entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ApplicationEpostBundle:EpostCategories')->findAll();

        return $this->render('ApplicationEpostBundle:EpostCategories:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new EpostCategories entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new EpostCategories();
        $form = $this->createForm(new EpostCategoriesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categories_show', array('id' => $entity->getId())));
        }

        return $this->render('ApplicationEpostBundle:EpostCategories:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new EpostCategories entity.
     *
     */
    public function newAction()
    {
        $entity = new EpostCategories();
        $form   = $this->createForm(new EpostCategoriesType(), $entity);

        return $this->render('ApplicationEpostBundle:EpostCategories:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a EpostCategories entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:EpostCategories')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostCategories entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationEpostBundle:EpostCategories:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing EpostCategories entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:EpostCategories')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostCategories entity.');
        }

        $editForm = $this->createForm(new EpostCategoriesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationEpostBundle:EpostCategories:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing EpostCategories entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:EpostCategories')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostCategories entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EpostCategoriesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categories_edit', array('id' => $id)));
        }

        return $this->render('ApplicationEpostBundle:EpostCategories:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a EpostCategories entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ApplicationEpostBundle:EpostCategories')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EpostCategories entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('categories'));
    }

    /**
     * Creates a form to delete a EpostCategories entity by id.
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
