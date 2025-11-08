<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->withoutTwoFactor()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->seedArtistsAndArtworks();
    }

    /**
     * Seed artists and their artworks with real art history data.
     */
    private function seedArtistsAndArtworks(): void
    {
        $artistsData = $this->getArtistsData();

        foreach ($artistsData as $artistData) {
            $artist = Artist::factory()->create([
                'name' => $artistData['name'],
                'bio' => $artistData['bio'],
                'birth_date' => $artistData['birth_date'],
                'death_date' => $artistData['death_date'],
            ]);

            foreach ($artistData['artworks'] as $artworkData) {
                $artwork = Artwork::factory()
                    ->forArtist($artist)
                    ->create([
                        'title' => $artworkData['title'],
                        'description' => $artworkData['description'],
                        'year_created' => $artworkData['year'],
                    ]);

                if (isset($artworkData['image'])) {
                    $artwork->addMedia(base_path('tests/files/medias/'.$artworkData['image']))
                        ->preservingOriginal()
                        ->usingName($artwork->title)
                        ->toMediaCollection('images');
                }
            }
        }
    }

    /**
     * Get the artists and artworks data.
     */
    private function getArtistsData(): array
    {
        return [
            [
                'name' => 'Leonardo da Vinci',
                'bio' => "Léonard de Vinci, né le 15 avril 1452 à Vinci en Italie et mort le 2 mai 1519 à Amboise en France, est un peintre, sculpteur, architecte, ingénieur, scientifique et inventeur italien de la Renaissance. Considéré comme l'un des plus grands génies de l'histoire, Léonard de Vinci est célèbre pour ses œuvres artistiques emblématiques telles que La Joconde et La Cène, ainsi que pour ses contributions dans divers domaines scientifiques et techniques.",
                'birth_date' => '1452-04-15',
                'death_date' => '1519-05-02',
                'artworks' => [
                    [
                        'title' => 'La joconde',
                        'description' => "La Joconde ou Mona Lisa est un portrait peint par Léonard de Vinci entre 1503 et 1506, peut-être jusqu'en 1517. Il représente Lisa Gherardini, l'épouse de Francesco del Giocondo, un marchand florentin. Célèbre pour son sourire énigmatique, ce tableau est considéré comme l'une des œuvres d'art les plus célèbres et les plus reconnaissables au monde.",
                        'year' => '1503',
                        'image' => 'joconde.jpg',
                    ],
                    [
                        'title' => 'La cène',
                        'description' => "La Cène (en italien : L'Ultima Cena, soit « Le Dernier Repas ») de Léonard de Vinci est une peinture murale à la détrempe de 460 × 880 cm, réalisée de 1495 à 1498 pour le réfectoire du couvent dominicain de Santa Maria delle Grazie à Milan.",
                        'year' => '1498',
                        'image' => 'cene.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Edvard Munch',
                'bio' => "Edvard Munch, prononcé [muŋk], né le 12 décembre 1863 à Ådalsbruk et mort le 23 janvier 1944 à Oslo, est un peintre et graveur expressionniste norvégien. Edvard Munch peut, a posteriori, être considéré après l'exposition berlinoise de 1892, comme le pionnier de l'expressionnisme dans la peinture moderne.",
                'birth_date' => '1863-12-12',
                'death_date' => '1944-01-23',
                'artworks' => [
                    [
                        'title' => 'Le cri',
                        'description' => "Le Cri (en norvégien : Skrik) est une œuvre expressionniste de l'artiste norvégien Edvard Munch dont il existe cinq versions (deux peintures, un pastel, un au crayon et une lithographie) réalisées entre 1893 et 1917. Symbolisant l'homme moderne emporté par une crise d'angoisse existentielle, elle est considérée comme l'œuvre la plus importante de l'artiste. Le paysage en arrière-plan est le fjord d'Oslo, vu d'Ekeberg.",
                        'year' => '1893',
                        'image' => 'cri.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Vincent van Gogh',
                'bio' => "Vincent Willem van Gogh, né le 30 mars 1853 à Groot-Zundert (Pays-Bas) et mort le 29 juillet 1890 à Auvers-sur-Oise (France), est un peintre et dessinateur néerlandais. Son œuvre pleine de naturalisme, inspirée par l'impressionnisme et le pointillisme, annonce le fauvisme et l'expressionnisme.",
                'birth_date' => '1853-03-30',
                'death_date' => '1890-07-29',
                'artworks' => [
                    [
                        'title' => 'La nuit étoilée',
                        'description' => "La Nuit étoilée est une peinture de l'artiste post-impressionniste néerlandais Vincent van Gogh. Le tableau représente ce que Van Gogh pouvait voir et extrapoler de la chambre qu'il occupait dans l'asile du monastère Saint-Paul-de-Mausole à Saint-Rémy-de-Provence en mai 1889.",
                        'year' => '1889',
                        'image' => 'la-nuit-etoilee.jpg',
                    ],
                    [
                        'title' => 'Les tournesols',
                        'description' => "Les Tournesols est le nom attribué à chacun des tableaux d'une série de sept toiles peintes à Arles par Vincent van Gogh entre août 1888 et janvier 1889.",
                        'year' => '1888',
                        'image' => 'tournesols.jpg',
                    ],
                    [
                        'title' => 'La chambre à coucher',
                        'description' => "La Chambre de Van Gogh à Arles est une peinture à l'huile sur toile de 72 × 90 cm. Elle a été réalisée par le peintre Vincent van Gogh en 1888.",
                        'year' => '1888',
                        'image' => 'la-chambre.jpg',
                    ],
                    [
                        'title' => 'Les mangeurs de pommes de terre',
                        'description' => "Les Mangeurs de pommes de terre est une peinture à l'huile réalisée par le peintre néerlandais Vincent van Gogh en 1885, alors qu'il vivait à Nuenen, aux Pays-Bas.",
                        'year' => '1885',
                        'image' => 'les-mangeurs-de-pomme-de-terre-Chef-doeuvre-Van-Gogh.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Pablo Picasso',
                'bio' => "Pablo Ruiz Picasso, né à Malaga (Espagne) le 25 octobre 1881 et mort le 8 avril 1973 à Mougins (Alpes-Maritimes, France), est un peintre, dessinateur, sculpteur et graveur espagnol ayant passé l'essentiel de sa vie en France. Artiste utilisant tous les supports pour son travail, il est considéré comme le fondateur du cubisme avec Georges Braque et un compagnon d'art du surréalisme.",
                'birth_date' => '1881-10-25',
                'death_date' => '1973-04-08',
                'artworks' => [
                    [
                        'title' => "Les Demoiselles d'Avignon",
                        'description' => "Les Demoiselles d'Avignon est une peinture à l'huile sur toile, de style cubiste, de grand format (243,9 × 233,7 cm), réalisée à Paris par Pablo Picasso en 1907.",
                        'year' => '1907',
                        'image' => 'Picasso-les-demoiselles-d-avignon.jpg',
                    ],
                    [
                        'title' => 'Guernica',
                        'description' => "Guernica est une peinture de Pablo Picasso, réalisée en 1937, à la suite du bombardement de la ville de Guernica, le 26 avril 1937, pendant la guerre d'Espagne.",
                        'year' => '1937',
                        'image' => 'guernica.jpg',
                    ],
                    [
                        'title' => 'La femme qui pleure',
                        'description' => "La Femme qui pleure est une série de peintures de Pablo Picasso peintes en 1937. Elle représente Dora Maar, compagne et muse de l'artiste.",
                        'year' => '1937',
                        'image' => 'la-femme-qui-pleure.jpeg',
                    ],
                ],
            ],
            [
                'name' => 'Claude Monet',
                'bio' => "Claude Monet, né le 14 novembre 1840 à Paris et mort le 5 décembre 1926 à Giverny, est un peintre français et l'un des fondateurs de l'impressionnisme. Il est reconnu comme étant l'un des créateurs de ce mouvement dont il va être le représentant le plus constant et le plus prolifique.",
                'birth_date' => '1840-11-14',
                'death_date' => '1926-12-05',
                'artworks' => [
                    [
                        'title' => 'Impression, soleil levant',
                        'description' => "Impression, soleil levant est un tableau de Claude Monet conservé au musée Marmottan à Paris, dont le titre donné pour la première exposition impressionniste d'avril 1874 a donné son nom au mouvement impressionniste.",
                        'year' => '1872',
                        'image' => 'Claude_Monet,_Impression,_soleil_levant.jpg',
                    ],
                    [
                        'title' => 'Les Nymphéas',
                        'description' => "Les Nymphéas sont une série d'environ 250 peintures à l'huile créées par Claude Monet (1840–1926). Les peintures dépeignent le jardin de fleurs de Monet à Giverny et furent le principal sujet de la production artistique de Monet pendant les trente dernières années de sa vie.",
                        'year' => '1916',
                        'image' => 'les-nympheas.jpg',
                    ],
                    [
                        'title' => 'La Cathédrale de Rouen',
                        'description' => "La Cathédrale de Rouen est le sujet d'une série de tableaux peints par l'artiste impressionniste français Claude Monet en 1892 et 1893.",
                        'year' => '1893',
                        'image' => 'la-cathedrale-de-rouen.jpeg',
                    ],
                    [
                        'title' => 'Femmes au jardin',
                        'description' => 'Femmes au jardin est un tableau de Claude Monet peint en 1866 et 1867.',
                        'year' => '1866',
                        'image' => 'femme-au-jardin.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Frida Kahlo',
                'bio' => 'Frida Kahlo de Rivera, née Magdalena Carmen Frida Kahlo y Calderón le 6 juillet 1907 dans une demeure construite par son père, la Casa Azul, à Coyoacán au Mexique, et morte dans cette même maison le 13 juillet 1954, est une artiste peintre mexicaine. Son œuvre présente essentiellement des autoportraits, marqués par les influences de la culture mexicaine et de thèmes comme le handicap, la stérilité, la sexualité et la mortalité.',
                'birth_date' => '1907-07-06',
                'death_date' => '1954-07-13',
                'artworks' => [
                    [
                        'title' => 'Les deux Frida',
                        'description' => "Les Deux Frida est l'un des tableaux les plus connus de Frida Kahlo. Il a été peint en 1939, lors de son divorce d'avec Diego Rivera.",
                        'year' => '1939',
                        'image' => 'les-deux-fridas-portrait.jpg',
                    ],
                    [
                        'title' => 'La colonne brisée',
                        'description' => "La Colonne brisée est un tableau de Frida Kahlo peint en 1944. L'œuvre montre Kahlo à peu près nue, fendue au milieu, révélant une colonne brisée à la place de sa colonne vertébrale.",
                        'year' => '1944',
                        'image' => 'la-colonne-brisee.jpg',
                    ],
                    [
                        'title' => 'Autoportrait à la frontière entre le Mexique et les États-Unis',
                        'description' => 'Autoportrait à la frontière entre le Mexique et les États-Unis est un tableau réalisé par Frida Kahlo en 1932.',
                        'year' => '1932',
                        'image' => 'autoportrait_sur_la_frontiere.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Johannes Vermeer',
                'bio' => "Johannes ou Jan Vermeer, dit Vermeer de Delft ou le Sphinx de Delft, baptisé à Delft le 31 octobre 1632, et inhumé dans cette même ville le 15 décembre 1675, est un peintre baroque néerlandais parmi les plus célèbres du siècle d'or néerlandais.",
                'birth_date' => '1632-10-31',
                'death_date' => '1675-12-15',
                'artworks' => [
                    [
                        'title' => 'La Jeune Fille à la perle',
                        'description' => "La Jeune Fille à la perle est une peinture à l'huile sur toile réalisée par le peintre néerlandais Johannes Vermeer vers 1665. Le tableau représente une jeune femme — peut-être une domestique — portant une perle à l'oreille et un turban jaune et bleu.",
                        'year' => '1665',
                        'image' => '1665_Girl_with_a_Pearl_Earring.jpg',
                    ],
                    [
                        'title' => 'La Laitière',
                        'description' => 'La Laitière est un tableau de Johannes Vermeer, peint vers 1658. Il représente une servante versant du lait dans un pot.',
                        'year' => '1658',
                        'image' => 'la-laitiere.jpg',
                    ],
                    [
                        'title' => 'Vue de Delft',
                        'description' => 'Vue de Delft est un tableau de Johannes Vermeer peint vers 1660-1661.',
                        'year' => '1660',
                        'image' => 'Vermeer-view-of-delft.jpg',
                    ],
                ],
            ],
        ];
    }
}
