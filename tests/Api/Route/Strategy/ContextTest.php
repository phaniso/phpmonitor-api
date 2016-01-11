<?php
namespace Api\Route\Strategy;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    private $controller;
    private $method;
    private $formatClass;
    private $parameters;

    public function setUp()
    {
        $this->controller = 'ServerInfo';
        $this->method = 'getInfo';
        $this->formatClass = null;
        $this->parameters = null;
        $this->strategyFactory = new \Api\Route\Strategy\Factory;
    }

    public function testStrategyNotFoundWithNonExistingController()
    {
        $strategyId = \FastRoute\Dispatcher::NOT_FOUND;
        $controller = 'NonExistentController';
        $method = 'unknown';
        $this->getContextRefTest($strategyId, $method, $controller, 'Api\Route\Strategy\RouteNotFound');
    }
    
    public function testStrategyNotFoundWithExistingControllerAndValidMethod()
    {
        $strategyId = \FastRoute\Dispatcher::NOT_FOUND;
        $this->getContextRefTest($strategyId, $this->method, $this->controller, 'Api\Route\Strategy\RouteNotFound');
    }

    public function testStrategyFound()
    {
        $strategyId = \FastRoute\Dispatcher::FOUND;
        $this->getContextRefTest($strategyId, $this->method, $this->controller, 'Api\Route\Strategy\RouteFound');
    }

    private function getContextRefTest($strategyId, $method, $controller, $strategyClassPath)
    {
        $context = new Context($strategyId, $this->strategyFactory, $controller, $method, $this->formatClass, $this->parameters);
        $contextRef = $this->setPropertyAccessible($context, 'strategy');
        $this->assertTrue(get_class($contextRef->getValue($context)) == $strategyClassPath);
    }

    public function setPropertyAccessible(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty;
    }
}
