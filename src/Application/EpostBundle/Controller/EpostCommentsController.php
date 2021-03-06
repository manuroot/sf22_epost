<?php

namespace Application\EpostBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\EpostBundle\Entity\Epost;
use Application\EpostBundle\Entity\EpostComments;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Application\EpostBundle\Form\EpostType;
use Application\EpostBundle\Form\EpostCommentsType;

/**
 * Epost controller.
 *
 */
class EpostCommentsController extends Controller {

    //====================================================================
    // FONCTION GENERIQUE RECUP USERID ET GROUPID 
    //====================================================================

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

    //====================================================================
    // CREATION COMMENTAIRE : 
    //====================================================================

    public function newAction($epost_id) {

        $em = $this->getDoctrine()->getManager();
        $validation = 1;
        list($user_id, $group_id) = $this->getuserid();
        if ($user_id != 0 && $group_id != 0) {
            $current_user = $em->getRepository('ApplicationSonataUserBundle:User')->find($user_id);
            $epost = $this->getEpost($epost_id);
            $comment = new EpostComments();
            $comment->setEpost($epost);
            $comment->setUser($current_user);
            $form = $this->createForm(new EpostCommentsType(), $comment);

            return $this->render('ApplicationEpostBundle:EpostComments:form.html.twig', array(
                        'comment' => $comment,
                        'form' => $form->createView(),
                        'validation' => $validation,
                    ));
        } else {
            $validation = 0;
            return $this->render('ApplicationEpostBundle:EpostComments:form.html.twig', array(
                        'validation' => $validation,
                    ));
        }
    }

    //====================================================================
    // CREATION COMMENTAIRE 
    //====================================================================

    public function createAction($epost_id) {
        $em = $this->getDoctrine()->getManager();
        //  $em = $this->container->get('doctrine')->getManager();
        list($user_id, $group_id) = $this->getuserid();
        if ($user_id == 0) {
            throw $this->createNotFoundException('User not connected.');
        }
        if ($group_id == 0) {
            throw $this->createNotFoundException('User has no group.');
        }
        $current_user = $em->getRepository('ApplicationSonataUserBundle:User')->find($user_id);
        // AJOUT COMMENT
        $epost = $this->getEpost($epost_id);
        $comment = new EpostComments();
        $comment->setEpost($epost);
        $comment->setUser($current_user);
       
        $request = $this->getRequest();
        $form = $this->createForm(new EpostCommentsType(), $comment);
        $form->bindRequest($request);
        if ($form->isValid()) {
             $em = $this->getDoctrine()->getManager();
            //$em = $this->getDoctrine()->getEntityManager();
            $em->persist($comment);
         //   $em->flush();
            // RECUP DES COMMENTS
           // $nombre=$epost->getCommentsNobbre();
         //   echo "nombre=$nombre<br>";exit(1);
            $epost->addComment($comment);
            $em->persist($epost);
            $em->flush();
            return $this->redirect($this->generateUrl('epost_showstandard', array(
                'blog_id' => $comment->getEpost()->getId(),
                'slug'=>$comment->getEpost()->getSlug(),
            )));
              //<a href="{{ path('epost_showstandard', { 'blog_id': entity.id,'slug': entity.slug }) }}">
        }
        // $produit = $this->getComments($produit_id);
    }

    //====================================================================
    // RECUP DES POSTS (MANAGER ?) 
    //====================================================================

    protected function getEpost($epost_id) {
        $em = $this->getDoctrine()
                ->getManager();

        $epost = $em->getRepository('ApplicationEpostBundle:Epost')->find($epost_id);

        if (!$epost) {
            throw $this->createNotFoundException('Unable to find Post.');
        }

        return $epost;
    }

