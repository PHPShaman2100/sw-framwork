<?php
declare(strict_types=1);

namespace SW;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Kernel
{
    private TaggedContainerInterface $containerBuilder;
    public function __construct(string|array $paths = [])
    {
        $this->generateCContainer($paths);
    }

    public function action(Request $request, Response $response): Response
    {
        $response->header("Content-Type", "text/plain");
        $response->write("Hello World\n");

        return $response;
    }

    public function getContainerBuilder(): TaggedContainerInterface
    {
        return $this->containerBuilder;
    }

    private function createRouts(): void
    {
        $routes = $this->getContainerBuilder()->getParameter('route');

    }

    private function generateCContainer(string|array $paths = []): void
    {
        if (!defined('BASE_APP_DIR')) {
            define('BASE_APP_DIR', __DIR__ . '/..');
        }
        if (empty($paths)) {
            $paths[] = __DIR__ . '/../config';
            if (is_dir(BASE_APP_DIR . '/config')) {
                $paths[] = BASE_APP_DIR . '/config';
            }
            if (is_dir(__DIR__ . '/config/' . ENVAROMENT)) {
                $paths[] = __DIR__ . '/config/' . ENVAROMENT;
            }
        }

        $this->containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader(
            container: $this->containerBuilder,
            locator: new FileLocator(paths: $paths)
        );
        foreach ($paths as $path) {
            foreach (glob(rtrim($path, '/') . '/*.yaml') as $file) {
                $fileName = pathinfo($file);
                $loader->load($fileName['basename']);
            }
        }

        $this->containerBuilder->compile();
    }
}