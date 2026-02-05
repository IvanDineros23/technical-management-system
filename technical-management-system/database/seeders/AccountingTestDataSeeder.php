<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{JobOrder, Certificate, Payment, User};
use Illuminate\Support\Facades\DB;

class AccountingTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Accounting Test Data...');

        DB::beginTransaction();
        try {
            // Get some completed job orders
            $jobOrders = JobOrder::whereIn('status', ['completed', 'approved'])->take(5)->get();

            if ($jobOrders->isEmpty()) {
                $this->command->warn('No completed job orders found. Creating sample job orders...');
                
                // Get or create a customer
                $customer = \App\Models\Customer::first();
                if (!$customer) {
                    $customer = \App\Models\Customer::create([
                        'name' => 'ABC Manufacturing Corp.',
                        'email' => 'contact@abcmanufacturing.com',
                        'phone' => '02-1234-5678',
                        'address' => '123 Industrial Ave, Makati City',
                        'city' => 'Makati',
                        'country' => 'Philippines',
                        'contact_person' => 'Juan Dela Cruz',
                        'tax_id' => '123-456-789-000',
                    ]);
                }

                // Create sample job orders
                for ($i = 1; $i <= 5; $i++) {
                    $jobOrders->push(JobOrder::create([
                        'job_order_number' => 'JO-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                        'customer_id' => $customer->id,
                        'description' => 'Calibration Service #' . $i,
                        'status' => $i <= 3 ? 'completed' : 'approved',
                        'priority' => 'normal',
                        'request_date' => now()->subDays(rand(5, 15)),
                        'required_date' => now()->addDays(rand(1, 7)),
                    ]));
                }
            }

            // Get a signatory user
            $signatory = User::whereHas('role', function($q) {
                $q->where('name', 'Signatory');
            })->first();

            if (!$signatory) {
                $this->command->warn('No signatory found in system.');
            }

            // Create certificates for the first 3 job orders (ready for release)
            foreach ($jobOrders->take(3) as $index => $jobOrder) {
                $certNumber = 'CERT-' . now()->format('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                
                $cert = Certificate::updateOrCreate(
                    ['job_order_id' => $jobOrder->id],
                    [
                        'certificate_number' => $certNumber,
                        'status' => 'approved',
                        'issue_date' => now()->subDays(rand(1, 5)),
                        'valid_until' => now()->addYear(),
                        'generated_at' => now()->subDays(rand(1, 5)),
                        'issued_by' => $signatory ? $signatory->id : null,
                        'signed_by' => $signatory ? $signatory->id : null,
                        'signed_at' => now()->subDays(rand(1, 5)),
                        'qr_code' => 'QR-' . uniqid(),
                    ]
                );

                $this->command->info("Created certificate: {$cert->certificate_number} for {$jobOrder->job_order_number}");
            }

            // Create payment records for first 2 job orders (verified)
            foreach ($jobOrders->take(2) as $index => $jobOrder) {
                Payment::updateOrCreate(
                    ['job_order_id' => $jobOrder->id],
                    [
                        'payment_code' => 'PAY-' . now()->format('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                        'amount_paid' => rand(5000, 25000),
                        'paid_at' => now()->subDays(rand(1, 10)),
                        'status' => 'verified',
                        'verified_by' => User::whereHas('role', function($q) {
                            $q->where('name', 'Accounting');
                        })->first()?->id,
                        'verified_at' => now()->subDays(rand(1, 3)),
                    ]
                );

                $this->command->info("Created payment for: {$jobOrder->job_order_number}");
            }

            // Create unverified payment for 3rd job order
            if ($jobOrders->count() >= 3) {
                $jobOrder = $jobOrders->get(2);
                Payment::updateOrCreate(
                    ['job_order_id' => $jobOrder->id],
                    [
                        'payment_code' => 'PAY-' . now()->format('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                        'amount_paid' => rand(5000, 25000),
                        'paid_at' => now()->subDays(rand(1, 3)),
                        'status' => 'paid',
                    ]
                );

                $this->command->info("Created unverified payment for: {$jobOrder->job_order_number}");
            }

            // Create released certificates for historical data
            if ($jobOrders->count() >= 4) {
                $jobOrder = $jobOrders->get(3);
                $certNumber = 'CERT-' . now()->format('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                
                $cert = Certificate::updateOrCreate(
                    ['job_order_id' => $jobOrder->id],
                    [
                        'certificate_number' => $certNumber,
                        'status' => 'released',
                        'issue_date' => now()->subDays(3),
                        'valid_until' => now()->addYear(),
                        'generated_at' => now()->subDays(3),
                        'issued_by' => $signatory ? $signatory->id : null,
                        'signed_by' => $signatory ? $signatory->id : null,
                        'signed_at' => now()->subDays(2),
                        'released_at' => now(),
                        'qr_code' => 'QR-' . uniqid(),
                    ]
                );

                Payment::updateOrCreate(
                    ['job_order_id' => $jobOrder->id],
                    [
                        'payment_code' => 'PAY-' . now()->format('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                        'amount_paid' => rand(5000, 25000),
                        'paid_at' => now()->subDays(2),
                        'status' => 'verified',
                        'verified_by' => User::whereHas('role', function($q) {
                            $q->where('name', 'Accounting');
                        })->first()?->id,
                        'verified_at' => now()->subDay(),
                    ]
                );

                $this->command->info("Created released certificate: {$cert->certificate_number}");
            }

            DB::commit();
            $this->command->info('✅ Accounting test data seeded successfully!');

            // Show summary
            $this->command->info("\n📊 Summary:");
            $this->command->info("Certificates pending release: " . Certificate::where('status', 'approved')->whereNotNull('generated_at')->whereNull('released_at')->count());
            $this->command->info("Unpaid jobs: " . JobOrder::whereDoesntHave('payment', function($q) {
                $q->where('status', 'verified');
            })->whereIn('status', ['completed', 'in_progress'])->count());
            $this->command->info("Released today: " . Certificate::whereDate('released_at', now())->count());
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding accounting data: ' . $e->getMessage());
            throw $e;
        }
    }
}
