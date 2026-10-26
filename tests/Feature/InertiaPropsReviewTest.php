<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Crew;
use Tests\TestCase;

/**
 * One test per finding. Each fails on the app as delivered (tag as-delivered, see
 * scripts/prove-flaws.sh) and passes after the fixes. Assertions that prove a leak carry
 * the message LEAK; the script counts only those, so a crash proves nothing.
 *
 * Everything in Inertia props is in the page source, whatever the Vue component shows.
 */
class InertiaPropsReviewTest extends TestCase
{
    use Crew, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCrew();
    }

    // Finding 1: the shared "team" prop sends every colleague's full record on every page.
    public function test_1_pages_do_not_carry_colleagues_addresses_phones_or_pay(): void
    {
        $props = $this->pageProps($this->actingAs($this->tom)->get('/dashboard'));
        $json = json_encode($props);

        $this->assertStringNotContainsString('12 cours Gambetta', $json, 'LEAK: home address');
        $this->assertStringNotContainsString('+33 6 10 00 00 03', $json, 'LEAK: phone');
        $this->assertStringNotContainsString('hourly_cost_cents', $json, 'LEAK: pay');
    }

    // Finding 2: the Vue page hides internal notes from technicians; the prop still carries them.
    public function test_2_technicians_do_not_receive_internal_client_notes(): void
    {
        $props = $this->pageProps($this->actingAs($this->tom)->get('/clients/'.$this->client->id));

        $this->assertStringNotContainsString('15 %', json_encode($props), 'LEAK: internal notes');
        $this->assertSame('Hôtel Bellecour', $props['client']['name']);
    }

    // Finding 3: the Edit link is hidden for technicians; the routes are not protected.
    public function test_3_a_technician_cannot_change_a_job_s_price(): void
    {
        $this->actingAs($this->tom)->put('/jobs/'.$this->tomsJob->id, [
            'title' => 'Boiler service', 'scheduled_on' => '2026-11-03', 'price_cents' => 1, 'technician_id' => $this->tom->id,
        ]);
        $edit = $this->actingAs($this->tom)->get('/jobs/'.$this->tomsJob->id.'/edit');

        $this->assertSame(48000, $this->tomsJob->fresh()->price_cents, 'LEAK: price changed');
        $this->assertSame(403, $edit->status(), 'LEAK: edit page opened');
    }

    // Finding 4: optional props are skipped on first load, but any client can ask for them.
    public function test_4_a_technician_cannot_load_the_payroll_with_a_partial_reload(): void
    {
        $version = $this->actingAs($this->tom)->get('/dashboard')->viewData('page')['version'];
        $response = $this->actingAs($this->tom)->get('/dashboard', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => (string) $version,
            'X-Inertia-Partial-Component' => 'Dashboard',
            'X-Inertia-Partial-Data' => 'payroll',
        ]);

        $response->assertOk();
        $this->assertEmpty($response->json('props.payroll'), 'LEAK: payroll');
    }
}
