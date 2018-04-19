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
        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')->findBy(array(), array('date' => 'asc'));
        $employees = $this->getDoctrine()->getRepository('AppBundle:Employee')->findAll();
        $details = $this->getDoctrine()->getRepository('AppBundle:Detail')->findBy(array(), array('date' => 'asc'));

        $totalProjects = count($projects);

        $totalSend = 0;
        $totalCapex = 0;


        $projectsResumee = array();

        foreach ($projects as $project){

            if($project->getSend()){
                $totalSend += 1;
            }
            if($project->getType() == "CAPEX"){
                $totalCapex += 1;
            }

            $costProject = 0;

            foreach ($details as $detail)
            {
                if($detail->getProject()->getId() == $project->getId())
                {
                    $costProject += $detail->getDuration() * $detail->getEmployee()->getCost();
                }
            }

            array_push($projectsResumee, array(
                'id' => $project->getId(),
                'name' => $project->getName(),
                'type' => $project->getType(),
                'date' => $project->getDate(),
                'cost' => $costProject,
                'send' => $project->getSend()
            ));
        }

        $lastProjects = array();

        for( $i = 0 ; $i < 5 ; $i++)
        {
            $lastProject = array_pop($projectsResumee);

            if($lastProject == null)
            {
                break;
            }
            array_push($lastProjects, $lastProject);
        }

        $percentSend = 0;
        $percentCapex = 0;

        if($totalProjects > 0)
        {
            $percentCapex = $totalCapex/$totalProjects * 100;
            $percentSend = $totalSend/$totalProjects * 100;
        }

        $percentOpex = 100 - $percentCapex;
        $percentNotSend = 100 - $percentSend;


        $duration = 0;

        foreach ($details as $detail)
        {
            $duration += $detail->getDuration();
        }

        $lastDetails = array();
        for( $i = 0 ; $i < 10 ; $i++)
        {
            $lastDetail = array_pop($details);

            if($lastDetail == null)
            {
                break;
            }
            array_push($lastDetails, $lastDetail);
        }

        $topEmployee = 0;
        $statEmployee = array();

        foreach ($employees as $employee)
        {
            $costEmployee = 0;

            foreach ($details as $detail)
            {
                if( $employee->getId() == $detail->getEmployee()->getId())
                {
                    $costEmployee += $detail->getDuration() * $employee->getCost();
                }
            }
            $statEmployee[$employee->getId()] = $costEmployee;
        }

        foreach ($employees as $employee)
        {
            if($employee->getId() == array_keys($statEmployee, max($statEmployee))[0] ){
                $topEmployee = $employee;
            }
        }

        return $this->render('dashboard/dashboard.html.twig', array(
            'totalProjects' => $totalProjects,
            'totalSend' => $totalSend,
            'totalEmployees' => count($employees),
            'duration' => $duration,
            'percentCapex' => round($percentCapex),
            'percentOpex' => round($percentOpex),
            'percentSend' => round($percentSend),
            'percentNotSend' => round($percentNotSend),
            'topEmployee' => $topEmployee,
            'topCostEmployee' => max($statEmployee),
            'lastProjects' => $lastProjects,
            'lastDetails' => $lastDetails
        ));
    }

}
