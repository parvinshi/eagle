<?php
declare(strict_types=1);

namespace Eagle\Route;

use Eagle\Route\Dispatcher\RouteDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RouteHandler implements HttpKernelInterface
{
    private $dispatcher;

    public function __construct(RouteDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * 根据路由调度结果执行路由回调
     * @param Request $request
     * @param int $type
     * @param bool $catch
     * @return mixed|Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        // TODO: Implement handle() method.
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getRequestUri());
        switch ($routeInfo[0]) {
            case RouteDispatcher::NOT_FOUND:
                return new Response('Not found', 404);
                break;

            case RouteDispatcher::METHOD_NOT_ALLOWED:
                return new Response('Method not allowed. Allowed methods: ' . implode(',', $routeInfo[1]), 405);
                break;

            case RouteDispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                return call_user_func_array($handler, $vars);
                break;
        }

        return new Response('Not found', 404);
    }

}