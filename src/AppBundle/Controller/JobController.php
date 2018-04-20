<?php
//src/AppBundle/Controller/JobController.php

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use AppBundle\Entity\Project;
use AppBundle\Form\JobType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends Controller
{
    /**
     * @Route("/job/", name="job")
     */
    public function jobAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //Get All jobs and display last 10 by the knp_paginator Bundle
        $jobs = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Job')->findAll(),
            $request->query->get('page', 1),
            10
        );

        if(!$jobs){
            throw $this->createNotFoundException();
        }

        $searchForm = $this->createFormBuilder()
            ->add('search', SubmitType::class, array('label' => 'Rechercher', 'attr' => array('style' => 'float: right')))
            ->add('word', TextType::class, array('label' => false, 'attr' => array('style' => 'float: right; width : 150px ; margin-right : 10px', 'placeholder' => 'Rechercher...')))
            ->getForm();

        //Search form
        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted() && $searchForm->isValid()){

            $word = $searchForm->getData()['word'];

            $em = $this->getDoctrine()->getManager();
            $projectsSearched = $em->getRepository(Project::class)->search($word);

            return $this->render('project/project.html.twig', array(
                'projects' => $projectsSearched,
                'fromSearch' => 'Resultat de recherche'));
        }

        return $this->render('job/job.html.twig', array('jobs' => $jobs,
            'searchForm' => $searchForm->createView()));
    }

    /**
     * @Route("/job/new/", name="job_new")
     */
    public function jobNewAction(Request $request)
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Métier ajouté avec succès");

            return $this->redirectToRoute('job');
        }

        //Search form
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

        return $this->render('job/job_new.html.twig', array(
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView(),
        ));
    }

    /**
     * @Route("/job/edit/{id}/", name="job_edit", requirements = {"id"="\d+"})
     */
    public function jobEditAction(Request $request, $id)
    {
        $jobToUpdate = $this->getDoctrine()->getRepository('AppBundle:Job')->findOneById($id);

        if(!$jobToUpdate){
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(JobType::class, $jobToUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($jobToUpdate);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Métier édité avec succès");

            return $this->redirectToRoute('job');
        }

        //Search form
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

        return $this->render('job/job_edit.html.twig', array(
            'form' => $form->createView(),
            'id' => $id,
            'searchForm' => $searchForm->createView(),
        ));
    }

    /**
     * @Route("/job/delete/{id}", name="job_delete", requirements = {"id"="\d+"})
     */
    public function jobDeleteAction(Request $request, $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $jobToDelete = $this->getDoctrine()->getRepository('AppBundle:Job')->findOneById($id);

            if(!$jobToDelete){
                throw $this->createNotFoundException();
            }

            $em->remove($jobToDelete);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Le métier a bien été supprimé");
            return $this->redirectToRoute('job');

        } catch (ForeignKeyConstraintViolationException $e) {

            $this->get('session')->getFlashBag()->add('error', "Le métier appartient à un employé, suppression impossible");
            return $this->redirectToRoute('job');
        }

    }
}