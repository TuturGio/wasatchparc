<?php
/** Articles du blog (liste /category/blog/, pied des articles). Du plus récent au plus ancien. */

const ARTICLES = [
    [
        'url' => '/what-is-snow-tubing-and-5-reasons-why-its-a-fun-family-activity/',
        'titre' => 'What is Snow Tubing and 5 Reasons Why it’s a Fun Family Activity',
        'date' => '2024-12-04',
        'extrait' => 'Snow tubing is basically allowing gravity, the snow and your tubing take you on a fun, jolly ride downhill.',
    ],
    [
        'url' => '/how-safe-is-snow-tubing/',
        'titre' => 'How safe is snow tubing ?',
        'date' => '2024-12-04',
        'extrait' => 'Snow tubing is an outdoor winter activity similar to sledding which involves riding an inflated inner tube down a snow-covered hill.',
    ],
    [
        'url' => '/5-amazing-benefits-of-winter-sports/',
        'titre' => '5 Amazing Benefits of Winter Sports',
        'date' => '2024-12-03',
        'extrait' => 'Snow tubing is basically allowing gravity, the snow and your tubing take you on a fun, jolly ride downhill.',
    ],
];

/** Date d'un article, en anglais (« December 4, 2024 »), dans une balise <time>. */
function article_date(string $date): string
{
    return '<time datetime="' . $date . '">' . date('F j, Y', strtotime($date)) . '</time>';
}

/** Carte d'article pour une liste ; $titre_niveau : balise du titre (h2 dans la liste du blog, h3 ailleurs). */
function article_carte(array $a, string $titre_niveau = 'h2'): string
{
    return '<article class="m-article-carte">'
        . '<p class="m-article-meta">' . article_date($a['date']) . '</p>'
        . "<$titre_niveau><a href=\"{$a['url']}\">" . htmlspecialchars($a['titre']) . "</a></$titre_niveau>"
        . '<p>' . htmlspecialchars($a['extrait']) . '</p>'
        . '<span class="m-article-lire" aria-hidden="true">Read article →</span>'
        . '</article>';
}

/** Les autres articles que celui de l'adresse $chemin. */
function articles_autres(string $chemin): array
{
    return array_values(array_filter(ARTICLES, fn($a) => $a['url'] !== $chemin));
}
