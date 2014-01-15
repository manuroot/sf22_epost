<?php

namespace Application\EpostBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\EpostBundle\Entity\Epost;
use Application\EpostBundle\Entity\EpostTags;
use Application\EpostBundle\Entity\EpostComments;
use Application\EpostBundle\Entity\EpostCategories;
use Application\EpostBundle\Entity\EpostRoll;
use Application\EpostBundle\Form\EpostType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Application\EpostBundle\Form\EpostTypeFiltres;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Epost controller.
 *
 */
class EpostController extends Controller {
    /* ====================================================================
     * 
     *  SIDEBAR : TAGS, COMMENTS, CATEGORIES
     * 
      =================================================================== */

    private function sidebar_tags() {

        $em = $this->container->get('doctrine')->getManager();
        $alltags = $em->getRepository('ApplicationEpostBundle:EpostTags')->myFindAll();
        $tagWeights = $em->getRepository('ApplicationEpostBundle:EpostTags')->getTagWeights($alltags);
        //   print_r($tagWeights);
        //  exit(1);
        return array($alltags, $tagWeights);
    }

    private function sidebar_comments() {
        $em = $this->container->get('doctrine')->getManager();
        list($user_id, $group_id) = $this->getuserid();
        if ($user_id != 0 && $group_id != 0) {
            $lastcomments = $em->getRepository('ApplicationEpostBundle:EpostComments')->FindGroupLastComments(10, $group_id);
        } else {
            $lastcomments = $em->getRepository('ApplicationEpostBundle:EpostComments')->FindLastComments();
        }

        return ($lastcomments);
    }

    private function sidebar_articles() {
        $em = $this->container->get('doctrine')->getManager();
        $lastarticles = $em->getRepository('ApplicationEpostBundle:Epost')->FindLastArticles();
        return ($lastarticles);
    }

    private function sidebar_categories() {
        $em = $this->container->get('doctrine')->getManager();
        $allcategories = $em->getRepository('ApplicationEpostBundle:EpostCategories')->findAll();
        //$allcategories = $em->getRepository('ApplicationEpostBundle:EpostCategories')->findByEnabled(1);
        return ($allcategories);
    }

    private function sidebar_rolls($group = null) {
        $em = $this->container->get('doctrine')->getManager();
        if (isset($group))
            $allrolls = $em->getRepository('ApplicationEpostBundle:EpostRoll')->getMyPager(array(
                        'group' => $group,
                    ))
                    ->getResult();
        else
            $allrolls = $em->getRepository('ApplicationEpostBundle:EpostRoll')->getMyPager()->getResult();
        //$allcategories = $em->getRepository('ApplicationEpostBundle:EpostCategories')->findByEnabled(1);
        return ($allrolls);
    }

    private function sidebar_oyears() {
        $em = $this->container->get('doctrine')->getManager();
        $myarr = array();
        $myarr['current_year'] = date('Y');
        $arr_years = $em->getRepository('ApplicationEpostBundle:Epost')->findaByYear($myarr['current_year']);
        //   print_r($arr_years);
        //   exit(1);
        return ($arr_years);
    }

    private function sidebar_years() {
        $em = $this->container->get('doctrine')->getManager();
        $myarr = array();
        // $myarr['current_year'] = date('Y');
        $arr_years = $em->getRepository('ApplicationEpostBundle:Epost')->CountByYears();
        //   print_r($arr_years);
        //   exit(1);
        return ($arr_years);
    }

    /* ====================================================================
     * 
     *  CREATION DU PAGINATOR
     * 
      =================================================================== */
    /* $paginator = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
      $query, $this->get('request')->query->get('page', 1), 10
      );
      $pagination->setTemplate('ApplicationCertificatsBundle:pagination:sliding.html.twig');

      //$defaut_paginator=array('pagename'=>'page1','sort'=>'sort1','sortfield'=>'sort1'); */

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
        //  $pagination->setTemplate('ApplicationEpostBundle:pagination:sliding.html.twig');

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

