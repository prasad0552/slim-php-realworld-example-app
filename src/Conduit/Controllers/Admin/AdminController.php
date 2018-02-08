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
    public function login(Request $request, Response $response, array $args)
    {

        return $this->view->render($response, 'login.html.twig', [
            'name' => $args['name']
        ]);
    }

    /**
     * Triggered after submitting the login form
     * Validate the user and redirect to Dashboard
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function postlogin(Request $request, Response $response, array $args)
    {
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        $result = [];
        $isValidUser = true;
        // Validating inputs
        if(!$username) {
            $result['status'] = false;
            $result['message'] = 'Please enter username';
        } elseif(!$password) {
            $result['status'] = false;
            $result['message'] = 'Please enter password';
        } else {
            // TODO : validate user
            $result['status'] = true;
        }
        $result['isValid'] = $isValidUser;

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
        if(isset($_POST['promo']) && $_POST['promo']!='')
        {
            $_POST['promo'] = trim($_POST['promo']);
        }
        else
        {
            $missings[] = 'promo';
        }
        if(isset($_POST['free_shipping']) && $_POST['free_shipping']!='')
        {
            $_POST['free_shipping'] = trim($_POST['free_shipping']);
        }
        else
        {
            $missings[] = 'free_shipping';
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

                $statement = $this->pdo->prepare("INSERT INTO promo(name, start_date, end_date, type, fixed_off, percent_off, description, promo, free_shipping)
                                    VALUES(:name, :start_date, :end_date, :type, :fixed_off, :percent_off, :description, :promo, :free_shipping)");
                $statement->execute(array(
                    "name" => $_POST['name'],
                    "start_date" => date('Y-m-d H:i:s', strtotime($_POST['start_date'])),
                    "end_date" => date('Y-m-d H:i:s', strtotime($_POST['end_date'])),
                    "type" => $_POST['type'],
                    "fixed_off" => $_POST['fixed_off'],
                    "percent_off" => $_POST['percent_off'],
                    "description" => $_POST['description'],
                    "promo" => $_POST['promo'],
                    "free_shipping" => $_POST['free_shipping']
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

        $_PUT = $request->getParams();

        $gstatus = 'false';
        $gmessage = 'Failed to update Discount';
        $resp  = "";
        if(isset($_PUT['id']) && trim($_PUT['id'])!='') {
            if (isset($_PUT['onlyenable']) && $_PUT['onlyenable'] == 'yes') {
                $sth = $this->pdo->prepare("SELECT * FROM promo where id= '" .trim($_PUT['id'])."' ");
                $sth->execute();
                $res = $sth->fetch();
                if(!empty($res))
                {
                    $newenb = 0;
                    if($res['enabled']==0)
                    {
                        $newenb = 1;
                    }
                    $resp = $newenb;
                    $sql = "UPDATE promo SET enabled=$newenb where id= '" .trim($_PUT['id'])."' ";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute();
                    $gmessage = $stmt->rowCount() . " records UPDATED successfully";
                    $gstatus = 'true';
                }
            }
        }
        $result = array(
            "status" => $gstatus,
            "message" => $gmessage,
            "resp" => $resp
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
    public function deletediscount(Request $request, Response $response, array $args)
    {

        $_DELETE = $request->getParams();

        $gstatus = 'false';
        $gmessage = 'Failed to delete Discount';
        $resp  = "";
        if(isset($_DELETE['id']) && trim($_DELETE['id'])!='') {
            $sql = "DELETE from promo  where id= '" .trim($_DELETE['id'])."' ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $gstatus = 'true';
            $gmessage = 'Promotion deleted Successfully';
        }
        $result = array(
            "status" => $gstatus,
            "message" => $gmessage,
            "resp" => $_DELETE
        );

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result));
    }


}