<?php declare(strict_types=1);
namespace Phan\Library;

use Closure;

/**
 * Implements Resource Acquisition Is Initialization.
 * An defined but unused variable in a function/method scope can be used to create this,
 * and the passed in finalizer closure will be called when that function/method returns.
 *
 * Note: This assumes that the garbage collector eagerly calls __destruct.
 * This may not be the case in alternate PHP implementations.
 *
 * @see https://en.wikipedia.org/wiki/Resource_acquisition_is_initialization
 */
class RAII
{
    /** @var null|Closure():void */
    private $finalizer;

    /**
     * @param Closure():void $finalizer - Should not throw an exception. It may be called in __destruct()
     */
    public function __construct(Closure $finalizer)
    {
        $this->finalizer = $finalizer;
    }

    /**
     * Calls the finalizer, unless it has already been called.
     * @return void
     */
    public function callFinalizerOnce()
    {
        $finalizer = $this->finalizer;
        if ($finalizer) {
            $finalizer();
            $this->finalizer = null;
        }
    }

    public function __destruct()
    {
        $this->callFinalizerOnce();
    }
}
