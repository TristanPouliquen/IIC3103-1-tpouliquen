<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class APIController extends Controller
{
    /**
     * @Route("/validarFirma")
     * @Method("POST")
     */
    public function indexAction(Request $request)
    {
        $message = $request->query->get('message');
        $hash = $request->query->get('hash');

        if (empty($message) or empty($hash)) {
            return new Response('One or more arguments are missing in the request', 400);
        }

        try {
            $untouched_message = (bool) ($hash === strtoupper(hash("sha256", $message)));
        } catch(\Exception $e) {
            return new Response('An error happened. Check the validity of your parameters and try resubmit your request', 500);
        }

        $array = ['original_message' => $message, 'untouched_message' => $untouched_message];

        return new JsonResponse($array);
    }

    /**
     * @Route("/status")
     * @Method("GET")
     */
    public function statusAction(Request $request)
    {
        return new Response($status = 201);
    }

    /**
     * @Route("/texto")
     * @Method("GET")
     */
    public function textAction(Request $request)
    {
        $text = file_get_contents("https://s3.amazonaws.com/files.principal/texto.txt");

        try {
            $hash = strtoupper(hash("sha256", $text));
        } catch(\Exception $e) {
            return new Response('An error happened. Try to resubmit your request later', 500);
        }

        $array = ['original_text' => $text, 'hash' => $hash];

        return new JsonResponse($array);
    }
}
