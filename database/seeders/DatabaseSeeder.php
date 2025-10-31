<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->withoutTwoFactor()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $Artist = new \App\Models\Artist();
        $Artist->name = 'Leonardo da Vinci';
        $Artist->bio = "Léonard de Vinci, né le 15 avril 1452 à Vinci en Italie et mort le 2 mai 1519 à Amboise en France, est un peintre, sculpteur, architecte, ingénieur, scientifique et inventeur italien de la Renaissance. Considéré comme l'un des plus grands génies de l'histoire, Léonard de Vinci est célèbre pour ses œuvres artistiques emblématiques telles que La Joconde et La Cène, ainsi que pour ses contributions dans divers domaines scientifiques et techniques.";
        $Artist->birt_date = '1452-04-15';
        $Artist->death_date = '1519-05-02';
        $Artist->save();

        $Work = new \App\Models\Work();
        $Work->title = 'La joconde';
        $Work->description = "La Joconde ou Mona Lisa est un portrait peint par Léonard de Vinci entre 1503 et 1506, peut-être jusqu'en 1517. Il représente Lisa Gherardini, l'épouse de Francesco del Giocondo, un marchand florentin. Célèbre pour son sourire énigmatique, ce tableau est considéré comme l'une des œuvres d'art les plus célèbres et les plus reconnaissables au monde.";
        $Work->year_created = '1503';
        $Work->artist_id = $Artist->id;
        $Work->save();

        $Work2 = new \App\Models\Work();
        $Work2->title = 'La cène';
        $Work2->description = "La Cène (en italien : L'Ultima Cena, soit « Le Dernier Repas ») de Léonard de Vinci est une peinture murale à la détrempe de 460 × 880 cm, réalisée de 1495 à 1498 pour le réfectoire du couvent dominicain de Santa Maria delle Grazie à Milan.";
        $Work2->year_created = '1498';
        $Work2->artist_id = $Artist->id;
        $Work2->save();

        $Artist2 = new \App\Models\Artist();
        $Artist2->name = 'Edvard Munch';
        $Artist2->bio = "Edvard Munch, prononcé [muŋk], né le 12 décembre 1863 à Ådalsbruk et mort le 23 janvier 1944 à Oslo, est un peintre et graveur expressionniste norvégien. Edvard Munch peut, a posteriori, être considéré après l'exposition berlinoise de 1892, comme le pionnier de l'expressionnisme dans la peinture moderne.";
        $Artist2->birt_date = '1863-12-12';
        $Artist2->death_date = '1944-01-23';
        $Artist2->save();

        $Work3 = new \App\Models\Work();
        $Work3->title = 'Le cri';
        $Work3->description = "Le Cri (en norvégien : Skrik) est une œuvre expressionniste de l'artiste norvégien Edvard Munch dont il existe cinq versions (deux peintures, un pastel, un au crayon et une lithographie) réalisées entre 1893 et 1917. Symbolisant l'homme moderne emporté par une crise d'angoisse existentielle, elle est considérée comme l'œuvre la plus importante de l'artiste. Le paysage en arrière-plan est le fjord d'Oslo, vu d'Ekeberg.";
        $Work3->year_created = '1893';
        $Work3->artist_id = $Artist2->id;
        $Work3->save();
    }
}
