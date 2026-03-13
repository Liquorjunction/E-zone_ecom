<?php

namespace Database\Seeders;

use App\Models\city;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countryName = [
            'en' => 'Morocco',
            'fr' => 'Maroc',
            'ar' => 'المغرب',
        ];

        $cityNames = [
            'Casablanca',
            'Rabat',
            'Salé',
            'Temara',
            'Skhirat',
            'Mohammedia',
            'Fes',
            'Meknes',
            'Marrakesh',
            'Tangier',
            'Tetouan',
            'Fnideq',
            'Mdiq',
            'Martil',
            'Chefchaouen',
            'Ouezzane',
            'Larache',
            'Ksar El Kebir',
            'Al Hoceima',
            'Nador',
            'Driouch',
            'Oujda',
            'Berkane',
            'Taourirt',
            'Jerada',
            'Guercif',
            'Taza',
            'Sefrou',
            'Ifrane',
            'Azrou',
            'El Hajeb',
            'Kenitra',
            'Sidi Kacem',
            'Sidi Slimane',
            'Sidi Yahya El Gharb',
            'Souk El Arbaa',
            'Mechra Bel Ksiri',
            'Khemisset',
            'Khouribga',
            'Settat',
            'Berrechid',
            'Benslimane',
            'Mediouna',
            'Nouaceur',
            'El Jadida',
            'Sidi Bennour',
            'Safi',
            'Essaouira',
            'Youssoufia',
            'Benguerir',
            'Chichaoua',
            'Kelaa Sraghna',
            'Agadir',
            'Inezgane',
            'Ait Melloul',
            'Biougra',
            'Oulad Teima',
            'Taroudant',
            'Tiznit',
            'Sidi Ifni',
            'Guelmim',
            'Tan-Tan',
            'Assa',
            'Tarfaya',
            'Laayoune',
            'Boujdour',
            'Smara',
            'Dakhla',
            'Aousserd',
            'Beni Mellal',
            'Fquih Ben Salah',
            'Azilal',
            'Demnate',
            'Khenifra',
            'Errachidia',
            'Rissani',
            'Midelt',
            'Tinghir',
            'Ouarzazate',
            'Zagora',
            'Kalaat Mgouna',
            'Bouarfa',
            'Ain Harrouda',
            'Bhalil',
            'Saidia',
            'Tiflet',
            'Arfoud',
            'Guelta Zemmur',
        ];

        foreach ($cityNames as $cityName) {
            $exists = city::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(city_name, '$.en')) = ?", [$cityName])->exists();

            if ($exists) {
                continue;
            }

            city::create([
                'city_name' => [
                    'en' => $cityName,
                    'fr' => $cityName,
                    'ar' => $cityName,
                ],
                'country_name' => $countryName,
                'status' => 'active',
            ]);
        }

        $this->command->info('Morocco cities seeded successfully!');
    }
}
