<?php

namespace App\Controller;

use App\Entity\Content;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractFOSRestController
{
    #[Rest\Get(path: '/{id}', name: 'get')]
    #[Rest\View(statusCode: Response::HTTP_OK, serializerGroups: [Content::GROUP_GET])]
    #[OA\Tag(name: 'Content')]
    #[ParamConverter('content', options: ['expr' => 'repository.find(id)'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns Content',
        content: new OA\JsonContent(ref: new Model(type: Content::class, groups: [Content::GROUP_GET]))
    )]
    public function getContent(Content $content): Response
    {
        $view = $this->view($content, 200);
        return $this->handleView($view);
    }
}
