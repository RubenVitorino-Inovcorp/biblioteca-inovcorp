<?php

namespace Database\Factories;

use App\Enums\ReviewStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Modelos de títulos de review contextualizados.
     * O placeholder {BOOK} é substituído pelo título do livro.
     *
     * @var array<int, string>
     */
    private const TITLE_TEMPLATES = [
        'Opinião sobre {BOOK}',
        'Análise de {BOOK}',
        'O que achei de {BOOK}',
        'Leitura de {BOOK}',
        'Comentários sobre {BOOK}',
        'Notas de leitura: {BOOK}',
        'A minha perspetiva sobre {BOOK}',
        'Reflexões sobre {BOOK}',
        'Crítica a {BOOK}',
        'As minhas impressões de {BOOK}',
        'Sobre o livro {BOOK}',
        'Avaliação: {BOOK}',
    ];

    /**
     * Modelos de corpo de review contextualizados.
     * Os placeholders {BOOK} e {TAGS} são substituídos pelos dados do livro.
     *
     * @var array<int, array<int, string>>
     */
    private const POSITIVE_TEXTS = [
        'Acabei de ler {BOOK} e posso dizer que é uma das melhores leituras que fiz recentemente. A abordagem sobre {TAGS} é clara e acessível, mesmo para quem não domina o tema. Recomendo sem hesitar.',
        'Adquiri o livro por curiosidade e fiquei surpreendido com a qualidade do conteúdo. O autor explica os {TAGS} de uma forma super interessante e organizada. Vou voltar a pedir livros deste género, de certeza.',
        '{BOOK} é daqueles que nos deixa com vontade de continuar a ler. Os temas de {TAGS} são apresentados de forma prática e interessante.',
        'O livro trata dos temas com uma profundidade que não se encontra em livros para o público em geral. Estou muito satisfeito.',
        'Li o livro num instante, porque é super cativante. O tratamento dado a {TAGS} é exemplar. Já acabei por recomendar a vários colegas.',
    ];

    private const NEUTRAL_TEXTS = [
        'O {BOOK} tem pontos positivos, especialmente no que toca a {TAGS}, mas senti que em algumas secções o ritmo abrandava. No geral, acho que é uma leitura aceitável para quem se interessa pelo tema.',
        'Adquiri o livro com grandes expetativas. A parte sobre {TAGS} está muito bem feita, mas esperava mais exemplos práticos. Não é mau, mas também não é excecional.',
        'O {BOOK} faz o que promete, mas não tem grandes surpresas. A cobertura de {TAGS} é boa, mas poderia ser mais aprofundada em alguns aspetos. Recomendo para quem quer começar a aprender sobre o tema.',
        'Gostei de alguns capítulos do livro, sobretudo os que falam sobre {TAGS}. Outros pareceram-me demasiado teóricos. Acho que está razoável.',
    ];

    private const NEGATIVE_TEXTS = [
        'Infelizmente o {BOOK} não correspondeu às minhas expectativas. A abordagem sobre {TAGS} pareceu-me desatualizada e pouco prática. Há alternativas melhores na biblioteca.',
        'Tentei gostar do livro, mas a escrita é confusa e os temas de {TAGS} são tratados de forma pouco clara. Não recomendo a quem procura clareza.',
        'O {BOOK} precisa de uma revisão urgente. Os conteúdos sobre {TAGS} estão desorganizados e cheios de informações erradas. Devolvi antes do prazo.',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rating = $this->faker->randomElement([1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0, 5.5, 6.0, 6.5, 7.0, 7.5, 8.0, 8.5, 9.0, 9.5, 10.0]);

        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'loan_id' => Loan::factory(),
            'review_title' => $this->faker->sentence(4),
            'review_text' => $this->faker->paragraph(3),
            'rating' => $rating,
            'status' => $this->faker->randomElement([
                ReviewStatus::APPROVED,
                ReviewStatus::APPROVED,
                ReviewStatus::APPROVED,
                ReviewStatus::PENDING,
                ReviewStatus::REJECTED,
            ]),
        ];
    }

    /**
     * Gera uma review contextualizada com base no livro e no empréstimo fornecidos.
     */
    public function forBookAndLoan(Book $book, Loan $loan): static
    {
        $book->loadMissing('tags');
        $tags = $book->tags->pluck('name')->join(', ') ?: 'este tema';
        $title = $book->title;

        return $this->state(function () use ($title, $tags, $loan) {
            $rating = $this->weightedRating();

            $reviewTitle = str_replace('{BOOK}', $title, $this->faker->randomElement(self::TITLE_TEMPLATES));

            $pool = match (true) {
                $rating >= 7.5 => self::POSITIVE_TEXTS,
                $rating >= 4.0 => self::NEUTRAL_TEXTS,
                default => self::NEGATIVE_TEXTS,
            };

            $reviewText = str_replace(
                ['{BOOK}', '{TAGS}'],
                [$title, $tags],
                $this->faker->randomElement($pool)
            );

            return [
                'user_id' => $loan->user_id,
                'book_id' => $loan->book_id,
                'loan_id' => $loan->id,
                'review_title' => $reviewTitle,
                'review_text' => $reviewText,
                'rating' => $rating,
                'status' => $this->faker->randomElement([
                    ReviewStatus::APPROVED,
                    ReviewStatus::APPROVED,
                    ReviewStatus::APPROVED,
                    ReviewStatus::PENDING,
                    ReviewStatus::REJECTED,
                ]),
            ];
        });
    }

    /**
     * Distribuição com mais peso para ratings altos para tornar a distribuição de avaliações mais realista.
     */
    private function weightedRating(): float
    {
        $weights = [
            10.0 => 25,
            9.5 => 20,
            9.0 => 20,
            8.5 => 15,
            8.0 => 10,
            7.5 => 5,
            7.0 => 3,
            6.5 => 1,
            6.0 => 1,
            5.5 => 1,
            5.0 => 1,
            4.5 => 1,
            4.0 => 1,
            3.5 => 1,
            3.0 => 1,
            2.5 => 5,
            2.0 => 3,
            1.5 => 1,
            1.0 => 1,
        ];

        $total = array_sum($weights);
        $rand = rand(1, $total);
        $cumulative = 0;

        foreach ($weights as $rating => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return (float) $rating;
            }
        }

        return 4.0;
    }
}
