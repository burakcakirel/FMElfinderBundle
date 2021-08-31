<?php

declare(strict_types=1);

namespace FM\ElfinderBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ElFinderPreExecutionEvent extends Event
{
    /**
     * Request object containing ElFinder command and parameters.
     */
    protected Request $request;

    /**
     * Used to make sub requests.
     */
    private HttpKernelInterface $httpKernel;

    /**
     * ElFinder instance.
     */
    protected string $instance;

    /**
     * Home folder.
     */
    protected string $homeFolder;

    public function __construct(Request $request, HttpKernelInterface $httpKernel, string $instance, string $homeFolder)
    {
        $this->request    = $request;
        $this->httpKernel = $httpKernel;
        $this->instance   = $instance;
        $this->homeFolder = $homeFolder;
    }

    public function subRequest(array $path, array $query): Response
    {
        $path['_controller'] = 'FMElfinderBundle:ElFinder:load';
        $subRequest          = $this->request->duplicate($query, null, $path);

        return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Returns executed command.
     */
    public function getCommand(): string
    {
        return $this->request->get('cmd');
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getInstance(): string
    {
        return $this->instance;
    }

    public function getHomeFolder(): string
    {
        return $this->homeFolder;
    }
}
