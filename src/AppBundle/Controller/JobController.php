<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use AppBundle\Form\JobType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        $jobs = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Job')->findAll(),
            $request->query->get('page', 1),
            10
        );

        if(!$jobs){
            throw $this->createNotFoundException();
        }

        return $this->render('job/job.html.twig', array(
            "jobs" => $jobs)
        );
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

        return $this->render('job/job_new.html.twig', array(
            'form' => $form->createView()
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

        return $this->render('job/job_edit.html.twig', array(
            'form' => $form->createView(),
            'id' => $id
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

            $this->get('session')->getFlashBag()->add('error', "Le métier appartient à un employer, suppression impossible");
            return $this->redirectToRoute('job');
        }

    }
}