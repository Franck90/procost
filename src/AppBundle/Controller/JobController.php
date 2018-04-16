<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use AppBundle\Form\JobType;
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
        $job = $em->getRepository('AppBundle:Job')->findAll();

        return $this->render('job/job.html.twig', array(
            "jobs" => $job)
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
        $em = $this->getDoctrine()->getManager();

        $jobToUpdate = $this->getDoctrine()->getRepository('AppBundle:Job')->findOneById($id);

        $form = $this->createForm(JobType::class, $jobToUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($jobToUpdate);
            $em->flush();

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
        $em = $this->getDoctrine()->getManager();

        $jobToDelete = $this->getDoctrine()->getRepository('AppBundle:Job')->findOneById($id);

        $em->remove($jobToDelete);
        $em->flush();

        return $this->redirectToRoute('job');
    }
}