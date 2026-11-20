<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_default_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_the_localized_home_page_returns_a_successful_response(): void
    {
        $this->withoutVite()->get('/en')->assertStatus(200);
    }
}
