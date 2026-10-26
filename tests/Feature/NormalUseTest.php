<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Crew;
use Tests\TestCase;

/** What managers and technicians do every day must keep working after the fixes. */
class NormalUseTest extends TestCase
{
    use Crew, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCrew();
    }

    public function test_a_technician_sees_his_jobs_and_the_crew_names(): void
    {
        $this->actingAs($this->tom)->get('/dashboard')->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('jobs', 1)
            ->where('jobs.0.title', 'Boiler service')
            ->where('auth.user.name', 'Tom')
            ->where('auth.user.role', 'technician')
            ->has('team', 3)
            ->where('team.0.name', 'Léa'));
    }

    public function test_a_technician_opens_a_client_page(): void
    {
        $this->actingAs($this->tom)->get('/clients/'.$this->client->id)->assertInertia(fn (Assert $page) => $page
            ->component('Clients/Show')
            ->where('client.contact_email', 'tech@bellecour.test')
            ->has('client.site_jobs', 2));
    }

    public function test_a_manager_sees_internal_notes_and_the_payroll(): void
    {
        $this->actingAs($this->manager)->get('/clients/'.$this->client->id)
            ->assertInertia(fn (Assert $page) => $page->where('client.internal_notes', 'Pays late; asked for a 15 % discount next year, refused'));

        $page = $this->actingAs($this->manager)->get('/dashboard');
        $page->assertInertia(fn (Assert $p) => $p->has('jobs', 2)->missing('payroll'));
        $payroll = $this->actingAs($this->manager)->get('/dashboard', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => (string) $page->viewData('page')['version'],
            'X-Inertia-Partial-Component' => 'Dashboard',
            'X-Inertia-Partial-Data' => 'payroll',
        ]);
        $this->assertSame([3400, 5200, 3100], array_column($payroll->json('props.payroll'), 'hourly_cost_cents'));
    }

    public function test_a_manager_edits_a_job(): void
    {
        $this->actingAs($this->manager)->get('/jobs/'.$this->tomsJob->id.'/edit')->assertInertia(fn (Assert $page) => $page->component('Jobs/Edit'));
        $this->actingAs($this->manager)->put('/jobs/'.$this->tomsJob->id, [
            'title' => 'Boiler service', 'scheduled_on' => '2026-11-05', 'price_cents' => 52000, 'technician_id' => $this->lea->id,
        ])->assertRedirect('/dashboard');

        $this->assertSame(52000, $this->tomsJob->fresh()->price_cents);
        $this->assertSame($this->lea->id, $this->tomsJob->fresh()->technician_id);
    }
}