    /* ====================================================================
     * 
     *  DASHBOARS NEWS
     * 
      =================================================================== */

    public function indexdashboardAction() {

        return $this->render('ApplicationEpostBundle:Epost:indexdashboard.html.twig', array(
        ));
    }

    public function indextestaAction() {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array());
        //var_dump($query->getDql());exit(1);
        $paginationa = $this->createpaginator($query, 10);

        return $this->render('ApplicationEpostBundle:Epost:index-test1.html.twig', array(
                    'paginationa' => $paginationa,
        ));
        //  }
    }

    public function indextestcAction() {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array());
        //var_dump($query->getDql());exit(1);
        $paginationa = $this->createpaginator($query, 10);

        return $this->render('ApplicationEpostBundle:Epost:index-test3.html.twig', array(
                    'paginationa' => $paginationa,
        ));
    }

    public function indextestquicksandAction() {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array());
        //var_dump($query->getDql());exit(1);
        $paginationa = $this->createpaginator($query, 10);

        return $this->render('ApplicationEpostBundle:Epost:index-testquicksand.html.twig', array(
                    'paginationa' => $paginationa,
        ));
    }

    public function indextestisotopeAction() {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array());
        //var_dump($query->getDql());exit(1);
        $paginationa = $this->createpaginator($query, 10);

        return $this->render('ApplicationEpostBundle:Epost:index-testisotope.html.twig', array(
                    'paginationa' => $paginationa,
        ));
    }

    public function indextestbAction() {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array());
        //var_dump($query->getDql());exit(1);
        $paginationa = $this->createpaginator($query, 5);

        return $this->render('ApplicationEpostBundle:Epost:index-test2.html.twig', array(
                    'paginationa' => $paginationa,
        ));
        //  }
    }

    public function indexAction() {

        $em = $this->getDoctrine()->getManager();
        list($user_id, $group_id) = $this->getuserid();
        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'epost');
        $all_years = $this->sidebar_years();

        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
            'author' => $user_id,
            'alltags' => 'yes',
        ));
        $defaut_paginator_a = array('pagename' => 'page1', 'sortdir' => 'dir1', 'sortfield' => 'sort1');
        $paginationa = $this->createpaginator($query, 5, $defaut_paginator_a);

        if ($group_id != 0) {
            $query_other = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
                'non-author' => $user_id,
                'group' => $group_id,
                'alltags' => 'yes',
            ));



            $defaut_paginator_b = array('pagename' => 'page2', 'sortdir' => 'dir2', 'sortfield' => 'sort2');
            $paginationb = $this->createpaginator($query_other, 5, $defaut_paginator_b);
        } else {
            $paginationb = null;
        }
        return $this->render('ApplicationEpostBundle:Epost:index.html.twig', array(
                    'paginationa' => $paginationa,
                    'paginationb' => $paginationb,
                    'all_years' => $all_years,
        ));
    }

    //====================================================================
    // BLOG STANDARD: ALL
    //====================================================================

    private function renderBlog(array $criteria = array()) {
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'epost_indexstandard');
        list($alltags, $tagWeights) = $this->sidebar_tags();
        $allcategories = $this->sidebar_categories();
        $lastcomments = $this->sidebar_comments();
        $all_years = $this->sidebar_years();
        list($user_id, $group_id) = $this->getuserid();
        $allrolls = $this->sidebar_rolls($group_id);
        $page = $criteria['page'];
        $query = $criteria['query'];
        // $query = $em->getRepository('ApplicationEpostBundle:Epost')->myFindActif();
        $paginationa = $this->createpaginator($query, 5);
        $parameters = array(
            'paginationa' => $paginationa,
            'allcategories' => $allcategories,
            'all_years' => $all_years,
            'lastcomments' => $lastcomments,
            'alltags' => $alltags,
            'allrolls' => $allrolls,
            'tagweight' => $tagWeights,
                // 'route' => $this->getRequest()->get('_route'),
                //   'route_parameters' => $this->getRequest()->get('_route_params')
        );

        $response = $this->render($page, $parameters);



        return $response;
    }

    /*
      public function addmyimageAction(Request $request, $id) {
      if ($request->isMethod('POST')) {
      $form->bind($request);
      if ($form->isValid()) {
      $data = $form->getData();

     */

    // @Secure(roles="ROLE_ADMIN")
    //====================================================================
    // BLOG ALL
    //====================================================================
    /* public function indexAllhjkhAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
      $session = $this->getRequest()->getSession();
      $session->set('buttonretour', 'epost_indexadmin');

      $searchForm = $this->createForm(new EpostTypeFiltres());
      if ($request->isMethod('POST')) {
      //   if ($this->get('request')->query->has('submit-filter')) {
      // bind values from the request
      $form = $this->createForm(new EpostType(), $entity);
      $form->bind($request);

      $searchForm->bind($request);
      //$form->$searchForm($request);
      // $data = $form->getData();
      //var_dump($searchForm);
      $query_tmp = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(), 'query');
      $query = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($searchForm, $query_tmp);

      //     $data = $searchForm->getData();
      $session->set('post_search', $searchForm);

      }else {
      $data_session=$session->get('post_search');
      if (isset($data_session)){
      //   var_dump($data_session);exit(1);
      $searchForm->bind($data_session);
      // $data = $searchForm->getData();
      $query_tmp = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(), 'query');
      // $query = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($data_session, $query_tmp);

      }
      else {
      // $em = $this->getDoctrine()->getManager();
      $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array());
      }
      }
      $paginationa = $this->createpaginator($query, 5);
      return $this->render('ApplicationEpostBundle:Epost:indexall.html.twig', array(
      'paginationa' => $paginationa,
      'search_form' => $searchForm->createView(),
      ));
      } */

    //====================================================================
    // BLOG ALL
    //====================================================================
    public function indexAllAction() {
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'epost_indexadmin');
        $searchForm = $this->createForm(new EpostTypeFiltres());
        if ($this->get('request')->query->has('submit-filter')) {
            //  var_dump($this->get('request')->query);exit(1);
            // bind values from the request
            $searchForm->bind($this->get('request'));
            //var_dump($searchForm);
            $query_tmp = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(), 'query');
            $query = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($searchForm, $query_tmp);
            //  var_dump($query->getDql());exit(1);
        } else {
            // $em = $this->getDoctrine()->getManager();
            $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array());
            //var_dump($query->getDql());exit(1);
        }
        $paginationa = $this->createpaginator($query, 5);
        return $this->render('ApplicationEpostBundle:Epost:indexall.html.twig', array(
                    'paginationa' => $paginationa,
                    'search_form' => $searchForm->createView(),
        ));
        //  }
    }

    //====================================================================
    // BLOG STANDARD: ALL
    //====================================================================

    public function standardblogAction() {
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'epost_indexstandard');
        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
            'open_status' => 'OPEN',
        ));
        //  print_r($query);   exit(1);
        // $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPagerStandard(array(open-status));
        return $this->renderBlog(array(
                    'page' => 'ApplicationEpostBundle:Epost:standardblog.html.twig',
                    'query' => $query,
        ));
    }
    
    
     //====================================================================
    // BLOG STANDARD: ALL
    //====================================================================

    public function agencewebAction() {
        
        return $this->render('ApplicationEpostBundle:Epost:agenceweb1.html.twig', array(
        ));
    }

     //====================================================================
    // BLOG STANDARD: ALL
    //====================================================================

    public function standardblog1Action() {
        $em = $this->getDoctrine()->getManager();
        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'epost_indexstandard');
        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
            'open_status' => 'OPEN',
        ));
        //  print_r($query);   exit(1);
        // $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPagerStandard(array(open-status));
        return $this->renderBlog(array(
                    'page' => 'ApplicationEpostBundle:Epost:standardblog1.html.twig',
                    'query' => $query,
        ));
    }
    //====================================================================
    // BLOG STANDARD: index par annee
    //====================================================================

    public function indexbyyearAction($year) {
        $em = $this->getDoctrine()->getManager();
        if ($year) {
            $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
                'year' => $year,
            ));
        } else {
            $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array());
        }
        return $this->renderBlog(array(
                    'page' => 'ApplicationEpostBundle:Epost:standardblog.html.twig',
                    'query' => $query,
        ));
    }

    //====================================================================
    // BLOG STANDARD: index par categorie
    //====================================================================
    public function indexbycategoryAction($categorie) {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('ApplicationEpostBundle:EpostCategories')->findOneBy(array(
            'slug' => $categorie,
        ));

        if ($category) {
            $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
                'categorie' => $category,
                'open_status' => 'OPEN',
            ));
        } else {
            $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
                'open_status' => 'OPEN',
            ));
        }

        // $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPagerStandard(array(open-status));
        return $this->renderBlog(array(
                    'page' => 'ApplicationEpostBundle:Epost:standardblog.html.twig',
                    'query' => $query,
        ));

        /* return $this->renderBlog(array(
          'page' => 'ApplicationEpostBundle:Epost:standardblog.html.twig',
          'query' => $query,
          )); */
    }

    //====================================================================
    // BLOG STANDARD: index par tag
    //====================================================================
    public function indexbytagAction($tag) {

        $em = $this->getDoctrine()->getManager();
        $entity_tag = $em->getRepository('ApplicationEpostBundle:EpostTags')->findOneBy(array(
            'slug' => $tag,
        ));
        if ($entity_tag) {
            $id_tag = $entity_tag->getId();
            $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
                'tag' => $id_tag,
            ));
        } else {
            $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array());
        }
        return $this->renderBlog(array(
                    'page' => 'ApplicationEpostBundle:Epost:standardblog.html.twig',
                    'query' => $query,
        ));
    }

    //====================================================================
    // BLOG POST DU USER CONNECTE
    //====================================================================

    public function indexmespostsAction() {

        $em = $this->getDoctrine()->getManager();
        list($user_id, $group_id) = $this->getuserid();
        if ($group_id == 0) {
            return $this->render('ApplicationEpostBundle:Epost:choosegroup.html.twig');
        }
        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'epost_mesposts');
        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
            'author' => $user_id,
            'alltags' => 'yes',
        ));


        $pagination = $this->createpaginator($query, 10);
        return $this->render('ApplicationEpostBundle:Epost:indexmesposts.html.twig', array(
                    'paginationa' => $pagination,
        ));
    }

    //====================================================================
    // BLOG POST LIE AU GROUP DU USER CONNECTE
    //====================================================================


    public function indexpropositionsAction() {

        $em = $this->getDoctrine()->getManager();
        list($user_id, $group_id) = $this->getuserid();
        if ($group_id == 0) {
            return $this->render('ApplicationEpostBundle:Epost:choosegroup.html.twig');
        }
        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'epost_propositions');
        $query = $em->getRepository('ApplicationEpostBundle:Epost')->getMyPager(array(
            'non-author' => $user_id,
            'group' => $group_id,
            'alltags' => 'yes',
        ));
        //  $query = $em->getRepository('ApplicationEpostBundle:Epost')->myFindOtherAll($user_id, $group_id);
        $paginationa = $this->createpaginator($query, 5);
        return $this->render('ApplicationEpostBundle:Epost:indexpropositions.html.twig', array(
                    'paginationa' => $paginationa,
        ));
    }

    //====================================================================
    // CREATION ARTICLE
    //====================================================================

    public function createAction(Request $request) {
        $entity = new Epost();
        $form = $this->createForm(new EpostType(), $entity);
        $form->bind($request);
        list($user_id, $group_id) = $this->getuserid();
        $session = $this->getRequest()->getSession();
        //  $session->get('buttonretour', 'eproduit');
        $myretour = $session->get('buttonretour');

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $current_user = $em->getRepository('ApplicationSonataUserBundle:User')->find($user_id);
            $entity->setProprietaire($current_user);
            $entity->setUpdatedAt(new \DateTime());
            $em->persist($entity);
            $em->flush();

            $session = $this->getRequest()->getSession();
            $nom_modif = $entity->getName();
            $id = $entity->getId();
            $session->getFlashBag()->add('warning', "Enregistrement $nom_modif ($id) ajouté");
            return $this->redirect($this->generateUrl('epost_showstandard', array(
                                'blog_id' => $entity->getId(),
                                'slug' => $entity->getSlug())));
        }

        return $this->render('ApplicationEpostBundle:Epost:new.html.twig', array(
                    'entity' => $entity,
                    'btnretour' => $myretour,
                    'form' => $form->createView(),
        ));
    }

    //====================================================================
    // NOUVEL ARTICLE
    //====================================================================

    public function newAction() {
        $entity = new Epost();
        /*
          $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
          $path = $helper->asset($entity, 'image'); */
        $form = $this->createForm(new EpostType(), $entity);
        $session = $this->getRequest()->getSession();
        $myretour = $session->get('buttonretour');
        return $this->render('ApplicationEpostBundle:Epost:new.html.twig', array(
                    'entity' => $entity,
                    'btnretour' => $myretour,
                    'form' => $form->createView(),
        ));
    }

    //====================================================================
    // SHOW ARTICLE
    //====================================================================

    public function showAction($blog_id, $slug, $comments) {
        //public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:Epost')->find($blog_id);
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
//$path = $helper->asset($entity, 'image');
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Epost entity.');
        }

        $comments = $em->getRepository('ApplicationEpostBundle:EpostComments')
                ->getCommentsForPost($entity->getId());
        $paginationa = $this->createpaginator($comments, 5);
        $session = $this->getRequest()->getSession();
        $myretour = $session->get('buttonretour');
        list($user_id, $group_id) = $this->getuserid();
        $allrolls = $this->sidebar_rolls($group_id);
        $deleteForm = $this->createDeleteForm($blog_id);
        return $this->render('ApplicationEpostBundle:Epost:show.html.twig', array(
                    'entity' => $entity,
                    'btnretour' => $myretour,
                    'comments' => $comments,
                    'allrolls' => $allrolls,
                    'paginationa' => $paginationa,
                    //   'path' => $path,
                    'delete_form' => $deleteForm->createView(),));
    }

    //====================================================================
    // EDIT ARTICLE
    //====================================================================

    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ApplicationEpostBundle:Epost')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Epost entity.');
        }
        $session = $this->getRequest()->getSession();
        $myretour = $session->get('buttonretour');
        list($user_id, $group_id) = $this->getuserid();
        $proprietaire = $entity->getProprietaire()->getId();
        //echo "u=$user_id  p=$proprietaire<br>";
        //    exit(1);
        if ($user_id != $proprietaire) {
            return $this->render('ApplicationEpostBundle:Epost:deny.html.twig', array(
            ));
        }

        $username = $entity->getProprietaire()->getUsername();
        //echo "username=$username";exit(1);
        $editForm = $this->createForm(new EpostType($username), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ApplicationEpostBundle:Epost:edit.html.twig', array(
                    'entity' => $entity,
                    'btnretour' => $myretour,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    //====================================================================
    // UPDATE ARTICLE
    //====================================================================

    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ApplicationEpostBundle:Epost')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Epost entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EpostType(), $entity);
        $editForm->bind($request);
        $session = $this->getRequest()->getSession();
        $myretour = $session->get('buttonretour');
        if (!isset($myretour))
            $myretour = 'epost';
        if ($editForm->isValid()) {
            $entity->setUpdatedAt(new \DateTime());
            $em->persist($entity);
            $em->flush();

            $session->getFlashBag()->add('warning', "Enregistrement $id update successfull");
            $route_back = $session->get('buttonretour');
            if (isset($route_back))
                return $this->redirect($this->generateUrl($route_back, array('id' => $id)));
            else
                return $this->redirect($this->generateUrl('epost', array('id' => $id)));
        }

        return $this->render('ApplicationEpostBundle:Epost:edit.html.twig', array(
                    'entity' => $entity,
                    'btnretour' => $myretour,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    //====================================================================
    // DELETE ARTICLE
    //====================================================================

    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        // echo "id delete=$id<br>";exit(1);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ApplicationEpostBundle:Epost')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Epost entity.');
            }

            $em->remove($entity);
            $em->flush();
        } else {
            
        }

        return $this->redirect($this->generateUrl('epost'));
    }

    //====================================================================
    // CREATE DELETE ARTICLE
    //====================================================================

    /**
     * Creates a form to delete a Epost entity by id.
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

    //====================================================================
    // AJAX: LISt PAR PROJET
    //====================================================================

    public function listByProjetAction() {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest() && $request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getEntityManager();
            $id = '';
            $applis = array();
            $cert_app = array();

            $id = $request->request->get('id_projet');
            $projet = $em->getRepository('ApplicationCertificatsBundle:Projet')->find($id);

            $id_cert = $request->request->get('id_cert');
            if (isset($id_cert) && $id_cert != "create") {
                //    var_dump($id_cert);
                $cert = $em->getRepository('ApplicationCertificatsBundle:CertificatsCenter')->find($id_cert);
                foreach ($cert->getIdapplis() as $appli) {
                    array_push($cert_app, $appli->getId());
                }
                $applis['cert'] = $cert_app;
            }
            foreach ($projet->getIdapplis() as $appli) {
                //$applis[] = array($appli);
                $applis['applis'][$appli->getId()] = $appli->getNomapplis();
                //      $applis[] = array($appli->getId(), $appli->getNomapplis());
            }

            //    $appli=array(3,4);
            $response = new Response(json_encode($applis));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        // return new Response();
    }

    //====================================================================
    // CALENDAR ARTICLE
    //====================================================================

    public function CalendarEventsAction() {


        $request = $this->getRequest();
        $session = $this->getRequest()->getSession();
        if ($request->isXmlHttpRequest() && $request->getMethod() == 'POST') {
            $month = $request->request->get('month');
            if ($month < 10)
                $month = "0" . $month;
            $year = $request->request->get('year');
            $date = $year . '-' . $month;
            // $session_event=$date;
            $current_session_events = $session->get($date);
            if (!isset($current_session_events)) {
                // echo "year=$year month=$month<br>";exit(1); 
                $em = $this->getDoctrine()->getManager();
                // recuperation des parametres
                $events_date = $em->getRepository('ApplicationEpostBundle:Epost')->getMyDate($date);
                $session->set($date, $events_date);
                //  print_r($events_date); 
            } else {
                $events_date = $current_session_events;
                //   print_r($events_date); 
            }
            //  print_r($events_date); 
            $response = new Response(json_encode($events_date));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        // return new Response();
    }

    public function indexsearchAction() {

        $em = $this->getDoctrine()->getManager();
        list($user_id, $group_id) = $this->getuserid();

        $session = $this->getRequest()->getSession();
        $session->set('buttonretour', 'epost_indexserch');

        $query = $em->getRepository('ApplicationEpostBundle:Epost')->myFindAll($user_id);
        $query_other = $em->getRepository('ApplicationEpostBundle:Epost')->myFindOtherAll($user_id, $group_id);
        $paginator = $this->get('knp_paginator');
        //   $query = $em->getRepository('ApplicationEpostBundle:Epost')->findAll();
        $pagename1 = 'page1'; // Set custom page variable name
        $page1 = $this->get('request')->query->get($pagename1, 1); // Get custom page variable
        $paginationa = $paginator->paginate(
                $query, $page1, 3, array('pageParameterName' => $pagename1)
        );

        $pagename2 = 'page2'; // Set another custom page variable name
        $page2 = $this->get('request')->query->get($pagename2, 1); // Get another custom page variable
        $paginationb = $paginator->paginate(
                $query_other, $page2, 3, array('pageParameterName' => $pagename2)
        );
        //$query = $em->getRepository('ApplicationEpostBundle:CertificatsCenter')->myFindaAll();

        $paginationa->setTemplate('ApplicationEpostBundle:pagination:twitter_bootstrap_pagination.html.twig');
        $paginationb->setTemplate('ApplicationEpostBundle:pagination:twitter_bootstrap_pagination.html.twig');
        //  $pagination->setTemplate('ApplicationEpostBundle:pagination:sliding.html.twig');
        return $this->render('ApplicationEpostBundle:Epost:search.html.twig', array(
                    'paginationa' => $paginationa,
                    'paginationb' => $paginationb,
        ));
        ;
    }

    //====================================================================
    // MEDIA MANIP
    //====================================================================

    public function mediaAction(Request $request) {
// preset a default value
        $media = $this->get('sonata.media.manager.media')->create();
        $media->setBinaryContent('http://www.youtube.com/watch?v=qTVfFmENgPU');

// create the target object
        $mediaPreview = new MediaPreview();
        $mediaPreview->setMedia($media);

// create the form
        $builder = $this->createFormBuilder($mediaPreview);
        $builder->add('media', 'sonata_media_type', array(
            'provider' => 'sonata.media.provider.youtube',
            'context' => 'default'
        ));

        $form = $builder->getForm();

// bind and transform the media's binary content into real content
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            $this->getSeoPage()
                    ->setTitle($media->getName())
                    ->addMeta('property', 'og:description', $media->getDescription())
                    ->addMeta('property', 'og:type', 'video')
            ;
        }

        return $this->render('SonataDemoBundle:Demo:media.html.twig', array(
                    'form' => $form->createView(),
                    'media' => $mediaPreview->getMedia()
        ));
    }

    /**
     * @return \Sonata\SeoBundle\Seo\SeoPageInterface
     */
    public function getSeoPage() {
        return $this->get('sonata.seo.page');
    }

    //====================================================================
    // ADD/UPDATE IMAGE
    //====================================================================

    public function addmyimageAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        list($user_id, $group_id) = $this->getuserid();
        if ($user_id == 0) {
            throw $this->createNotFoundException('User not connected.');
        }
        if ($group_id == 0) {
            throw $this->createNotFoundException('User has no group.');
        }
        $current_user = $em->getRepository('ApplicationSonataUserBundle:User')->find($user_id);

        $form = $this->createFormBuilder()
                ->add('binarycontent', 'file', array('label' => 'Fichier'))
                //  ->add('description', 'text')
                ->getForm();


        $entity = $em->getRepository('ApplicationEpostBundle:Epost')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Epost entity.');
        }

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $binarycontent = $data['binarycontent'];
                $em = $this->getDoctrine()->getManager();
                $mediaManager = $this->get('sonata.media.manager.media');
                $photo = $mediaManager->create();
                $photo->setBinaryContent($binarycontent);
                $photo->setContext('default');
                $username = $current_user->getUsername();
                $photo->setAuthorName($username);
                /*
                  if (isset($data['description'])) {
                  $description = $data['binarycontent'];
                  $photo->setDescription($description);
                  }
                 * 
                 */
                $photo->setProviderName('sonata.media.provider.image');
                $mediaManager->save($photo);

                $entity->setImageMedia($photo);
                $em->flush();
                return $this->render('ApplicationEpostBundle:Epost:addimage.html.twig', array(
                            'form' => $form->createView(),
                            'entity' => $entity,
                                // 'delete_form' => $deleteForm->createView(),
                ));
            }
        }
        return $this->render('ApplicationEpostBundle:Epost:addimage.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity,
                        // 'delete_form' => $deleteForm->createView(),
        ));
    }

}
