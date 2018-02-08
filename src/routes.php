<?php

use Conduit\Controllers\Article\ArticleController;
use Conduit\Controllers\Article\CommentController;
use Conduit\Controllers\Article\FavoriteController;
use Conduit\Controllers\Auth\LoginController;
use Conduit\Controllers\Auth\RegisterController;
use Conduit\Controllers\User\ProfileController;
use Conduit\Controllers\User\UserController;
use Conduit\Controllers\Admin\AdminController;
use Conduit\Controllers\Discounts\GetDiscountController;
use Conduit\Middleware\OptionalAuth;
use Conduit\Models\Tag;
use Slim\Http\Request;
use Slim\Http\Response;


// Api Routes
$app->group('/api',
    function () {
        $jwtMiddleware = $this->getContainer()->get('jwt');
        $optionalAuth = $this->getContainer()->get('optionalAuth');
        /** @var \Slim\App $this */

        // Auth Routes
        $this->post('/users', RegisterController::class . ':register')->setName('auth.register');
        $this->post('/users/login', LoginController::class . ':login')->setName('auth.login');

        // User Routes
        $this->get('/user', UserController::class . ':show')->add($jwtMiddleware)->setName('user.show');
        $this->put('/user', UserController::class . ':update')->add($jwtMiddleware)->setName('user.update');

        // Profile Routes
        $this->get('/profiles/{username}', ProfileController::class . ':show')
            ->add($optionalAuth)
            ->setName('profile.show');
        $this->post('/profiles/{username}/follow', ProfileController::class . ':follow')
            ->add($jwtMiddleware)
            ->setName('profile.follow');
        $this->delete('/profiles/{username}/follow', ProfileController::class . ':unfollow')
            ->add($jwtMiddleware)
            ->setName('profile.unfollow');


        // Articles Routes
        $this->get('/articles/feed', ArticleController::class . ':index')->add($optionalAuth)->setName('article.index');
        $this->get('/articles/{slug}', ArticleController::class . ':show')->add($optionalAuth)->setName('article.show');
        $this->put('/articles/{slug}',
            ArticleController::class . ':update')->add($jwtMiddleware)->setName('article.update');
        $this->delete('/articles/{slug}',
            ArticleController::class . ':destroy')->add($jwtMiddleware)->setName('article.destroy');
        $this->post('/articles', ArticleController::class . ':store')->add($jwtMiddleware)->setName('article.store');
        $this->get('/articles', ArticleController::class . ':index')->add($optionalAuth)->setName('article.index');
        $this->post('/getDiscount', GetDiscountController::class . ':index')->add($optionalAuth)->setName('getDiscount.index');

        // Comments
        $this->get('/articles/{slug}/comments',
            CommentController::class . ':index')
            ->add($optionalAuth)
            ->setName('comment.index');
        $this->post('/articles/{slug}/comments',
            CommentController::class . ':store')
            ->add($jwtMiddleware)
            ->setName('comment.store');
        $this->delete('/articles/{slug}/comments/{id}',
            CommentController::class . ':destroy')
            ->add($jwtMiddleware)
            ->setName('comment.destroy');

        // Favorite Article Routes
        $this->post('/articles/{slug}/favorite',
            FavoriteController::class . ':store')
            ->add($jwtMiddleware)
            ->setName('favorite.store');
        $this->delete('/articles/{slug}/favorite',
            FavoriteController::class . ':destroy')
            ->add($jwtMiddleware)
            ->setName('favorite.destroy');

        // Tags Route
        $this->get('/tags', function (Request $request, Response $response) {
            return $response->withJson([
                'tags' => Tag::all('title')->pluck('title'),
            ]);
        });

        //Promotions Route
        $this->get('/promotion/sales',
            \Conduit\Controllers\Promotions\SaleController::class . ':index')
            ->add($optionalAuth)
            ->setName('promotion.sales.index');
    });


// Admin Routes
$app->group('/admin',
    function () {
        $jwtMiddleware = $this->getContainer()->get('jwt');
        $optionalAuth = $this->getContainer()->get('optionalAuth');
        /** @var \Slim\App $this */


        $this->get('/dashboard', AdminController::class . ':index')->add($optionalAuth)->setName('admin.index');
        $this->get('/discounts', AdminController::class . ':discounts')->add($optionalAuth)->setName('admin.discounts');
        $this->get('/discount/add', AdminController::class . ':adddiscount')->add($optionalAuth)->setName('admin.adddiscount');
        $this->post('/discount', AdminController::class . ':postdiscount')->add($optionalAuth)->setName('admin.postdiscount');
        $this->put('/discount', AdminController::class . ':putdiscount')->add($optionalAuth)->setName('admin.putdiscount');
        $this->delete('/discount', AdminController::class . ':deletediscount')->add($optionalAuth)->setName('admin.deletediscount');
        $this->get('/discount/edit/{id}', AdminController::class . ':editdiscount')->add($optionalAuth)->setName('admin.editdiscount');
        $this->get('/login', AdminController::class . ':login')->add($optionalAuth)->setName('login.index');
        $this->post('/login', AdminController::class . ':postlogin')->add($optionalAuth)->setName('loginpost.index');
        $this->post('/logout', AdminController::class . ':logout')->add($optionalAuth)->setName('admin.logout');
        //Promotions Route
        $this->get('/promotion/sales',
            \Conduit\Controllers\Promotions\SaleController::class . ':lists')
            ->add($optionalAuth)
            ->setName('promotion.sales.index');
    });


// Routes

$app->get('/[{name}]',
    function (Request $request, Response $response, array $args) {
        // Sample log message
        $this->logger->info("Slim-Skeleton '/' route");

        // Render index view
        return $this->renderer->render($response, 'index.phtml', $args);
    });
