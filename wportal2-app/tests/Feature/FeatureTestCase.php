<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class FeatureTestCase extends TestCase
{
    /**
     * Base for feature/integration tests that boot the Laravel app and refresh DB.
     * Unit tests should extend PHPUnit\Framework\TestCase directly.
     */
    use RefreshDatabase;
}
