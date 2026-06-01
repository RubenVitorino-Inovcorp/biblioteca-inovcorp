<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class GoogleBooksSeeder extends Seeder
{
    /**
     * Temas de pesquisa para criar relações semânticas entre livros
     * usando palavras-chave em portugues para evitar livros em inglês
     * Cada query devolve até 40 resultados, dando ~200 livros reais.
     *
     * @var array<int, string>
     */
    private const QUERIES = [
        // Tecnologia & Engenharia
        'programação orientada a objetos',
        'desenvolvimento web arquitetura',
        'redes de computadores segurança',
        'inteligência artificial algoritmos',
        'sistemas de informação gestão',
        'bases de dados relacionais',
        'engenharia de software testes',

        // Ciências & Matemática
        'cálculo diferencial e integral',
        'física quântica relatividade',
        'química orgânica laboratório',
        'biologia celular molecular',
        'ciência de dados estatística',

        // Ciências Sociais & Gestão
        'história contemporânea de portugal',
        'economia macroeconomia finanças',
        'geografia humana território',
        'psicologia cognitiva comportamento',
        'filosofia introdução pensamento',
        'marketing digital estratégias',

        // Artes & Humanidades
        'história da arte estética',
        'teoria musical composição',
        'cinema português realização',
        'literatura clássica tradução',
        'poesia contemporânea antologia',

        // Ficção & Lazer
        'romance policial investigação',
        'ficção científica galáxia',
        'literatura fantástica dragões',

        'tecnologia "edição portuguesa"',
        'ciência "traduzido para português"',
    ];

    /**
     * Consome a Google Books API para popular a BD com livros reais.
     * Necessário para testar a similaridade de texto do Meilisearch.
     */
    public function run(): void
    {
        $this->command->info('A transferir dados da Google Books API...');

        $importedCount = 0;
        $skippedCount = 0;

        foreach (self::QUERIES as $query) {
            $this->command->info("  → Pesquisa: {$query}");

            $params = [
                'q' => $query,
                'maxResults' => 40,
                'langRestrict' => 'pt',
                'printType' => 'books',
            ];

            $apiKey = config('services.google.books_key');

            if ($apiKey) {
                $params['key'] = $apiKey;
            }

            $response = Http::timeout(15)
                ->retry(3, 500, throw: false)
                ->get('https://www.googleapis.com/books/v1/volumes', $params);

            if ($response->failed()) {
                $this->command->warn("    Falhou (HTTP {$response->status()}). A continuar...");

                continue;
            }

            // Respeitar rate limits entre queries
            $queries = self::QUERIES;

            if ($query !== end($queries)) {
                sleep(1);
            }

            $items = $response->json('items') ?? [];

            foreach ($items as $item) {
                $volumeInfo = $item['volumeInfo'] ?? [];

                if ($this->processBook($volumeInfo)) {
                    $importedCount++;
                } else {
                    $skippedCount++;
                }
            }
        }

        $this->command->info("Importação concluída: {$importedCount} livros importados, {$skippedCount} ignorados.");
        $this->command->info('Executa `php artisan scout:sync-index-settings` e `php artisan scout:import "App\Models\Book"` para sincronizar o Meilisearch.');
    }

    /**
     * Extrai e insere um livro a partir dos dados da Google Books API.
     *
     * @param  array<string, mixed>  $volumeInfo
     */
    private function processBook(array $volumeInfo): bool
    {

        // Ignorar livros que não tenham o idioma em português
        $language = $volumeInfo['language'] ?? '';
        if ($language !== 'pt' && $language !== 'pt-BR' && $language !== 'pt-PT') {
            return false;
        }

        // Ignorar livros sem título ou descrição (inúteis para BM25)
        if (empty($volumeInfo['title']) || empty($volumeInfo['description'])) {
            return false;
        }

        $isbn = $this->extractIsbn($volumeInfo['industryIdentifiers'] ?? []);
        $publisherId = $this->resolvePublisher($volumeInfo['publisher'] ?? null);
        $thumbnail = $this->extractThumbnail($volumeInfo['imageLinks'] ?? []);
        $totalStock = rand(1, 2);

        $book = Book::updateOrCreate(
            ['isbn' => $isbn],
            [
                'title' => Str::limit($volumeInfo['title'], 200),
                'bibliography' => $volumeInfo['description'],
                'publisher_id' => $publisherId,
                'image_path' => $thumbnail,
                'total_stock' => $totalStock,
                'available_stock' => rand(1, $totalStock),
                'price' => rand(8, 45) + (rand(0, 99) / 100),
            ]
        );

        $this->syncAuthors($book, $volumeInfo['authors'] ?? []);
        $this->syncTags($book, $volumeInfo['categories'] ?? []);

        return true;
    }

    private function resolvePublisher(?string $publisherName): ?int
    {
        if (! $publisherName) {
            return null;
        }

        return Publisher::firstOrCreate(['name' => Str::limit($publisherName, 100)])->id;
    }

    /**
     * @param  array<int, string>  $authorNames
     */
    private function syncAuthors(Book $book, array $authorNames): void
    {
        if (empty($authorNames)) {
            return;
        }

        $authorIds = [];

        foreach ($authorNames as $name) {
            $authorIds[] = Author::firstOrCreate(['name' => Str::limit($name, 100)])->id;
        }

        $book->authors()->syncWithoutDetaching($authorIds);
    }

    /**
     * Importa as categorias da Google Books API como tags.
     * Cada categoria pode conter sub-categorias separadas por " / ".
     *
     * @param  array<int, string>  $categories
     */
    private function syncTags(Book $book, array $categories): void
    {
        if (empty($categories)) {
            return;
        }

        $tagIds = [];

        foreach ($categories as $category) {
            // A Google Books devolve categorias como "Computers / Programming Languages"
            $parts = array_map('trim', explode('/', $category));

            foreach ($parts as $part) {
                if ($part !== '') {
                    $tagIds[] = Tag::firstOrCreate(['name' => Str::limit($part, 100)])->id;
                }
            }
        }

        $book->tags()->syncWithoutDetaching($tagIds);
    }

    /**
     * Extrai o ISBN_13 (prioritário) ou ISBN_10 dos identificadores.
     * Gera um fallback único quando a API não fornece ISBN.
     *
     * @param  array<int, array{type: string, identifier: string}>  $identifiers
     */
    private function extractIsbn(array $identifiers): string
    {
        $isbn10 = null;

        foreach ($identifiers as $id) {
            $type = $id['type'] ?? '';

            if ($type === 'ISBN_13') {
                return $id['identifier'];
            }

            if ($type === 'ISBN_10') {
                $isbn10 = $id['identifier'];
            }
        }

        return $isbn10 ?? 'GBOOKS-'.Str::upper(Str::random(9));
    }

    /**
     * Extrai a melhor thumbnail disponível da Google Books.
     *
     * @param  array<string, string>  $imageLinks
     */
    private function extractThumbnail(array $imageLinks): ?string
    {
        // Preferir a imagem maior; converter para HTTPS
        $url = $imageLinks['thumbnail'] ?? $imageLinks['smallThumbnail'] ?? null;

        if ($url) {
            return str_replace('http://', 'https://', $url);
        }

        return null;
    }
}
