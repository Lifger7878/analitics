<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $regions = [
            'cherkasy' => ['name' => 'Черкаська', 'issued' => 26, 'revoked' => 5, 'suspended' => 2, 'total' => 588, 'weeklyIssued' => [18, 26, 22, 30, 26, 34, 26, 28], 'weeklyRevoked' => [3, 5, 6, 4, 5, 2, 5, 4]],
            'chernihiv' => ['name' => 'Чернігівська', 'issued' => 15, 'revoked' => 2, 'suspended' => 1, 'total' => 378, 'weeklyIssued' => [10, 15, 12, 18, 15, 20, 15, 17], 'weeklyRevoked' => [1, 2, 3, 2, 2, 1, 2, 2]],
            'chernivtsi' => ['name' => 'Чернівецька', 'issued' => 14, 'revoked' => 2, 'suspended' => 1, 'total' => 318, 'weeklyIssued' => [10, 14, 12, 16, 14, 19, 14, 16], 'weeklyRevoked' => [1, 2, 3, 2, 2, 1, 2, 2]],
            'dnipro' => ['name' => 'Дніпропетровська', 'issued' => 82, 'revoked' => 13, 'suspended' => 5, 'total' => 1784, 'weeklyIssued' => [58, 82, 69, 93, 82, 104, 82, 90], 'weeklyRevoked' => [9, 13, 17, 11, 13, 8, 13, 10]],
            'donetsk' => ['name' => 'Донецька', 'issued' => 12, 'revoked' => 3, 'suspended' => 2, 'total' => 274, 'weeklyIssued' => [8, 12, 10, 14, 12, 16, 12, 13], 'weeklyRevoked' => [2, 3, 4, 2, 3, 1, 3, 2]],
            'ivano' => ['name' => 'Івано-Франківська', 'issued' => 28, 'revoked' => 4, 'suspended' => 2, 'total' => 634, 'weeklyIssued' => [20, 28, 23, 33, 28, 36, 28, 31], 'weeklyRevoked' => [3, 4, 6, 3, 4, 2, 4, 3]],
            'kharkiv' => ['name' => 'Харківська', 'issued' => 74, 'revoked' => 12, 'suspended' => 5, 'total' => 1628, 'weeklyIssued' => [53, 74, 62, 84, 74, 94, 74, 81], 'weeklyRevoked' => [9, 12, 16, 10, 12, 7, 12, 9]],
            'kherson' => ['name' => 'Херсонська', 'issued' => 11, 'revoked' => 2, 'suspended' => 1, 'total' => 248, 'weeklyIssued' => [7, 11, 9, 13, 11, 14, 11, 12], 'weeklyRevoked' => [1, 2, 3, 2, 2, 1, 2, 1]],
            'khmeln' => ['name' => 'Хмельницька', 'issued' => 22, 'revoked' => 4, 'suspended' => 2, 'total' => 498, 'weeklyIssued' => [15, 22, 18, 26, 22, 29, 22, 24], 'weeklyRevoked' => [3, 4, 5, 3, 4, 2, 4, 3]],
            'kyiv' => ['name' => 'Київська', 'issued' => 87, 'revoked' => 14, 'suspended' => 6, 'total' => 1842, 'weeklyIssued' => [62, 87, 74, 98, 87, 110, 87, 95], 'weeklyRevoked' => [10, 14, 18, 12, 14, 9, 14, 11]],
            'kyiv_city' => ['name' => 'м. Київ', 'issued' => 142, 'revoked' => 22, 'suspended' => 8, 'total' => 3124, 'weeklyIssued' => [105, 142, 124, 162, 142, 178, 142, 155], 'weeklyRevoked' => [16, 22, 28, 18, 22, 14, 22, 17]],
            'kirovohrad' => ['name' => 'Кіровоградська', 'issued' => 17, 'revoked' => 3, 'suspended' => 1, 'total' => 388, 'weeklyIssued' => [12, 17, 14, 20, 17, 22, 17, 19], 'weeklyRevoked' => [2, 3, 4, 2, 3, 1, 3, 2]],
            'lviv' => ['name' => 'Львівська', 'issued' => 64, 'revoked' => 9, 'suspended' => 4, 'total' => 1387, 'weeklyIssued' => [46, 64, 55, 72, 64, 80, 64, 70], 'weeklyRevoked' => [7, 9, 12, 8, 9, 5, 9, 7]],
            'luhansk' => ['name' => 'Луганська', 'issued' => 8, 'revoked' => 2, 'suspended' => 1, 'total' => 187, 'weeklyIssued' => [5, 8, 6, 9, 8, 11, 8, 9], 'weeklyRevoked' => [1, 2, 3, 2, 2, 1, 2, 1]],
            'mykolaiv' => ['name' => 'Миколаївська', 'issued' => 29, 'revoked' => 5, 'suspended' => 2, 'total' => 658, 'weeklyIssued' => [21, 29, 24, 34, 29, 37, 29, 32], 'weeklyRevoked' => [3, 5, 7, 4, 5, 3, 5, 4]],
            'odesa' => ['name' => 'Одеська', 'issued' => 68, 'revoked' => 11, 'suspended' => 4, 'total' => 1492, 'weeklyIssued' => [48, 68, 57, 77, 68, 86, 68, 75], 'weeklyRevoked' => [8, 11, 14, 9, 11, 7, 11, 9]],
            'poltava' => ['name' => 'Полтавська', 'issued' => 38, 'revoked' => 7, 'suspended' => 3, 'total' => 865, 'weeklyIssued' => [27, 38, 32, 44, 38, 49, 38, 42], 'weeklyRevoked' => [5, 7, 9, 6, 7, 4, 7, 5]],
            'rivne' => ['name' => 'Рівненська', 'issued' => 24, 'revoked' => 4, 'suspended' => 2, 'total' => 534, 'weeklyIssued' => [18, 24, 20, 28, 24, 30, 24, 27], 'weeklyRevoked' => [3, 4, 5, 3, 4, 2, 4, 3]],
            'sumy' => ['name' => 'Сумська', 'issued' => 19, 'revoked' => 4, 'suspended' => 2, 'total' => 445, 'weeklyIssued' => [13, 19, 16, 22, 19, 25, 19, 21], 'weeklyRevoked' => [3, 4, 5, 3, 4, 2, 4, 3]],
            'ternopil' => ['name' => 'Тернопільська', 'issued' => 16, 'revoked' => 3, 'suspended' => 1, 'total' => 362, 'weeklyIssued' => [11, 16, 13, 19, 16, 21, 16, 18], 'weeklyRevoked' => [2, 3, 4, 2, 3, 1, 3, 2]],
            'vinnytsia' => ['name' => 'Вінницька', 'issued' => 31, 'revoked' => 5, 'suspended' => 2, 'total' => 712, 'weeklyIssued' => [22, 31, 26, 36, 31, 40, 31, 34], 'weeklyRevoked' => [4, 5, 7, 4, 5, 3, 5, 4]],
            'volyn' => ['name' => 'Волинська', 'issued' => 18, 'revoked' => 3, 'suspended' => 1, 'total' => 412, 'weeklyIssued' => [12, 18, 15, 22, 18, 24, 18, 21], 'weeklyRevoked' => [2, 3, 4, 2, 3, 1, 3, 2]],
            'zakarpat' => ['name' => 'Закарпатська', 'issued' => 35, 'revoked' => 6, 'suspended' => 3, 'total' => 798, 'weeklyIssued' => [25, 35, 29, 41, 35, 45, 35, 38], 'weeklyRevoked' => [4, 6, 8, 5, 6, 3, 6, 5]],
            'zaporizhzhia' => ['name' => 'Запорізька', 'issued' => 42, 'revoked' => 7, 'suspended' => 3, 'total' => 956, 'weeklyIssued' => [30, 42, 35, 48, 42, 54, 42, 46], 'weeklyRevoked' => [5, 7, 9, 6, 7, 4, 7, 5]],
            'zhytomyr' => ['name' => 'Житомирська', 'issued' => 21, 'revoked' => 5, 'suspended' => 2, 'total' => 487, 'weeklyIssued' => [14, 21, 17, 24, 21, 28, 21, 23], 'weeklyRevoked' => [4, 5, 6, 4, 5, 3, 5, 4]],
            'crimea' => ['name' => 'АР Крим', 'issued' => 10, 'revoked' => 2, 'suspended' => 1, 'total' => 215, 'weeklyIssued' => [7, 10, 8, 12, 10, 13, 10, 11], 'weeklyRevoked' => [1, 2, 2, 1, 2, 1, 2, 2]],
        ];

        $totals = [
            'issued' => array_sum(array_column($regions, 'issued')),
            'revoked' => array_sum(array_column($regions, 'revoked')),
            'active' => array_sum(array_column($regions, 'total')),
        ];

        $mapPath = resource_path('data/map.json');
        $map = is_file($mapPath) ? json_decode(file_get_contents($mapPath), true) : [];
        $leadingRegion = array_reduce($regions, static function (?array $leader, array $region): array {
            return $leader === null || $region['issued'] > $leader['issued'] ? $region : $leader;
        });

        $permitAnalytics = [
            'carriers' => 1842,
            'borderPoints' => 42,
            'ekmtApplications' => 1276,
            'types' => [
                ['id' => 'cargo', 'label' => 'Вантажний', 'value' => 1024, 'color' => 'blue'],
                ['id' => 'bus', 'label' => 'Автобусний', 'value' => 378, 'color' => 'green'],
            ],
            'irregular' => [
                ['label' => 'Міжнародні', 'value' => 74],
                ['label' => 'Внутрішні', 'value' => 44],
            ],
            'purposes' => [
                ['label' => 'Туристично-екскурсійні', 'value' => 198],
                ['label' => 'Весільні та святкові', 'value' => 146],
                ['label' => 'Ритуальні', 'value' => 82],
                ['label' => 'Одноразові перевезення до місць відпочинку', 'value' => 126],
                ['label' => 'Інші перевезення, що не заборонені пунктом 55 Правил надання послуг пасажирського автотранспорту', 'value' => 96],
            ],
        ];

        return view('analytics.index', [
            'regions' => $regions,
            'totals' => $totals,
            'map' => $map ?: [],
            'mapInsights' => [
                'regions' => count($regions),
                'leadingRegion' => $leadingRegion['name'],
                'leadingIssued' => $leadingRegion['issued'],
            ],
            'permitAnalytics' => $permitAnalytics,
            'updatedAt' => now()->format('d.m.Y'),
        ]);
    }
}
