<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsCustomer(): User
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($user);

        return $user;
    }

    public function test_order_page_renders_and_shows_base_amount_for_satuan(): void
    {
        $this->actingAsCustomer();

        $service = Service::create([
            'name' => 'AC Cleaning',
            'description' => 'Pembersihan AC standar',
            'base_price' => 350000,
            'duration_minutes' => 60,
            'category' => 'Cleaning',
            'slug' => 'ac-cleaning',
            'active' => true,
            'unit_type' => 'Satuan',
        ]);

        $response = $this->get(route('customer.order.create', ['slug' => $service->slug]));

        $response->assertStatus(200);
        $this->assertStringContainsString('amount_base', $response->getContent());
    }

    public function test_store_order_calculates_qty_for_satuan(): void
    {
        $user = $this->actingAsCustomer();

        $service = Service::create([
            'name' => 'AC Cleaning',
            'description' => 'Pembersihan AC per unit',
            'base_price' => 350000,
            'duration_minutes' => 60,
            'category' => 'Cleaning',
            'slug' => 'ac-cleaning-qty',
            'active' => true,
            'unit_type' => 'Satuan',
        ]);

        \App\Models\Customer::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'address' => 'Jl. Testing No. 1',
        ]);

        $response = $this->post(route('customer.order.store'), [
            'service_id' => $service->id,
            'date' => now()->toDateString(),
            'time' => '10:00',
            'qty' => 3,
            'address' => 'Jl. Testing No. 1',
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(302);
        $booking = \App\Models\Booking::first();
        $this->assertNotNull($booking);
        $this->assertSame(1050000.00, (float) $booking->total_amount);
    }

    public function test_store_order_calculates_area_for_m2(): void
    {
        $user = $this->actingAsCustomer();

        $service = Service::create([
            'name' => 'Floor Polishing',
            'description' => 'Poles lantai per meter persegi',
            'base_price' => 100000,
            'duration_minutes' => 60,
            'category' => 'Cleaning',
            'slug' => 'floor-polishing',
            'active' => true,
            'unit_type' => 'M2',
        ]);

        \App\Models\Customer::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'address' => 'Jl. Testing No. 2',
        ]);

        $response = $this->post(route('customer.order.store'), [
            'service_id' => $service->id,
            'date' => now()->toDateString(),
            'time' => '11:00',
            'length_m' => 2.5,
            'width_m' => 3,
            'address' => 'Jl. Testing No. 2',
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(302);
        $booking = \App\Models\Booking::first();
        $this->assertNotNull($booking);
        $this->assertSame(750000.00, (float) $booking->total_amount);
    }

    public function test_store_order_calculates_duration_for_durasi(): void
    {
        $user = $this->actingAsCustomer();

        $service = Service::create([
            'name' => 'Home Cleaning (Hourly)',
            'description' => 'Kebersihan rumah per jam',
            'base_price' => 350000,
            'duration_minutes' => 60,
            'category' => 'Cleaning',
            'slug' => 'home-cleaning-hourly',
            'active' => true,
            'unit_type' => 'Durasi',
        ]);

        \App\Models\Customer::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'address' => 'Jl. Testing No. 3',
        ]);

        $response = $this->post(route('customer.order.store'), [
            'service_id' => $service->id,
            'date' => now()->toDateString(),
            'time' => '12:00',
            'duration_minutes' => 90,
            'address' => 'Jl. Testing No. 3',
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(302);
        $booking = \App\Models\Booking::first();
        $this->assertNotNull($booking);
        $this->assertSame(525000.00, (float) $booking->total_amount);
    }

    public function test_store_order_calculates_duration_120_minutes_as_two_hours(): void
    {
        $user = $this->actingAsCustomer();

        $service = Service::create([
            'name' => 'AC Service (Hourly)',
            'description' => 'Per jam, minimal 60 menit',
            'base_price' => 80000,
            'duration_minutes' => 60,
            'category' => 'AC',
            'slug' => 'ac-service-hourly',
            'active' => true,
            'unit_type' => 'Durasi',
        ]);

        \App\Models\Customer::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'address' => 'Jl. Testing No. 4',
        ]);

        $response = $this->post(route('customer.order.store'), [
            'service_id' => $service->id,
            'date' => now()->toDateString(),
            'time' => '13:00',
            'duration_minutes' => 120,
            'address' => 'Jl. Testing No. 4',
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(302);
        $booking = \App\Models\Booking::latest('id')->first();
        $this->assertNotNull($booking);
        $this->assertSame(160000.00, (float) $booking->total_amount);
    }
}

