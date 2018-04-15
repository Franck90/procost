<?php
/**
 * Created by PhpStorm.
 * User: Franck
 * Date: 15/04/2018
 * Time: 17:16
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class EmployeeController extends Controller
{
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
}