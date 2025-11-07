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

        $Artist = new \App\Models\Artist;
        $Artist->name = 'Leonardo da Vinci';
        $Artist->bio = "Léonard de Vinci, né le 15 avril 1452 à Vinci en Italie et mort le 2 mai 1519 à Amboise en France, est un peintre, sculpteur, architecte, ingénieur, scientifique et inventeur italien de la Renaissance. Considéré comme l'un des plus grands génies de l'histoire, Léonard de Vinci est célèbre pour ses œuvres artistiques emblématiques telles que La Joconde et La Cène, ainsi que pour ses contributions dans divers domaines scientifiques et techniques.";
        $Artist->birth_date = '1452-04-15';
        $Artist->death_date = '1519-05-02';
        $Artist->save();

        $Artwork = new \App\Models\Artwork;
        $Artwork->title = 'La joconde';
        $Artwork->description = "La Joconde ou Mona Lisa est un portrait peint par Léonard de Vinci entre 1503 et 1506, peut-être jusqu'en 1517. Il représente Lisa Gherardini, l'épouse de Francesco del Giocondo, un marchand florentin. Célèbre pour son sourire énigmatique, ce tableau est considéré comme l'une des œuvres d'art les plus célèbres et les plus reconnaissables au monde.";
        $Artwork->year_created = '1503';
        $Artwork->artist_id = $Artist->id;
        $Artwork->save();
        $Artwork->addMedia(base_path('tests/files/medias/joconde.jpg'))
            ->usingName($Artwork->title)
            ->toMediaCollection('images');

        $Artwork2 = new \App\Models\Artwork;
        $Artwork2->title = 'La cène';
        $Artwork2->description = "La Cène (en italien : L'Ultima Cena, soit « Le Dernier Repas ») de Léonard de Vinci est une peinture murale à la détrempe de 460 × 880 cm, réalisée de 1495 à 1498 pour le réfectoire du couvent dominicain de Santa Maria delle Grazie à Milan.";
        $Artwork2->year_created = '1498';
        $Artwork2->artist_id = $Artist->id;
        $Artwork2->save();
        $Artwork2->addMedia(base_path('tests/files/medias/cene.jpg'))
            ->usingName($Artwork2->title)
            ->toMediaCollection('images');

        $Artist2 = new \App\Models\Artist;
        $Artist2->name = 'Edvard Munch';
        $Artist2->bio = "Edvard Munch, prononcé [muŋk], né le 12 décembre 1863 à Ådalsbruk et mort le 23 janvier 1944 à Oslo, est un peintre et graveur expressionniste norvégien. Edvard Munch peut, a posteriori, être considéré après l'exposition berlinoise de 1892, comme le pionnier de l'expressionnisme dans la peinture moderne.";
        $Artist2->birth_date = '1863-12-12';
        $Artist2->death_date = '1944-01-23';
        $Artist2->save();

        $Artwork3 = new \App\Models\Artwork;
        $Artwork3->title = 'Le cri';
        $Artwork3->description = "Le Cri (en norvégien : Skrik) est une œuvre expressionniste de l'artiste norvégien Edvard Munch dont il existe cinq versions (deux peintures, un pastel, un au crayon et une lithographie) réalisées entre 1893 et 1917. Symbolisant l'homme moderne emporté par une crise d'angoisse existentielle, elle est considérée comme l'œuvre la plus importante de l'artiste. Le paysage en arrière-plan est le fjord d'Oslo, vu d'Ekeberg.";
        $Artwork3->year_created = '1893';
        $Artwork3->artist_id = $Artist2->id;
        $Artwork3->save();
        $Artwork3->addMedia(base_path('tests/files/medias/cri.jpg'))
            ->usingName($Artwork3->title)
            ->toMediaCollection('images');
    }
}
