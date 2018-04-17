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
        $projects = $em->getRepository('AppBundle:Project')->findAll();

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
        $em = $this->getDoctrine()->getManager();

        $projectToUpdate = $this->getDoctrine()->getRepository('AppBundle:Project')->findOneById($id);

        $form = $this->createForm(ProjectType::class, $projectToUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($projectToUpdate);
            $em->flush();

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

        $em->remove($projectToDelete);
        $em->flush();

        return $this->redirectToRoute('project');
    }

    /**
     * @Route("/project/{id}/detail/", name="project_detail", requirements= {"id"="\d+"})
     */
    public function projectDetailAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('AppBundle:Project')->find($id);

        $detailList = $this->get('knp_paginator')->paginate(

            $em->getRepository('AppBundle:Detail')->findBy(array('project' => $project->getId()), array('date' => 'desc')),
            $request->query->get('page', 1),
            10
        );

        $detail = new Detail();
        $detail->setProject($project);

        $form = $this->createForm(DetailProjectType::class, $detail);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($detail);
            $em->flush();

            return $this->redirectToRoute("project_detail", array(
                'id' => $id
            ));
        }

        return $this->render('project/project_detail.html.twig', array(
            'project' => $project,
            'detailList' => $detailList,
            'form' => $form->createView()
        ));
    }

}