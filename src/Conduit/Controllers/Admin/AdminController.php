<?php

namespace Conduit\Controllers\Admin;

use Conduit\Models\Article;
use Conduit\Models\Tag;
use Conduit\Transformers\ArticleTransformer;
use Interop\Container\ContainerInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class AdminController
{

    /** @var \Conduit\Validation\Validator */
    protected $validator;
    /** @var \Illuminate\Database\Capsule\Manager */
    protected $db;
    /** @var \Conduit\Services\Auth\Auth */
    protected $auth;
    /** @var \League\Fractal\Manager */
    protected $fractal;
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
        $this->pdo = $container->get('pdo');
    }

    /**
     * Return List of Admin
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function index(Request $request, Response $response, array $args)
    {
        // TODO Extract the logic of filtering articles to its own class
        return $this->view->render($response, 'dashboard.html.twig', [
            'name' => $args['name']
        ]);
    }

    /**
     * Return List of Admin
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function discounts(Request $request, Response $response, array $args)
    {
        // TODO Extract the logic of filtering articles to its own class
        return $this->view->render($response, 'discounts.html.twig', [
            'name' => $args['name']
        ]);
    }

    /**
     * Return List of Admin
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function adddiscount(Request $request, Response $response, array $args)
    {
        // TODO Extract the logic of filtering articles to its own class
        return $this->view->render($response, 'adddiscount.html.twig', [
            'name' => $args['name']
        ]);
    }

    /**
     * Return List of Admin
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function postdiscount(Request $request, Response $response, array $args)
    {
        if (stripos($_SERVER["CONTENT_TYPE"], "application/json") === 0) {
            $_POST = json_decode(file_get_contents("php://input"), true);
        }
        $missings = array();
        if(isset($_POST['name']))
        {
            $_POST['name'] = trim($_POST['name']);
        }
        else
        {
            $missings[] = 'name';
        }
        if(isset($_POST['start_date']) && $_POST['start_date']!='')
        {
            $_POST['start_date'] = trim($_POST['start_date']);
        }
        else
        {
            $missings[] = 'start_date';
        }
        if(isset($_POST['end_date']) && $_POST['end_date']!='')
        {
            $_POST['end_date'] = trim($_POST['end_date']);
        }
        else
        {
            $missings[] = 'end_date';
        }
        if(isset($_POST['type']) && $_POST['type']!='')
        {
            $_POST['type'] = trim($_POST['type']);
        }
        else
        {
            $missings[] = 'type';
        }
        if(isset($_POST['percent_off']) )
        {
            $_POST['percent_off'] = trim($_POST['percent_off']);
        }
        else
        {
            $missings[] = 'percent_off';
        }
        if(isset($_POST['fixed_off']) )
        {
            $_POST['fixed_off'] = trim($_POST['fixed_off']);
        }
        else
        {
            $missings[] = 'fixed_off';
        }
        if(isset($_POST['description']))
        {
            $_POST['description'] = trim($_POST['description']);
        }
        else
        {
            $missings[] = 'description';
        }

        $result = array(
            "status" => "false",
            "message" => "Failed to add Discount",
            'missings' => $missings
        );

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result));
    }


}