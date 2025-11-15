<script setup>
import StarIcon from '@/assets/star.svg';
import StarEmptyIcon from '@/assets/star_empty.svg';

const props = defineProps({
    averageRating: {
        type: Number,
        required: true,
    },
    totalReviews: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <div class="card flex h-[155px] flex-col justify-between p-6">
        <div class="flex items-center gap-4">
            <p class="rating-value-text">
                {{ props.averageRating.toFixed(1) }}
            </p>
            <div class="flex items-center gap-1">
                <img
                    v-for="n in 5"
                    :key="`summary-star-${n}`"
                    :src="n <= Math.round(props.averageRating) ? StarIcon : StarEmptyIcon"
                    alt="Rating star"
                    class="h-4 w-4"
                />
            </div>
        </div>
        <div class="h-[2px] w-full border border-[#F1F4F7]" />
        <div>
            <p class="text-dark-12-bold whitespace-nowrap">
                Всего отзывов: {{ props.totalReviews }}
            </p>
        </div>
    </div>
</template>

