<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_show_home_with_all_domains()
    {
        $this->get('/')->assertViewIs('home');
        $this->get('/')->assertViewHas('domains');
    }

    /** @test */
    public function it_should_show_home_with_all_active_domains()
    {
        $this->get('/active')->assertViewIs('home');
        $this->get('/')->assertViewHas('domains');
    }

    /** @test */
    public function it_should_show_home_with_all_expiring_domains()
    {
        $this->get('/expiring')->assertViewIs('home');
        $this->get('/')->assertViewHas('domains');
    }

    /** @test */
    public function it_should_show_home_with_all_incomplete_domains()
    {
        $this->get('/incomplete')->assertViewIs('home');
        $this->get('/')->assertViewHas('domains');
    }

    /** @test */
    public function it_should_show_home_with_the_result_of_the_text_search()
    {
        $this->get('/search')->assertViewIs('home');
        $this->get('/')->assertViewHas('domains');
    }
}
