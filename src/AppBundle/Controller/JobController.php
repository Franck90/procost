<?php

namespace AppBundle\Controller;

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
        // replace this example code with whatever you need
        return $this->render('job/job_edit.html.twig');
    }
}