<?php

namespace Belcoder\AssistantBundle\Handler;

class CRUDHandler
{
    public static function getRouting($entity)
    {
        $body = '' .
'{{entity}}:
    resource: "@AppBundle/Controller/{{Entity}}RESTController.php"
    type:   rest
    prefix:   /' .
        '';

        $body = str_replace('{{Entity}}', $entity, $body);
        $body = str_replace('{{entity}}', strtolower($entity), $body);

        return $body;
    }

    public static function getController($entity)
    {
        $body = '' .
'<?php

namespace AppBundle\Controller;

use AppBundle\Entity\{{Entity}};
use AppBundle\Repository\{{Entity}}Repository;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\View\View as FOSView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * {{Entity}} controller.
 * @RouteResource("{{Entity}}")
 */
class {{Entity}}RESTController extends FOSRestController
{
    /** @var {{Entity}}Repository $repository */
    private $repository = null;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $this->repository = $this
            ->getDoctrine()
            ->getRepository(\'AppBundle:{{Entity}}\');
    }

    /**
     * @ApiDoc(
     *      section="{{Entity}}",
     *      description="Get one {{entity}}",
     *      statusCodes={
     *          200="HTTP_OK"
     *      }
     * )
     * @param {{Entity}} $entity
     * @return FOSView
     */
    public function getAction({{Entity}} $entity)
    {
        return FOSView::create($entity, Response::HTTP_OK);
    }

    /**
     * @ApiDoc(
     *      section="{{Entity}}",
     *      description="Get all {{entity}}",
     *      statusCodes={
     *          200="HTTP_OK",
     *          204="HTTP_NO_CONTENT",
     *      },
     *      parameters={
     *          {"name"="page", "dataType"="integer", "required"=false, "source"="query", "description"="..."},
     *          {"name"="per_page", "dataType"="integer", "required"=false, "source"="query", "description"="..."},
     *          {"name"="order_by", "dataType"="string", "required"=false, "source"="query", "description"="..."},
     *          {"name"="order_direction", "dataType"="string", "required"=false, "source"="query", "description"="..."}
     *      }
     * )
     * @param Request $request
     * @return FOSView
     */
    public function cgetAction(Request $request)
    {
        $rows = $this->repository->cget($request);
        return FOSView::create(
            $rows[\'data\'] ? $rows : null,
            $rows[\'data\'] ? Response::HTTP_OK : Response::HTTP_NO_CONTENT
        );
    }

    /**
     * @ApiDoc(
     *      section="{{Entity}}",
     *      description="Create {{entity}}",
     *      statusCodes={
     *          201="HTTP_CREATED",
     *          422="HTTP_UNPROCESSABLE_ENTITY"
     *      },
     *      parameters={
     *          {"name"="name", "dataType"="string", "required"=true, "source"="body"}
     *      }
     * )
     * @param Request $request
     * @return FOSView
     */
    public function postAction(Request $request)
    {
        $result = $this->repository->create($request, $this->get(\'validator\'));

        if ($result instanceof {{Entity}}) {
            return FOSView::create($result, Response::HTTP_CREATED);
        }

        return FOSView::create([\'errors\' => $result], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @ApiDoc(
     *      section="{{Entity}}",
     *      description="Edit {{entity}}",
     *      statusCodes={
     *          201="HTTP_CREATED",
     *          422="HTTP_UNPROCESSABLE_ENTITY"
     *      },
     *      parameters={
     *          {"name"="name", "dataType"="string", "required"=true, "source"="body"}
     *      }
     * )
     * @param Request $request
     * @param {{Entity}} $entity
     * @return FOSView
     */
    public function putAction(Request $request, {{Entity}} $entity)
    {
        $result = $this->repository->update($request, $this->get(\'validator\'), $entity);

        if ($result instanceof {{Entity}}) {
            return FOSView::create($result, Response::HTTP_OK);
        }

        return FOSView::create([\'errors\' => $result], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @ApiDoc(
     *      section="{{Entity}}",
     *      description="Delete {{entity}}",
     *      statusCodes={
     *          204="HTTP_NO_CONTENT"
     *      }
     * )
     * @param {{Entity}} $entity
     * @return FOSView
     */
    public function deleteAction({{Entity}} $entity)
    {
        $this->repository->delete($entity);

        return FOSView::create(null, Response::HTTP_NO_CONTENT);
    }
}' .
        '';

        $body = str_replace('{{Entity}}', $entity, $body);
        $body = str_replace('{{entity}}', strtolower($entity), $body);

        return $body;
    }

    public static function getRepository($entity)
    {
        $body = '' .
'<?php

namespace AppBundle\Repository;

use AppBundle\Entity\{{Entity}};
use AppBundle\Handler\OrderHandler;
use AppBundle\Handler\PageHandler;
use AppBundle\Handler\ValidateHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * {{Entity}}Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class {{Entity}}Repository extends \Doctrine\ORM\EntityRepository
{
    /**
     * UPDATE
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param {{Entity}} $item
     * @return {{Entity}}|array|bool
     */
    public function update(Request $request, ValidatorInterface $validator, {{Entity}} $item)
    {
        return $this->create($request, $validator, $item);
    }

    /**
     * CREATE
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param {{Entity}} $item
     * @return {{Entity}}|array|bool
     */
    public function create(Request $request, ValidatorInterface $validator, {{Entity}} $item = null)
    {
        if (!$item) {
            $item = new {{Entity}}();
        }

        $item->setName($request->request->get(\'name\'));

        $validateHandler = new ValidateHandler($item, $validator);

        if ($validateHandler->errors) {
            return $validateHandler->errors;
        }

        $this->getEntityManager()->persist($item);
        $this->getEntityManager()->flush();

        return $item;
    }

    /**
     * CGET
     * @param Request $request
     * @return array
     */
    public function cget(Request $request)
    {
        $pageHandler = new PageHandler($request);
        $orderHandler = new OrderHandler($request);

        $qb = $this
            ->createQueryBuilder(\'item\')
            ->orderBy(
                \'item.\' . $orderHandler->order_by,
                $orderHandler->order_direction
            )
            ->setFirstResult($pageHandler->offset)
            ->setMaxResults($pageHandler->limit);

        $qb = $qb->getQuery();
        $rows = $qb->getResult();

        return [
            \'data\' => $rows,
            \'pagination\' => $pageHandler->getPagination(
                $this->createQueryBuilder(\'item\')
            )
        ];
    }

    /**
     * DELETE
     * @param {{Entity}} $entity
     */
    public function delete({{Entity}} $entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}' .
            '';

        $body = str_replace('{{Entity}}', $entity, $body);
        $body = str_replace('{{entity}}', strtolower($entity), $body);

        return $body;
    }
}
