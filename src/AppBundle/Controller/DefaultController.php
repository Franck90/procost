<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Form\SearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;



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

        $searchForm = $this->createFormBuilder()
            ->add('search', SubmitType::class, array('label' => 'Rechercher', 'attr' => array('style' => 'float: right')))
            ->add('word', TextType::class, array('label' => false, 'attr' => array('style' => 'float: right; width : 150px ; margin-right : 10px')))
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
            'lastDetails' => $lastDetails,
            'searchForm' => $searchForm->createView()
        ));
    }

    /**
     * @Route("/search/{word}", name="search")
     */
    public function searchAction(Request $request, $word)
    {
        dump($word);
        die;
        return $this->redirectToRoute("dashboard");
    }

}
