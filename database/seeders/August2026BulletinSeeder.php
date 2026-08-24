<?php

namespace Database\Seeders;

use App\Enums\BulletinStatus;
use App\Models\Bulletin;
use App\Models\Church;
use Illuminate\Database\Seeder;

class August2026BulletinSeeder extends Seeder
{
    public function run(): void
    {
        $church = Church::query()->firstOrCreate(
            ['slug' => 'icvb'],
            [
                'name' => 'Igreja Congregacional Vale da Benção',
                'short_name' => 'ICVB',
                'timezone' => 'America/Fortaleza',
                'pix_key' => '50.208.029/0001-31',
                'logo_path' => null,
                'settings' => [],
            ],
        );

        $bulletin = Bulletin::query()->updateOrCreate(
            [
                'church_id' => $church->id,
                'year' => 2026,
                'month' => 8,
            ],
            [
                'theme' => 'Igreja em Ação',
                'status' => BulletinStatus::Published,
                'published_at' => now('America/Fortaleza'),
            ],
        );

        $bulletin->scheduleItems()->forceDelete();
        $bulletin->specialEvents()->forceDelete();
        $bulletin->serviceRosters()->forceDelete();
        $bulletin->childrenMinistryRosters()->forceDelete();
        $bulletin->ebdClasses()->forceDelete();
        $bulletin->birthdays()->forceDelete();

        foreach ([
            ['DOM 02', '08:00 e 09:30 · Culto e Jardim de Oração', false],
            ['Domingos', '17:00 EBD · 19:00 Culto', false],
            ['DOM 30', '19:00 Santa Ceia', true],
            ['Segunda', 'Cristo nos Lares · Cocó', false],
            ['Terça', 'Oração Campo Missionário · Itaitinga', false],
            ['Quarta', 'Estudo bíblico e oração nas congregações', false],
            ['Quinta', 'Cristo nos Lares · Montese', false],
            ['Sexta', 'Cristo nos Lares · Parreão', false],
        ] as $index => [$day, $description, $highlight]) {
            $bulletin->scheduleItems()->create([
                'day_label' => $day,
                'description' => $description,
                'is_highlight' => $highlight,
                'sort_order' => $index,
            ]);
        }

        foreach ([
            ['2026-08-08', 'SÁB', 'Bazar da Ação Social', 'Sábado, 8 de agosto'],
            ['2026-08-22', 'SÁB', '171 anos de Congregacionalismo no Brasil', 'Sábado, 22 de agosto'],
            ['2026-08-26', 'QUA', 'Culto especial de homens e mulheres', 'Quarta-feira, 26 de agosto'],
            ['2026-08-29', 'SÁB', 'Encontro de mulheres · Itaitinga', 'Sábado, 29 de agosto'],
        ] as $index => [$date, $weekday, $title, $subtitle]) {
            $bulletin->specialEvents()->create([
                'event_date' => $date,
                'weekday_label' => $weekday,
                'title' => $title,
                'subtitle' => $subtitle,
                'sort_order' => $index,
            ]);
        }

        foreach ([
            ['2026-08-02', 'José de Freitas', 'Pb. Mardilson', 'Ev. Jackson', 'Pr. Fernando Fernandes', 'Marcilene / Marcilene'],
            ['2026-08-09', 'Ev. Jackson', 'Diác. Hugo', 'Ruth', 'Miss. Alda', 'Regina / Antônia'],
            ['2026-08-16', 'Dc. Hugo', 'Ev. Jackson', 'Pb. Mardilson', 'Pr. Nay', 'Amanda / Marcilene'],
            ['2026-08-23', 'Pb. Mardilson', 'Beth', 'Pr. Fernando Fernandes', 'Pr. Josival (IEC Guarabira)', 'Ruth / Antônia'],
            ['2026-08-30', 'Nogueira', 'Cícera', 'Diác. Marcos Penha', 'Pr. Fernando Fernandes', 'Beth / Marcilene'],
        ] as $index => [$date, $introducers, $offertory, $leaders, $preachers, $support]) {
            $bulletin->serviceRosters()->create([
                'service_date' => $date,
                'introducers' => $introducers,
                'offertory' => $offertory,
                'leaders' => $leaders,
                'preachers' => $preachers,
                'support' => $support,
                'sort_order' => $index,
            ]);
        }

        foreach ([
            ['2026-08-02', 'Isabel', 'Marli'],
            ['2026-08-09', 'Catarina', 'Cira'],
            ['2026-08-16', 'Marta', 'Beth'],
            ['2026-08-23', 'Cira', 'Clara'],
            ['2026-08-30', 'Paula', 'Fabíola'],
        ] as $index => [$date, $nursery, $primary]) {
            $bulletin->childrenMinistryRosters()->create([
                'service_date' => $date,
                'nursery' => $nursery,
                'primary_class' => $primary,
                'sort_order' => $index,
            ]);
        }

        foreach ([
            ['Berçário', 'Zuíla'],
            ['Primário', 'Amanda'],
            ['Juniores', 'Francisca'],
            ['Rei Davi', 'Emerson'],
            ['Logos', '02 Miss. Alda · 09 Ruth · 16 Diác. Marcos · 23 Miss. Alda · 30 Pr. Nay'],
            ['Bereanos', '02 Pb. Mardilson · 09 Nogueira · 16 Marilene · 23 Pr. Nay · 30 Miss. Alda'],
        ] as $index => [$class, $teachers]) {
            $bulletin->ebdClasses()->create([
                'class_name' => $class,
                'teachers_text' => $teachers,
                'sort_order' => $index,
            ]);
        }

        foreach ([
            [6, 'Cecy'],
            [8, 'Robson'],
            [9, 'Yasmin'],
            [9, 'Alvan'],
            [20, 'Antoniel'],
            [24, 'Valdiana'],
            [26, 'Ester'],
            [27, 'Regis Tavares'],
            [29, 'Marcilene'],
            [29, 'Cristiani Campelo'],
        ] as $index => [$day, $name]) {
            $bulletin->birthdays()->create([
                'day' => $day,
                'name' => $name,
                'sort_order' => $index,
            ]);
        }
    }
}
