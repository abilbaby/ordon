<?php

namespace Tests\Unit;

use App\Services\AllocationEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllocationEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_blood_compatibility_rule_handles_universal_donor_and_universal_recipient(): void
    {
        $engine = app(AllocationEngine::class);

        $this->assertTrue($engine->isCompatible('O-', 'AB+'));
        $this->assertFalse($engine->isCompatible('AB+', 'O-'));
    }
}
