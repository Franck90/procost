<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Job;
use AppBundle\Form\JobType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


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
     * @Route("/job/edit/", name="job_edit")
     */
    public function jobEditAction(Request $request)
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

        /*if ($form->isValid()) {


            $job = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job');

        }*/

        return $this->render('job/job_edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}