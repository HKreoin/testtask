<script setup>
import YandexMapsIcon from '@/assets/yandex_maps.svg';
import StarIcon from '@/assets/star.svg';
import StarEmptyIcon from '@/assets/star_empty.svg';

const props = defineProps({
    date: String,
    time: String,
    place: String,
    rating: Number,
    author: String,
    phone: String,
    text: String,
});
</script>

<template>
    <article class="review-card space-y-3 px-[17px] py-[19px]">
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
                    :src="n <= props.rating ? StarIcon : StarEmptyIcon"
                    alt="Rating"
                    class="h-4 w-4"
                />
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-dark-12-bold">{{ props.author }}</span>
            <span class="review-phone-text">{{ props.phone }}</span>
        </div>
        <p class="text-black-12-normal">
            {{ props.text }}
        </p>
    </article>
</template>

