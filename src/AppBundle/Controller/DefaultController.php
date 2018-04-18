<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function dashboardAction(Request $request)
    {
        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->findAll();
        $employees = $this->getDoctrine()->getRepository('AppBundle:Employee')->findAll();
        $details = $this->getDoctrine()->getRepository('AppBundle:Detail')->findAll();

        $totalProjects = count($projects);

        $projectsSend = array();
        $totalCapex = 0;
        foreach ($projects as $project){
            if($project->getSend()){
                array_push($projectsSend, $project);
            }
            if($project->getType() == "CAPEX"){
                $totalCapex += 1;
            }
        }

        $duration = 0;
        foreach ($details as $detail)
        {
            $duration += $detail->getDuration();
        }





        // replace this example code with whatever you need
        return $this->render('dashboard/dashboard.html.twig', array(
            'totalProjects' => $totalProjects,
            'totalProjectsSend' => count($projectsSend),
            'totalEmployees' => count($employees),
            'duration' => $duration,
            'totalCapex' => $totalCapex
        ));
    }


}
