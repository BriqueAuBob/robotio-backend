<?php

use App\Models\Stat;
use Illuminate\Database\Seeder;

class StatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stats = [
            [
                'label' => 'line-of-code',
                'value' => 0,
                'icon' => 'assets/img/icons/essential/detailed/Code_2.svg',
                'unit' => 'k',
                'on_homepage' => 1
            ],
            [
                'label' => 'vocal-time',
                'value' => 0,
                'icon' => 'assets/img/icons/essential/detailed/Microphone.svg',
                'unit' => 'time',
                'on_homepage' => 1
            ],
            [
                'label' => 'projects-count',
                'value' => 7,
                'icon' => 'assets/img/icons/essential/detailed/Package.svg',
                'unit' => '',
                'on_homepage' => 1
            ],
            [
                'label' => 'giveaways-count',
                'value' => 0,
                'icon' => 'assets/img/icons/essential/detailed/Gift.svg',
                'unit' => '',
                'on_homepage' => 1
            ]
        ];
        foreach ($stats as $stat) {
            if (!Stat::where('label', '=', $stat['label'])->first()) {
                Stat::create($stat);
            }
        }
    }
}
