<script setup>
import YandexMapsIcon from '@/assets/yandex_maps.svg';
import StarIcon from '@/assets/star.svg';
import StarEmptyIcon from '@/assets/star_empty.svg';
import { computed } from 'vue';

const props = defineProps({
    date: String,
    time: String,
    place: String,
    rating: [Number, String],
    author: String,
    phone: String,
    text: String,
});

const ratingValue = computed(() => Number(props.rating) || 0);
</script>

<template>
    <article class="review-card-outer">
        <div class="review-card-inner">
            <div
                class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-2 text-dark-12-bold">
                    <span>{{ props.date }} · {{ props.time }}</span>
                    <span class="inline-flex items-center gap-1 text-dark-12-bold">
                        <img :src="YandexMapsIcon" alt="location" class="h-4 w-4" />
                        <span>{{ props.place }}</span>
                    </span>
                </div>
                <div class="flex items-center gap-1">
                    <img
                        v-for="n in 5"
                        :key="`card-star-${n}`"
                        :src="n <= ratingValue ? StarIcon : StarEmptyIcon"
                        alt="Rating"
                        class="h-4 w-4"
                    />
                </div>
            </div>
            <div class="flex items-center gap-2 mt-3">
                <span class="text-dark-12-bold">{{ props.author }}</span>
                <span class="review-phone-text">{{ props.phone }}</span>
            </div>
            <p class="text-black-12-normal mt-3">
                {{ props.text }}
            </p>
        </div>
    </article>
</template>

