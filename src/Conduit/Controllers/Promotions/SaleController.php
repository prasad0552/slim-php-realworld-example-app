<?php

namespace Conduit\Controllers\Promotions;

use Conduit\Models\Promotions\Sale;
use Conduit\Transformers\Promotions\SaleTransformer;
use League\Fractal\Resource\Collection;
use Slim\Http\Request;
use Slim\Http\Response;
use Interop\Container\ContainerInterface;
use Slim\Views\Twig;

class SaleController
{
    /** @var \Conduit\Validation\Validator */
    protected $validator;
    /** @var \Illuminate\Database\Capsule\Manager */
    protected $db;
    /** @var \Conduit\Services\Auth\Auth */
    protected $auth;
    /** @var \League\Fractal\Manager */
    protected $fractal;

    /**
     * @var Twig
     */
    protected $view;

    /**
     * UserController constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     *
     * @internal param $auth
     */
    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->fractal = $container->get('fractal');
        $this->validator = $container->get('validator');
        $this->db = $container->get('db');
        $this->view = $container->get('view');
    }

    /**
     * Return List of Promotions
     *
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     * @param array $args
     *
     * @return \Slim\Http\Response
     */
    public function index(Request $request, Response $response, array $args)
    {
        // TODO Extract the logic of filtering articles to its own class

        $requestUserId = optional($requestUser = $this->auth->requestUser($request))->id;
        $builder = Sale::query()->latest()->limit(20);

        if ($limit = $request->getParam('limit')) {
            $builder->limit($limit);
        }

        if ($offset = $request->getParam('offset')) {
            $builder->offset($offset);
        }

        $promoCount = $builder->count();
        $promotions = $builder->get();

        $data = $this->fractal->createData(new Collection($promotions,
            new SaleTransformer($requestUserId)))->toArray();

        return $response->withJson(['promotions' => $data['data'], 'promotionsCount' => $promoCount]);
    }

    public function lists(Request $request, Response $response, array $args)
    {
        $requestUserId = optional($requestUser = $this->auth->requestUser($request))->id;
        $builder = Sale::query()->latest()->limit(20);

        if ($limit = $request->getParam('limit')) {
            $builder->limit($limit);
        }

        if ($offset = $request->getParam('offset')) {
            $builder->offset($offset);
        }

        $promoCount = $builder->count();
        $promotions = $builder->get();

        return $this->view->render($response, 'promotionsale.html.twig', [
            'discounts' => $promotions
        ]);
    }
}