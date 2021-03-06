<?php
namespace Api\Controller;

use Api\Module\Facade as ModuleFacade;
use Api\Format\Factory as FormatFactory;
use Api\Format\Processor as FormatProcessor;
use Api\Module\Factory as ModuleFactory;
use Api\Module\Composite as ModuleComposite;
use Api\Config\ConfigProxy as Config;
use League\Container\Container as Container;

class ServerInfoTest extends \PHPUnit_Framework_TestCase
{
   
    public function testIfWeCanPassWhiteListWhenOurIpIsNotOnList()
    {
        $ip = 'localhost';
        $controller = new ServerInfo();
        $config = new Config('Config.json');
        $config->whitelistEnabled = true;
        $config->whitelist = ['99.99.99.99'];
        $ret = $this->invokeMethod($controller, 'canUserPassWhiteList', array($ip, $config));
        $this->assertFalse($ret);
    }

    public function testIfWeCanPassWhiteListWhenOurIpIsOnList()
    {
        $ip = 'localhost';
        $controller = new ServerInfo();
        $config = new Config('Config.json');
        $config->whitelistEnabled = true;
        $config->whitelist = [$ip];
        $ret = $this->invokeMethod($controller, 'canUserPassWhiteList', array($ip, $config));
        $this->assertTrue($ret);
    }

    public function testAddingModulesAndGettingData()
    {
        $logger = $this->getMockBuilder('Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();
        $moduleFacade = new ModuleFacade(new ModuleFactory, new ModuleComposite, $logger);
        $controller = new ServerInfo();
        $config = new Config('Config.json');
        $config->hostToPing = $config->defaultHostToPing;
        $this->invokeMethod($controller, 'addModules', array($moduleFacade, $config));
        $data = $moduleFacade->returnModulesData();
        $this->assertArrayHasKey('hostname', $data);
    }

    public function invokeMethod(&$object, $methodName, $args = array())
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    public function setPropertyAccessible(&$object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty;
    }
}
