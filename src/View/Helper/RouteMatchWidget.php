<?php
namespace PlaygroundDesign\View\Helper;

use Laminas\Http\Request;
use Laminas\Router\RouteStackInterface;
use Laminas\View\Helper\AbstractHelper;

/**
 * Helper to get the RouteMatch
 */
class RouteMatchWidget extends AbstractHelper
{
    /**
     * RouteStackInterface instance.
     *
     * @var RouteStackInterface
     */
    protected $router;

    /**
     * @var Request
     */
    protected $request;

    /**
     * RouteMatch constructor.
     * @param RouteStackInterface $router
     * @param Request $request
     */
    public function __construct(RouteStackInterface $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    /**
     * @return \Laminas\Router\RouteMatch
     */
    public function __invoke()
    {
        return $this->router->match($this->request);
    }
}