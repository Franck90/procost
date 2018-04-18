<?php
/**
 * Created by PhpStorm.
 * User: Franck
 * Date: 15/04/2018
 * Time: 17:17
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Detail;
use AppBundle\Entity\Project;
use AppBundle\Form\DetailProjectType;
use AppBundle\Form\DetailType;
use AppBundle\Form\ProjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    /**
     * @Route("/project/", name="project")
     */
    public function projectAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $projects = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Project')->findAll(),
            $request->query->get('page', 1),
            10
        );

        if(!$projects){
            throw $this->createNotFoundException();
        }

        return $this->render('project/project.html.twig', array(
                "projects" => $projects)
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

        return $this->render('project/project_new.html.twig', array(
            'form' => $form->createView()
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

        return $this->render('project/project_edit.html.twig', array(
            'form' => $form->createView(),
            'id' => $id
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

        $detailList = $this->get('knp_paginator')->paginate(
            $em->getRepository('AppBundle:Detail')->findBy(array('project' => $project->getId()), array('date' => 'desc')),
            $request->query->get('page', 1),
            10
        );

        if(!$detailList)
        {
            throw $this->createNotFoundException();
        }

        $detail = new Detail();
        $detail->setProject($project);

        $form = $this->createForm(DetailProjectType::class, $detail);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($detail);
            $em->flush();

            $this->get('session')->getFlashBag()->add('confirmation', "Succès de l'ajout de temps sur le projet");

            return $this->redirectToRoute("project_detail", array(
                'id' => $id
            ));
        }

        $employeesOnProject = array();
        $employees = $em->getRepository('AppBundle:Employee')->findAll();

        if($employees)
        {
            throw $this->createNotFoundException();
        }

        $employeesIdOnProject = $em->getRepository('AppBundle:Detail')->findBy(array('project' => $id));

        foreach ($employeesIdOnProject as $employeeIdOnProject){

            foreach ($employees as $employee){

                if( $employee->getId() == $employeeIdOnProject->getEmployee()->getId()){
                    array_push($employeesOnProject, $employee);
                }
            }
        }

        $totalCost = 0;

        foreach ($employeesOnProject as $employeeOnProject){
            foreach ($detailList as $detail){
                if($employeeOnProject->getId() == $detail->getEmployee()->getId()){
                    $totalCost += ($detail->getDuration() * $employeeOnProject->getCost());
                }
            }
        }

        return $this->render('project/project_detail.html.twig', array(
            'project' => $project,
            'detailList' => $detailList,
            'totalEmployee' => count($employeesOnProject),
            'totalCost' => $totalCost,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("project/{id}/detail/{idDetail}/delete", name="detail_project_delete", requirements={"id"="\d+", "idDetail"="\d+"})
     */
    public function detailDeleteAction(Request $request, $id, $idDetail)
    {
        $em = $this->getDoctrine()->getManager();
        $detail = $em->getRepository('AppBundle:Detail')->find($idDetail);

        if($detail)
        {
            throw $this->createNotFoundException();
        }

        if($detail->getProject()->getSend() == true || $detail->getEmployee()->getActive() == false)
        {
            $this->get('session')->getFlashBag()->add('error', "Suppression impossible, le projet est livré ou l'employé est archivé");

            return $this->redirectToRoute('project_detail', array(
                "id" => $id)
            );
        }

        $em->remove($detail);
        $em->flush();

        $this->get('session')->getFlashBag()->add('confirmation', "Succès de la supression de temps sur le projet");

        return $this->redirectToRoute('project_detail', array(
                "id" => $id)
        );
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
            $this->get('session')->getFlashBag()->add('error', "Projet définit comme étant en développement ");
        }

        return $this->redirectToRoute('project');
    }

}