    //====================================================================
    // COMMENTAIRE SUR BLOG DU USER, par defaut owner blog
    //====================================================================
    public function renderComments($criteria = "ownerblog") {
        $em = $this->getDoctrine()->getManager();
        list($user_id, $group_id) = $this->getuserid();
        if ($user_id == 0) {
         } 
         
        $session = $this->getRequest()->getSession();

        switch ($criteria) {
            case 'ownerblog':
                $query = $em->getRepository('ApplicationEpostBundle:EpostComments')->myFindBlogComments($user_id);
                $session->set('buttonretour', 'epost_comment_ownerblogview');
                $page = 'ApplicationEpostBundle:EpostComments:indexownerblog.html.twig';
                break;
            // les commentaires du user loggé
            case 'owner':
                $query = $em->getRepository('ApplicationEpostBundle:EpostComments')->myFindMyComments($user_id);
                $session->set('buttonretour', 'epost_comment_ownerview');
                $page = 'ApplicationEpostBundle:EpostComments:indexowner.html.twig';
                break;
            default:
                throw $this->createNotFoundException('PAs de parametre sur fonction: rendercomments.');
                break;
        }
               $paginationa = $this->createpaginator($query, 20);
            $paginationa->setTemplate('ApplicationEpostBundle:pagination:twitter_bootstrap_pagination.html.twig');
    
        return $this->render($page, array(
                    'paginationa' => $paginationa,
                ));
    }

    //====================================================================
    // COMMENTAIRE SUR BLOG DU USER
    //====================================================================
    public function indexownerblogAction() {
        return $this->renderComments("ownerblog");
    }

    //====================================================================
    // COMMENTAIRE  DU USER
    //====================================================================
    public function indexownerAction() {
        return $this->renderComments("owner");
    }

    //====================================================================
    // PAGINATION
    //====================================================================
    private function createpaginator($query, $num_perpage = 5) {

        $paginator = $this->get('knp_paginator');
        $pagename = 'page'; // Set custom page variable name
        $page = $this->get('request')->query->get($pagename, 1); // Get custom page variable
        $pagination = $paginator->paginate(
                $query, $page, $num_perpage, array('pageParameterName' => $pagename,
            "sortDirectionParameterName" => "dir",
            'sortFieldParameterName' => "sort")
        );
        return $pagination;
    }

    /* edition d'un commentaire par le proprietaire du post
     * 
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ApplicationEpostBundle:EpostComments')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostComment entity.');
        }
        $session = $this->getRequest()->getSession();
        $myretour = $session->get('buttonretour');
        list($user_id, $group_id) = $this->getuserid();
        // propeitaire du post
        $proprietaire = $entity->getEpost()->getProprietaire()->getId();
        //echo "u=$user_id  p=$proprietaire<br>";
        //    exit(1);
        if ($user_id != $proprietaire) {
            return $this->render('ApplicationEpostBundle:Epost:deny.html.twig', array(
                    ));
        }

        $editForm = $this->createForm(new EpostCommentsType(true), $entity);
      //  $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationEpostBundle:EpostComments:edit.html.twig', array(
                    'entity' => $entity,
                    'btnretour' => $myretour,
                    'edit_form' => $editForm->createView(),
                   // 'delete_form' => $deleteForm->createView(),
                ));
    }

      /**
     * Edits an existing EpostNotes entity.
     * id de la note
     */
    public function updateAction(Request $request, $id) {
         $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ApplicationEpostBundle:EpostComments')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EpostComment entity.');
        }
        $session = $this->getRequest()->getSession();
        $myretour = $session->get('buttonretour');
        list($user_id, $group_id) = $this->getuserid();
        // propeitaire du post
        $proprietaire = $entity->getEpost()->getProprietaire()->getId();
        //echo "u=$user_id  p=$proprietaire<br>";
        //    exit(1);
        if ($user_id != $proprietaire) {
            return $this->render('ApplicationEpostBundle:Epost:deny.html.twig', array(
                    ));
        }

           
         $editForm = $this->createForm(new EpostCommentsType(true), $entity);
        //$editForm = $this->createForm(new EpostCommentsType(), $entity);
        $editForm->bind($request);
        if ($editForm->isValid()) {
            // Ajout de la note du user pour ce post
            // gestion par em
            $em->persist($entity);
            // execution de la requete en base
            $em->flush();
            $session->getFlashBag()->add('warning', "Enregistrement $id update successfull");
                  return $this->redirect($this->generateUrl('epost_comment_ownerblogview'));
        }
         return $this->render('ApplicationEpostBundle:EpostComments:edit.html.twig', array(
                    'entity' => $entity,
                    'btnretour' => $myretour,
                    'edit_form' => $editForm->createView(),
                   // 'delete_form' => $deleteForm->createView(),
                ));
    }

}
