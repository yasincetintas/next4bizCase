<?php


namespace App\Controller;

use App\Model\Request\DateCount;
use App\Service\Test\TestService as TestServiceAlias;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TestController
 * @package App\Controller
 * @Route ("/api/test")
 */
class TestController extends BaseController
{
    /**
     * @Route ("/itemCount", methods={"POST"}, name="get_date_count")
     *
     * @param Request $request
     * @param TestServiceAlias $service
     *
     * @return Response
     */
    public function itemCount(Request $request, TestServiceAlias $service): Response
    {
        /** @var DateCount $dateModel */
        $dateModel = $this->validateRequest($request->getContent(),DateCount::class);

        $result = $service->getDateCount($dateModel);

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}