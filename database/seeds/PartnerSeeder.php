<?php

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partners = [
            [
                'display_name' => 'mTxServ',
                'logo_url' => 'https://mtxserv.com/build/core/img/logo.png',
                'description' => 'Créer son serveur n\'a jamais été aussi facile. Avec votre hébergeur favori, testez gratuitement notre service de location. Contrôle total sur votre serveur loué chez mTxServ, configurez et installez maps, mods, addons et plugins de votre choix. Sauvegardes automatisées, Anti-DDoS, accès FTP/Web FTP, Live Console, et bien plus!',
                'link' => 'https://mtxserv.com/',
            ],
        ];

        foreach ($partners as $partner) {
            if (!Partner::where('display_name', '=', $partner['display_name'])->first()) {
                Partner::create($partner);
            }
        }
    }
}
