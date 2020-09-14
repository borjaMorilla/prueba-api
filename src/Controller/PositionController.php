<?php

namespace App\Controller;

use App\Entity\Team;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;


class PositionController extends AbstractController
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }


    /**
     * @Rest\Post("/position", name="position_add", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=201,
     *     description="Posición creada correctamente"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Error al crear la nueva posición"
     * )
     *
     * @SWG\Parameter(name="name", in="body", type="string", description="Nombre del equipo", schema={})
     * @SWG\Parameter(name="short_name", in="body", type="string", description="Nombre corto del equipo", schema={})
     *
     * @SWG\Tag(name="Equipo")
     * @param Request $request
     * @return Response
     */
    public function addTeam(Request $request)
    {
        try {
            $serializer = $this->serializer;
            $em = $this->getDoctrine()->getManager();

            $data = $request->getContent();

            /** @var Team $team */

            $team = $serializer->serialize($data, 'json', [
                'circular_reference_handler' => function ($object) {
                    return $object->getId();
                }
            ]);

            $em->persist($team);
            $em->flush();

            return new Response($serializer->serialize($team, "json"), Response::HTTP_CREATED);

        } catch (Exception $ex) {
            return new Response($serializer->serialize([
                'error_code'=> 'XXX',
                'message' => $ex->getMessage()
            ], "json"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
