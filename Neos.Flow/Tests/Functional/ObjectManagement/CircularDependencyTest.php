<?php

namespace Neos\Flow\Tests\Functional\ObjectManagement;

/*
 * This file is part of the Neos.Flow package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\ObjectManagement\Exception\CannotBuildObjectException;
use Neos\Flow\Tests\FunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Functional tests for circular dependencies between singletons
 */
class CircularDependencyTest extends FunctionalTestCase
{
    #[Test]
    public function singletonsDependingOnEachOtherThroughPropertyInjectionCanBeInstantiated(): void
    {
        $singletonA = $this->objectManager->get(Fixtures\CircularSingletonA::class);
        $singletonB = $this->objectManager->get(Fixtures\CircularSingletonB::class);

        self::assertSame($singletonB, $singletonA->singletonB);
        self::assertSame($singletonA, $singletonB->singletonA);
    }

    #[Test]
    public function singletonsDependingOnEachOtherThroughPropertyAndConstructorInjectionCanBeInstantiated(): void
    {
        $singletonC = $this->objectManager->get(Fixtures\CircularSingletonC::class);
        $singletonD = $this->objectManager->get(Fixtures\CircularSingletonD::class);

        self::assertSame($singletonD, $singletonC->singletonD);
        self::assertSame($singletonC, $singletonD->singletonC);
    }

    /**
     * Unlike singletons, prototypes cannot be registered before their dependencies are resolved,
     * because each request must result in a new instance. Two prototypes depending on each other
     * would therefore lead to an infinite recursion – which is what the circular dependency guard
     * of the Object Manager prevents.
     */
    #[Test]
    public function prototypesDependingOnEachOtherAreRejectedWithAnExplanatoryException(): void
    {
        $this->expectException(CannotBuildObjectException::class);
        $this->expectExceptionCode(1168505928);

        $this->objectManager->get(Fixtures\CircularPrototypeE::class);
    }
}
