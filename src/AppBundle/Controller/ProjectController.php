<?php
//src/AppBundle/Controller/ProjectController.php

namespace AppBundle\Controller;

use AppBundle\Entity\Detail;
use AppBundle\Entity\Project;
use AppBundle\Form\DetailProjectType;
use AppBundle\Form\ProjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    /**
     * @Route("/project/", name="project")
     */
    public function projectAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //Get All projects and display last 10 by the knp_paginator Bundle
        $projects = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Project')->findBy(array(), array("date" => "desc")),
            $request->query->get('page', 1),
            10
        );

        if(!$projects){
            throw $this->createNotFoundException();
        }

        //searchForm
        $searchForm = $this->createFormBuilder()
            ->add('search', SubmitType::class, array('label' => 'Rechercher', 'attr' => array('style' => 'float: right')))
            ->add('word', TextType::class, array('label' => false, 'attr' => array('style' => 'float: right; width : 150px ; margin-right : 10px', 'placeholder' => 'Rechercher...')))
            ->getForm();

        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted() && $searchForm->isValid()){

            $word = $searchForm->getData()['word'];

            $em = $this->getDoctrine()->getManager();
            $projectsSearched = $em->getRepository(Project::class)->search($word);

            return $this->render('project/project.html.twig', array(
                'projects' => $projectsSearched,
                'fromSearch' => 'Resultat de recherche'));
        }

        return $this->render('project/project.html.twig', array(
                "projects" => $projects,
                'searchForm' => $searchForm->createView())
        );
    }

    /**
     * @Route("/project/new/", name="project_new")
     */
    public function projectNewAction(Request $request)
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Projet ajouté avec succès");

            return $this->redirectToRoute('project');
        }

        //searchForm
        $searchForm = $this->createFormBuilder()
            ->add('search', SubmitType::class, array('label' => 'Rechercher', 'attr' => array('style' => 'float: right')))
            ->add('word', TextType::class, array('label' => false, 'attr' => array('style' => 'float: right; width : 150px ; margin-right : 10px', 'placeholder' => 'Rechercher...')))
            ->getForm();

        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted() && $searchForm->isValid()){

            $word = $searchForm->getData()['word'];

            $em = $this->getDoctrine()->getManager();
            $projectsSearched = $em->getRepository(Project::class)->search($word);

            return $this->render('project/project.html.twig', array(
                'projects' => $projectsSearched,
                'fromSearch' => 'Resultat de recherche'));
        }

        return $this->render('project/project_new.html.twig', array(
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView()
        ));
    }

    /**
     * @Route("/project/{id}/edit/", name="project_edit", requirements = {"id"="\d+"})
     */
    public function projectEditAction(Request $request, $id)
    {
        $projectToUpdate = $this->getDoctrine()->getRepository('AppBundle:Project')->findOneById($id);

        if(!$projectToUpdate){
            throw $this->createNotFoundException();
        }

        if($projectToUpdate->getSend())
        {
            $this->get('session')->getFlashBag()->add('error', "Projet en production, édition de ce dernier impossible");

            return $this->redirectToRoute('employee');
        }

        $form = $this->createForm(ProjectType::class, $projectToUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($projectToUpdate);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Edition du projet réalisé avec succès");

            return $this->redirectToRoute('project');
        }

        //searchForm
        $searchForm = $this->createFormBuilder()
            ->add('search', SubmitType::class, array('label' => 'Rechercher', 'attr' => array('style' => 'float: right')))
            ->add('word', TextType::class, array('label' => false, 'attr' => array('style' => 'float: right; width : 150px ; margin-right : 10px', 'placeholder' => 'Rechercher...')))
            ->getForm();

        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted() && $searchForm->isValid()){

            $word = $searchForm->getData()['word'];

            $em = $this->getDoctrine()->getManager();
            $projectsSearched = $em->getRepository(Project::class)->search($word);

            return $this->render('project/project.html.twig', array(
                'projects' => $projectsSearched,
                'fromSearch' => 'Resultat de recherche'));
        }

        return $this->render('project/project_edit.html.twig', array(
            'form' => $form->createView(),
            'id' => $id,
            'searchForm' => $searchForm->createView()
        ));
    }

    /**
     * @Route("/project/{id}/delete/", name="project_delete", requirements = {"id"="\d+"})
     */
    public function projectDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $projectToDelete = $this->getDoctrine()->getRepository('AppBundle:Project')->findOneById($id);

        if(!$projectToDelete){
            throw $this->createNotFoundException();
        }

        $detailProjectToDelete = $em->getRepository('AppBundle:Detail')->findBy(array('project' => $projectToDelete->getId()), array());

        $totalCost = 0;
        $arrayEmployee = array();

        foreach ($detailProjectToDelete as $detail)
        {
            $totalCost += $detail->getEmployee()->getCost() * $detail->getDuration();
            array_push($arrayEmployee,$detail->getEmployee()->getId() );
        }

        $totalEmployee = count(array_unique($arrayEmployee));

        //Email
        $message = \Swift_Message::newInstance()
            ->setSubject('Suppression d\'un projet')
            ->setFrom('franck.moniez90@gmail.com')
            ->setTo('franck.moniez90@gmail.com')
            ->setBody(
                $this->renderView("mail/mail.html.twig",array(
                    'project' => $projectToDelete,
                    'totalCost' => $totalCost,
                    'totalEmployee' => $totalEmployee
                )),'text/html');

        //Sending Email
        $this->get('mailer')->send($message);

        $em->remove($projectToDelete);
        $em->flush();

        $this->get('session')->getFlashBag()->add('confirmation', "Suppression du projet réalisé avec succès");

        return $this->redirectToRoute('project');
    }

    /**
     * @Route("/project/{id}/detail/", name="project_detail", requirements= {"id"="\d+"})
     */
    public function projectDetailAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('AppBundle:Project')->find($id);

        if(!$project){
            throw $this->createNotFoundException();
        }

        //Get All details and display last 10 by the knp_paginator Bundle
        $detailList = $this->get('knp_paginator')->paginate(
            $em->getRepository('AppBundle:Detail')->findBy(array('project' => $project->getId()), array('date' => 'desc')),
            $request->query->get('page', 1),
            10
        );

        if(!$detailList)
        {
            throw $this->createNotFoundException();
        }

        //For new detail
        $detail = new Detail();
        $detail->setProject($project);

        $form = $this->createForm(DetailProjectType::class, $detail);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($detail->getProject()->getSend() == true || $detail->getEmployee()->getActive() == false)
            {
                $this->get('session')->getFlashBag()->add('error', "Ajout de temps impossible, le projet est livré ou l'employé est archivé");

                return $this->redirectToRoute("project_detail", array(
                    'id' => $id
                ));
            }

            $em->persist($detail);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Succès de l'ajout de temps sur le projet");

            return $this->redirectToRoute("project_detail", array(
                'id' => $id
            ));
        }

        //Stat for the template
        $totalCost = 0;
        $arrayEmployee = array();

        foreach ($detailList as $detail)
        {
            $totalCost += $detail->getEmployee()->getCost() * $detail->getDuration();
            array_push($arrayEmployee,$detail->getEmployee()->getId() );
        }

        $totalEmployee = count(array_unique($arrayEmployee));

        $searchForm = $this->createFormBuilder()
            ->add('search', SubmitType::class, array('label' => 'Rechercher', 'attr' => array('style' => 'float: right')))
            ->add('word', TextType::class, array('label' => false, 'attr' => array('style' => 'float: right; width : 150px ; margin-right : 10px', 'placeholder' => 'Rechercher...')))
            ->getForm();

        //searchForm
        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted() && $searchForm->isValid()){

            $word = $searchForm->getData()['word'];

            $em = $this->getDoctrine()->getManager();
            $projectsSearched = $em->getRepository(Project::class)->search($word);

            return $this->render('project/project.html.twig', array(
                'projects' => $projectsSearched,
                'fromSearch' => 'Resultat de recherche'));
        }

        return $this->render('project/project_detail.html.twig', array(
            'project' => $project,
            'detailList' => $detailList,
            'totalEmployee' => $totalEmployee,
            'totalCost' => $totalCost,
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView()
        ));
    }

    /**
     * @Route("/project/{id}/send/", name="project_send", requirements = {"id"="\d+"})
     */
    public function projectSendAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $projectToActivate = $this->getDoctrine()->getRepository('AppBundle:Project')->findOneById($id);

        if(!$projectToActivate){
            throw $this->createNotFoundException();
        }

        if($projectToActivate->getSend() == true){
            $projectToActivate->setSend(false);
        }else{
            $projectToActivate->setSend(true);
        }

        $em->persist($projectToActivate);
        $em->flush();

        if($projectToActivate->getSend() == true){
            $this->get('session')->getFlashBag()->add('confirmation', "Projet définit comme étant livré ");
        }else{
            $this->get('session')->getFlashBag()->add('confirmation', "Projet définit comme étant en développement ");
        }

        return $this->redirectToRoute('project');
    }

}