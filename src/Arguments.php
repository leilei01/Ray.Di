<?php
/**
 * This file is part of the Ray package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Di;

use Doctrine\Common\Annotations\AnnotationReader;
use Ray\Di\Exception\Unbound;

final class Arguments
{
    /**
     * @var Argument[]
     */
    private $arguments = [];

    public function __construct(\ReflectionMethod $method, Name $name)
    {
        $parameters = $method->getParameters();
        foreach ($parameters as $parameter) {
            $this->arguments[] = new Argument($parameter, $name($parameter));
        }
    }

    /**
     * Return arguments
     *
     * @param Container $container
     *
     * @return Argument[]
     * @throws Exception\Unbound
     */
    public function get(Container $container)
    {
        $parameters = $this->arguments;
        foreach ($parameters as &$parameter) {
            $parameter = $this->getParameter($container, $parameter);
        }

        return $parameters;
    }

    /**
     * @param Container $container
     * @param Argument  $argument
     *
     * @return mixed
     * @throws Unbound
     */
    private function getParameter(Container $container, Argument $argument)
    {
        $this->bindInjectionPoint($container, $argument);
        try {
            return $container->getDependency((string) $argument);
        } catch (Unbound $e) {
            return $this->getDefaultValue($argument);
        }
    }

    private function bindInjectionPoint(Container $container, Argument $argument)
    {
        $isSelf = (string) $argument === 'Ray\Di\InjectionPointInterface-*';
        if ($isSelf) {
            return;
        }
        (new Bind($container, 'Ray\Di\InjectionPointInterface'))->toInstance(new InjectionPoint($argument->get(), new AnnotationReader));
    }

    /**
     * @param Argument $argument
     *
     * @return mixed
     * @internal param Unbound $e
     */
    private function getDefaultValue(Argument $argument)
    {
        if ($argument->isDefaultAvailable()) {
            return $argument->getDefaultValue();
        }
        $message = sprintf("%s (%s)", $argument->getDebugInfo(), $argument);
        throw new Unbound($message);
    }
}