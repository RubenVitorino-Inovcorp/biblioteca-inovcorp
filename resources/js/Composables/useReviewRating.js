export function useReviewRating() {
    const getRatingLabel = (rating) => {
        if (!rating) return 'Sem avaliação';
        const LABELS = {
            1: 'Péssimo', 2: 'Muito Mau', 3: 'Mau', 4: 'Abaixo da Média',
            5: 'Razoável', 6: 'Bom', 7: 'Muito Bom', 8: 'Ótimo',
            9: 'Excelente', 10: 'Obra-Prima',
        };
        const key = Math.floor(rating);
        return LABELS[key] || 'Sem classificação';
    };

    return {
        getRatingLabel,
    };
}
