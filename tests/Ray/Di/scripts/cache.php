<?php

namespace Ray\Di;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Ray\Aop\Bind;
use Ray\Aop\Compiler;
use Doctrine\Common\Annotations\AnnotationReader;

require_once dirname(dirname(dirname(__DIR__))) . '/bootstrap.php';

$injector = function () {
    $container = new Container(new Forge(new Config(new Annotation(new Definition, new AnnotationReader))));
    return new Injector(
        $container,
        new Modules\AopModule,
        new Bind,
        new Compiler(__DIR__ . '/aop_files')
    );
};
$postInject = function($instance) {};
$injector = new CacheInjector($injector, $postInject, 'test', new FilesystemCache(__DIR__ . '/object_files'));
$billing = $injector->getInstance('Ray\Di\Aop\CacheBilling');

return serialize($billing);