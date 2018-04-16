<?php
/**
 * Created by PhpStorm.
 * User: Franck
 * Date: 15/04/2018
 * Time: 17:17
 */

namespace AppBundle\Controller;

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
     * @Route("/project/edit/", name="project_edit")
     */
    public function projectEditAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('project/project_edit.html.twig');
    }
}