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
        // replace this example code with whatever you need
        return $this->render('dashboard/dashboard.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/employee/", name="employee")
     */
    public function employeeAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('employee/employee.html.twig');
    }

    /**
     * @Route("/employee/edit/", name="employee_edit")
     */
    public function employeeEditAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('employee/employee_edit.html.twig');
    }

    /**
     * @Route("/project/", name="project")
     */
    public function projectAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('project/project.html.twig');
    }

    /**
     * @Route("/project/edit/", name="project_edit")
     */
    public function projectEditAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('project/project_edit.html.twig');
    }

    /**
     * @Route("/job/", name="job")
     */
    public function jobAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('job/job.html.twig');
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
