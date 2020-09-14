<?php

namespace App\Controller;

use App\Entity\Player;
use App\Service\ExchangeRatesApi;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializerInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class PlayerController extends AbstractController
{

    protected $entityClass;
    private $em;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->entityClass = Player::class;

        $this->em = $em;
    }

    /**
     * @Rest\Post("/player", name="player_add", defaults={"_format":"json"})
     *
     * @SWG\Response(response=201, description="Jugador creado correctamente")
     * @SWG\Response(response=500, description="Error al crear el nuevo jugador" )
     *
     * @SWG\Parameter(name="name", in="body", type="string", description="Nombre del jugador", schema={})
     * @SWG\Parameter(name="last_name", in="body", type="string", description="Apellidos del jugador", schema={})
     * @SWG\Parameter(name="price", in="body", type="float", description="Precio del jugador", schema={})
     * @SWG\Parameter(name="team", in="body", type="object", description="Equipo del jugador", schema={})
     * @SWG\Parameter(name="positions", in="body", type="array", description="Array de posiciones del jugador", schema={})
     *
     * @SWG\Tag(name="Player")
     * @param Request $request
     * @return Response
     */
    public function addPlayer(Request $request)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $data = $request->getContent();

            /** @var Player $player */
            $player = $this->get('serializer')->deserialize($data, $this->entityClass, 'json');

            $em->persist($player);
            $em->flush();

            return new Response($this->get('serializer')->serialize($player, "json", $this->getDefaultContext('player')), Response::HTTP_CREATED);

        } catch (Exception $e) {
            return new Response($this->serializer->serialize([
                'error_code' => 'XXX',
                'message' => $e
            ], "json"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Delete("/player/{id}", name="player_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(response=201, description="Jugador creado correctamente")
     * @SWG\Response(response=500, description="Error al crear el nuevo jugador" )
     *
     * @SWG\Parameter(name="id", in="path", type="integer", description="ID del jugador", schema={})
     *
     * @SWG\Tag(name="Player")
     * @param int $id
     * @return Response
     */
    public function deletePlayer(int $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $player = $em->getRepository("App:Player")->find($id);

            if (!$player) return new Response($this->get('serializer')->serialize(['message' => 'Jugador no encontrado'], "json"), Response::HTTP_NOT_FOUND);

            $em->remove($player);
            $em->flush();

            return new Response($this->get('serializer')->serialize(['message' => 'OK'], "json"), Response::HTTP_CREATED);

        } catch (Exception $e) {
            return new Response($this->get('serializer')->serialize([
                'error_code' => 'XXX',
                'message' => $e
            ], "json"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Put("/player/{id}", name="player_edit", defaults={"_format":"json"})
     *
     *
     * @SWG\Response(response=200, description="Jugador editad correctamente.")
     * @SWG\Response(response=404, description="Jugador no encontrado")
     * @SWG\Response(response=500, description="Jugador al editar el equipo")
     *
     * @SWG\Parameter(name="id", in="path", type="integer", description="ID del jugador", schema={})
     * @SWG\Parameter(name="name", in="body", type="string", description="Nombre del jugador", schema={})
     * @SWG\Parameter(name="last_name", in="body", type="string", description="Apellidos del jugador", schema={})
     * @SWG\Parameter(name="price", in="body", type="float", description="Precio del jugador", schema={})
     * @SWG\Parameter(name="team", in="body", type="object", description="Equipo del jugador", schema={})
     * @SWG\Parameter(name="positions", in="body", type="array", description="Array de posiciones del jugador", schema={})
     *
     * @SWG\Tag(name="Player")
     */
    public function editPlayer(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $data = $request->getContent();

        $player = $em->getRepository("App:Player")->find($id);

        if (!$player) return new Response($this->get('serializer')->serialize(['message' => 'Jugador no encontrado'], "json"), Response::HTTP_NOT_FOUND);

        $this->get('serializer')->deserialize($data, $this->entityClass, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $player]);

        $em->persist($player);
        $em->flush();

        return new Response($this->get('serializer')->serialize($player, "json", $this->getDefaultContext('player')), Response::HTTP_CREATED);

    }

    /**
     * @Rest\Get("/{field_filter}/{value}/players", name="get_players_by_subresource", defaults={"_format":"json"})
     *
     * @SWG\Response(response=200, description="OK")
     * @SWG\Response(response=500, description="Error al obtener los jugadores")
     *
     * @SWG\Parameter(name="subresource_filter", in="path", type="string", description="Subrecurso por el que vamos a filtrar", schema={})
     * @SWG\Parameter(name="id", in="path", type="integer", description="Obtiene los jugadores de un equipo", schema={})
     *
     * @SWG\Tag(name="Player")
     * @param Request $request
     * @param string $field_filter
     * @param string|int $value
     * @return Response
     */
    public function getPlayersBySubresource(Request $request, string $field_filter, string $value)
    {
        try {

            $priceUSD = $request->get('priceUSD', 0);
            $em = $this->getDoctrine()->getManager();

            $subresources = ['position', 'team'];

            if (!in_array($field_filter, $subresources)) throw new Exception("No existe el filtro para el campo: $field_filter");

            $function = "findBy" . lcfirst($field_filter);
            $players = $em->getRepository("App:Player")->$function($value);

            return new Response($this->get('serializer')->serialize($players, "json", $this->getDefaultContext('player', $priceUSD)), Response::HTTP_OK);

        } catch (Exception $e) {
            return new Response($this->get('serializer')->serialize([
                'error_code' => 'XXX',
                'message' => $e
            ], "json"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @Rest\Get("/team/{id_team}/position/{id_position}/players", name="get_players_by_team_and_position", defaults={"_format":"json"})
     *
     * @SWG\Response(response=200, description="OK")
     * @SWG\Response(response=500, description="Error al obtener los jugadores")
     *
     * @SWG\Parameter(name="id_team", in="path", type="integer", description="ID del equipo", schema={})
     * @SWG\Parameter(name="id_position", in="path", type="integer", description="ID de la posición", schema={})
     *
     * @SWG\Tag(name="Player")
     * @param Request $request
     * @param int $id_team
     * @param int $id_position
     * @return Response
     */
    public function getPlayersByTeamAndPosition(Request $request, int $id_team, int $id_position)
    {
        try {

            $priceUSD = $request->get('priceUSD', 0);

            $em = $this->getDoctrine()->getManager();

            $players = $em->getRepository("App:Player")->findByTeamAndPosition($id_team, $id_position, true);

            return new Response($this->get('serializer')->serialize($players, "json", $this->getDefaultContext('player', $priceUSD)), Response::HTTP_OK);

        } catch (Exception $e) {
            return new Response($this->get('serializer')->serialize([
                'error_code' => 'XXX',
                'message' => $e
            ], "json"), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getDefaultContext($groups, $changeCourrency = false)
    {

        $groups = is_array($groups) ? $groups : [$groups];

        $context = [
            'groups' => $groups,
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];

        if ($changeCourrency) {

            $priceCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
                return $innerObject ? $innerObject * ExchangeRatesApi::getExchangeRate() : 0;
            };

            $context[AbstractNormalizer::CALLBACKS] = [
                'price' => $priceCallback,
            ];
        }

        return $context;
    }
}
