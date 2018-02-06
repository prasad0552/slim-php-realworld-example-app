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
        $sth = $this->pdo->prepare("SELECT * FROM promo");
        $sth->execute();
        $result = $sth->fetchAll();
        return $this->view->render($response, 'discounts.html.twig', [
            'discounts' => $result
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
    public function editdiscount(Request $request, Response $response, array $args)
    {
        // TODO Extract the logic of filtering articles to its own class
        return $this->view->render($response, 'editdiscount.html.twig', [
            'id' => $args['id']
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
        $gstatus = 'false';
        $gmessage = 'Failed to add Discount';
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
        $invalid = array();
        if(empty($missings))
        {
            list($mm,$dd,$yyyy) = explode('/',$_POST['start_date']);
            if (!checkdate($mm,$dd,$yyyy)) {
                $invalid[] = 'start_date';
            }
            list($mm,$dd,$yyyy) = explode('/',$_POST['end_date']);
            if (!checkdate($mm,$dd,$yyyy)) {
                $invalid[] = 'end_date';
            }
            if($_POST['fixed_off']=='' && $_POST['percent_off']=='')
            {
                $invalid[] = 'fixed_off';
                $invalid[] = 'percent_off';
            }
            if(empty($invalid))
            {

                $statement = $this->pdo->prepare("INSERT INTO promo(name, start_date, end_date, type, fixed_off, percent_off, description)
                                    VALUES(:name, :start_date, :end_date, :type, :fixed_off, :percent_off, :description)");
                $statement->execute(array(
                    "name" => $_POST['name'],
                    "start_date" => date('Y-m-d H:i:s', strtotime($_POST['start_date'])),
                    "end_date" => date('Y-m-d H:i:s', strtotime($_POST['end_date'])),
                    "type" => $_POST['type'],
                    "fixed_off" => $_POST['fixed_off'],
                    "percent_off" => $_POST['percent_off'],
                    "description" => $_POST['description']
                ));
                $gstatus = 'true';
                $gmessage = 'Discount added successfully';
            }

        }

        $result = array(
            "status" => $gstatus,
            "message" => $gmessage,
            'missings' => $missings,
            'invalid' => $invalid
        );

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result));
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
    public function putdiscount(Request $request, Response $response, array $args)
    {
        $gstatus = 'false';
        $gmessage = 'Failed to add Discount';
        $result = array(
            "status" => $gstatus,
            "message" => $gmessage
        );

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result));
    }


}