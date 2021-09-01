<?php

declare(strict_types=1);

namespace FM\ElfinderBundle\Loader;

use elFinderVolumeDriver;
use FM\ElfinderBundle\Connector\ElFinderConnector;
use FM\ElfinderBundle\Bridge\ElFinderBridge;
use FM\ElfinderBundle\Configuration\ElFinderConfigurationProviderInterface;
use FM\ElfinderBundle\Exception\ImproperConfigurationClassException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ElFinderLoader
{
    protected string $instance;
    protected ElFinderConfigurationProviderInterface $configurator;
    protected array $config;
    protected ElFinderBridge $bridge;
    protected SessionInterface $session;

    public function __construct(ElFinderConfigurationProviderInterface $configurator)
    {
        $this->configurator = $configurator;
    }

    public function configure(): array
    {
        $configurator = $this->configurator;
        if (!($configurator instanceof ElFinderConfigurationProviderInterface)) {
            throw new ImproperConfigurationClassException();
        }

        return $configurator->getConfiguration($this->instance);
    }

    public function initBridge(string $instance, array $efParameters): void
    {
        $this->setInstance($instance);

        $arrayInstance = $efParameters['instances'][$instance];
        $whereIsMulti  = $arrayInstance['where_is_multi'];
        $multiHome     = $arrayInstance['multi_home_folder'];
        $separator     = $arrayInstance['folder_separator'];

        $this->config = $this->configure();

        if (count($whereIsMulti) > 0) {
            foreach ($whereIsMulti as $key => $value) {
                if ($multiHome) {
                    $this->config[$key][$value]['path'] = str_replace($separator, '/', $this->config[$key][$value]['path']);
                    $this->config[$key][$value]['URL']  = str_replace($separator, '/', $this->config[$key][$value]['URL']);
                }
            }
        }

        $this->bridge = new ElFinderBridge($this->config);
        if ($this->session) {
            $this->bridge->setSession($this->session);
        }
    }

    /**
     * Starts ElFinder.
     *
     * @var Request
     *
     * @return void|array
     */
    public function load(Request $request)
    {
        $connector = new ElFinderConnector($this->bridge);

        if ($this->config['corsSupport']) {
            return $connector->execute($request->query->all());
        } else {
            $connector->run($request->query->all());
        }
    }

    public function setInstance(string $instance)
    {
        $this->instance = $instance;
    }

    public function setConfigurator(ElFinderConfigurationProviderInterface $configurator)
    {
        $this->configurator = $configurator;
    }

    /**
     * @return mixed
     */
    public function encode(string $path)
    {
        $aPathEncoded = [];

        $volumes = $this->bridge->getVolumes();

        foreach ($volumes as $hashId => $volume) {
            $aPathEncoded[$hashId] = $volume->getHash($path);
        }

        if (1 == count($aPathEncoded)) {
            return array_values($aPathEncoded)[0];
        } elseif (count($aPathEncoded) > 1) {
            return $aPathEncoded;
        } else {
            return false;
        }
    }

    public function decode(string $hash)
    {
        $volume = $this->bridge->getVolume($hash);

        /* @var $volume elFinderVolumeDriver */
        return (!empty($volume)) ? $volume->getPath($hash) : false;
    }

    public function setSession($session)
    {
        $this->session = $session;
    }
}
