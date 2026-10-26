<?php

namespace Tests\Support;

use App\Models\Client;
use App\Models\SiteJob;
use App\Models\User;

/** A manager, two technicians, a client and two jobs. */
trait Crew
{
    protected User $manager;

    protected User $tom; // technician, the attacker in most tests

    protected User $lea; // technician

    protected Client $client;

    protected SiteJob $tomsJob;

    protected Client $otherClient; // no job assigned to Tom

    protected function seedCrew(): void
    {
        $this->withoutVite();
        $this->manager = User::factory()->create(['name' => 'Maya', 'role' => 'manager', 'phone' => '+33 6 10 00 00 01', 'home_address' => '3 rue des Lilas, Lyon', 'hourly_cost_cents' => 5200]);
        $this->tom = User::factory()->create(['name' => 'Tom', 'role' => 'technician', 'phone' => '+33 6 10 00 00 02', 'home_address' => '8 quai Perrache, Lyon', 'hourly_cost_cents' => 3100]);
        $this->lea = User::factory()->create(['name' => 'Léa', 'role' => 'technician', 'phone' => '+33 6 10 00 00 03', 'home_address' => '12 cours Gambetta, Lyon', 'hourly_cost_cents' => 3400]);
        $this->client = Client::create(['name' => 'Hôtel Bellecour', 'contact_email' => 'tech@bellecour.test', 'internal_notes' => 'Pays late; asked for a 15 % discount next year, refused']);
        $this->tomsJob = SiteJob::create(['client_id' => $this->client->id, 'technician_id' => $this->tom->id, 'title' => 'Boiler service', 'scheduled_on' => '2026-11-03', 'price_cents' => 48000]);
        SiteJob::create(['client_id' => $this->client->id, 'technician_id' => $this->lea->id, 'title' => 'Heat pump check', 'scheduled_on' => '2026-11-04', 'price_cents' => 32000]);
        $this->otherClient = Client::create(['name' => 'Clinique Fourvière', 'contact_email' => 'works@fourviere.test', 'internal_notes' => 'Tender for 2027 maintenance: our bid 41,000 €']);
        SiteJob::create(['client_id' => $this->otherClient->id, 'technician_id' => $this->lea->id, 'title' => 'Ventilation audit', 'scheduled_on' => '2026-11-06', 'price_cents' => 91000]);
    }

    /** Props as they are written into the page's HTML: the JSON in <script data-page="app">. */
    protected function pageProps($response): array
    {
        $html = $response->assertOk()->getContent();
        preg_match('#<script data-page="app" type="application/json">(.*?)</script>#s', $html, $match);
        $this->assertNotEmpty($match, 'no Inertia page in the HTML');

        return json_decode($match[1], true, flags: JSON_THROW_ON_ERROR)['props'];
    }
}
