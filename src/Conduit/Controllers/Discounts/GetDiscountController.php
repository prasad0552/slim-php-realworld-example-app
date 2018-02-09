<?php

namespace Conduit\Controllers\Discounts;

use Conduit\Models\Promo;
use Conduit\Models\Tag;
use Conduit\Transformers\ArticleTransformer;
use Interop\Container\ContainerInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class GetDiscountController
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
     * GetDiscountController constructor.
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
        $this->pdo = $container->get('pdo');

    }

    /**
     * Return List of Articles
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

        $requestUserId = optional($requestUser = $this->auth->requestUser($request))->id;
        $builder = Promo::query()->latest()->with(['tags', 'user'])->limit(20);
        $parsedBody = $request->getParsedBody();
        $ids = implode(',',array_column($parsedBody,'product_id'));
        $sth = $this->pdo->prepare("SELECT * FROM items_prices where item_id in (" .trim($ids).")");
        $sth->execute();
        $res = $sth->fetchAll();

        $promoSth = $this->pdo->prepare("SELECT * FROM promo where end_date >= '".date("Y-m-d")."'");
        $promoSth->execute();
        $promoRes = $promoSth->fetchAll();
        if($promoRes[0]['percent_off']>0){

        }
        if($promoRes[0]['fixed_off']>0){

        }
        print_r($promoRes);exit;
        if(!empty($res))
        {

        }



        return $response->withJson(['articles' => $request, 'articlesCount' => $parsedBody]);
    }


    /**
     * Return List of Promotions with status
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function promos_status(Request $request, Response $response, array $args)
    {
        // TODO Extract the logic of filtering articles to its own class

        $sth = $this->pdo->prepare("SELECT name,type,description,enabled FROM promo");
        $sth->execute();
        $res = $sth->fetchAll();





        return $response->withJson(['promos' => $res]);
    }

    /**
     * Return a single Article to get article endpoint
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function show(Request $request, Response $response, array $args)
    {
        $requestUserId = optional($this->auth->requestUser($request))->id;

        $article = Article::query()->where('slug', $args['slug'])->firstOrFail();

        $data = $this->fractal->createData(new Item($article, new ArticleTransformer($requestUserId)))->toArray();

        return $response->withJson(['article' => $data]);
    }

    /**
     * Create and store a new Article
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     *
     * @return Response
     */
    public function store(Request $request, Response $response)
    {
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson([], 401);
        }

        $this->validator->validateArray($data = $request->getParam('article'),
            [
                'title'       => v::notEmpty(),
                'description' => v::notEmpty(),
                'body'        => v::notEmpty(),
            ]);

        if ($this->validator->failed()) {
            return $response->withJson(['errors' => $this->validator->getErrors()], 422);
        }

        $article = new Article($request->getParam('article'));
        $article->slug = str_slug($article->title);
        $article->user_id = $requestUser->id;
        $article->save();

        $tagsId = [];
        if (isset($data['tagList'])) {
            foreach ($data['tagList'] as $tag) {
                $tagsId[] = Tag::updateOrCreate(['title' => $tag], ['title' => $tag])->id;
            }
            $article->tags()->sync($tagsId);
        }

        $data = $this->fractal->createData(new Item($article, new ArticleTransformer()))->toArray();

        return $response->withJson(['article' => $data]);

    }

    /**
     * Update Article Endpoint
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function update(Request $request, Response $response, array $args)
    {
        $article = Article::query()->where('slug', $args['slug'])->firstOrFail();
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson([], 401);
        }

        if ($requestUser->id != $article->user_id) {
            return $response->withJson(['message' => 'Forbidden'], 403);
        }

        $params = $request->getParam('article', []);

        $article->update([
            'title'       => isset($params['title']) ? $params['title'] : $article->title,
            'description' => isset($params['description']) ? $params['description'] : $article->description,
            'body'        => isset($params['body']) ? $params['body'] : $article->body,
        ]);

        if (isset($params['title'])) {
            $article->slug = str_slug($params['title']);
        }

        $data = $this->fractal->createData(new Item($article, new ArticleTransformer()))->toArray();

        return $response->withJson(['article' => $data]);
    }

    /**
     * Delete Article Endpoint
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function destroy(Request $request, Response $response, array $args)
    {
        $article = Article::query()->where('slug', $args['slug'])->firstOrFail();
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson([], 401);
        }

        if ($requestUser->id != $article->user_id) {
            return $response->withJson(['message' => 'Forbidden'], 403);
        }

        $article->delete();

        return $response->withJson([], 200);
    }

}