<?php

declare(strict_types=1);

namespace Ray\Di;

/**
 * Interface for instance provider. (lazy-loading)
 */
interface ProviderInterface
{
    /**
     * Get an instance
     */
    public function get();
}
