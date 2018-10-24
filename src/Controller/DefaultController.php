<?php


namespace App\Controller;


use App\Tester;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{

    /**
     * @param Tester $tester
     * @return Response
     */
    public function index(Tester $tester)
    {
        return new Response(
            sprintf('<pre>%s</pre>', var_export($tester->run(), true))
        );
    }

}