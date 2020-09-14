<?php

namespace App\Controller;

use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


class TeamController extends AbstractController
{

    protected $entityClass;
    private $em;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->entityClass = Team::class;

        $this->em = $em;
    }


    /**
     * @Rest\Post("/team", name="team_add", defaults={"_format":"json"})
     *
     * @SWG\Response(response=201, description="Equipo creado correctamente")
     * @SWG\Response( response=500, description="Error al crear el nuevo equipo")
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

            $data = $request->getContent();

            /** @var Team $team */
            $team = $this->get('serializer')->deserialize($data, $this->entityClass, 'json');

            $this->em->persist($team);
            $this->em->flush();

            return new Response($this->get('serializer')->serialize($team, "json", $this->getDefaultContext('show_team')), Response::HTTP_CREATED);

        } catch (Exception $e) {
            return new Response($this->serializer->serialize([
                'error_code' => 'XXX',
                'message' => $e
            ], "json"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Delete("/team/{id}", name="team_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(response=201, description="Equipo borrado correctamente")
     * @SWG\Response(response=404, description="Equipo no encontrado")
     * @SWG\Response(response=500, description="Error al borrar el equipo" )
     *
     * @SWG\Parameter(name="id", in="path", type="integer", description="ID del jugador", schema={})
     *
     * @SWG\Tag(name="Team")
     * @param int $id
     * @return Response
     */
    public function deleteTeam(int $id)
    {
        try {

            $team = $this->em->getRepository("App:Team")->find($id);

            if (!$team) return new Response($this->get('serializer')->serialize(['message' => 'Equipo no encontrado'], "json"), Response::HTTP_NOT_FOUND);

            $this->em->remove($team);
            $this->em->flush();

            return new Response($this->get('serializer')->serialize(['message' => 'OK'], "json"), Response::HTTP_CREATED);

        } catch (Exception $e) {
            return new Response($this->get('serializer')->serialize([
                'error_code' => 'XXX',
                'message' => $e
            ], "json"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Put("/team/{id}", name="team_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(response=200, description="Equipo editad correctamente.")
     * @SWG\Response(response=404, description="Equipo no encontrado")
     * @SWG\Response(response=500, description="Error al editar el equipo")
     *
     * @SWG\Parameter(name="name", in="body", type="string", description="Nombre del equipo", schema={})
     * @SWG\Parameter(name="short_name", in="body", type="string", description="Nombre corto del equipo", schema={})
     *
     * @SWG\Tag(name="Team")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editTeam(Request $request, $id)
    {

        $data = $request->getContent();

        $team = $this->em->getRepository("App:Team")->find($id);

        if (!$team) return new Response($this->get('serializer')->serialize(['message' => 'Equipo no encontrado'], "json"), Response::HTTP_NOT_FOUND);

        $this->get('serializer')->deserialize($data, $this->entityClass, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $team]);

        $this->em->persist($team);
        $this->em->flush();

        return new Response($this->get('serializer')->serialize($team, "json", $this->getDefaultContext('show_team')), Response::HTTP_CREATED);

    }


    /**
     * @Rest\Get("/teams", name="get_all_teams", defaults={"_format":"json"})
     *
     * @SWG\Response(response=200, description="OK")
     * @SWG\Response(response=500, description="Error al obtener los eqipos")
     *
     * @SWG\Parameter(name="id_team", in="path", type="integer", description="ID del equipo", schema={})
     * @SWG\Parameter(name="id_position", in="path", type="integer", description="ID de la posición", schema={})
     *
     * @SWG\Tag(name="Team")
     * @return Response
     */
    public function getTeams()
    {
        try {

            $teams = $this->em->getRepository("App:Team")->findAll();

            return new Response($this->get('serializer')->serialize($teams, "json", $this->getDefaultContext('show_team')), Response::HTTP_OK);

        } catch (Exception $e) {
            return new Response($this->get('serializer')->serialize([
                'error_code' => 'XXX',
                'message' => $e
            ], "json"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getDefaultContext($groups)
    {

        $groups = is_array($groups) ? $groups : [$groups];

        $context = [
            'groups' => $groups,
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];

        return $context;
    }
}
