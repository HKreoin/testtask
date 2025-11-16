<script setup>
import Layout from '@/Layouts/Layout.vue';
import YandexMapsIcon from '@/assets/yandex_maps.svg';
import ReviewCard from '@/Components/ReviewCard.vue';
import RatingSummaryCard from '@/Components/RatingSummaryCard.vue';
import { computed } from 'vue';

const fallbackReviews = [
    {
        id: 1,
        author: 'Нет данных',
        rating: 0,
        date: null,
        time: null,
        place: '—',
        phone: '',
        text: 'Добавьте ссылку в настройках, чтобы мы смогли загрузить реальные отзывы.',
    },
];

const props = defineProps({
    reviews: {
        type: Array,
        default: () => [],
    },
    summary: {
        type: Object,
        default: () => ({ average: null, count: 0 }),
    },
    profileExists: {
        type: Boolean,
        default: false,
    },
});

const displayedReviews = computed(() =>
    props.reviews.length ? props.reviews : fallbackReviews,
);

const averageRating = computed(() => props.summary?.average ?? 0);
const totalReviews = computed(() => props.summary?.count ?? 0);
</script>

<template>
    <Layout active-section="reviews">
        <template #header>Отзывы</template>

            <section class="pl-[26px] pt-[17px] pr-8" aria-label="Последние отзывы">
                <div class="flex h-6 w-28 items-center gap-1.5 rounded-[8px] border border-[#DCE4EA] bg-white pl-1.5">
                    <img
                        :src="YandexMapsIcon"
                        alt="Yandex Maps"
                        class="w-3"
                    />
                   <span class="menu-text">
                        Яндекс карты
                    </span>
                </div>

                <div class="mt-[9px] grid grid-cols-4 gap-[20px]">
                    <div class="col-span-4 xl:col-span-1 xl:order-last order-first">
                        <RatingSummaryCard
                            :average-rating="Number(averageRating)"
                            :total-reviews="Number(totalReviews)"
                        />
                    </div>
                    <div class="col-span-4 xl:col-span-3 space-y-4">
                        <ReviewCard
                            v-for="review in displayedReviews"
                            :key="review.id"
                            :date="review.date"
                            :time="review.time"
                            :place="review.place"
                            :rating="review.rating"
                            :author="review.author"
                            :phone="review.phone"
                            :text="review.text"
                        />
                        <div
                            v-if="!props.profileExists"
                            class="rounded-[12px] border border-dashed border-[#DCE4EA] bg-white px-6 py-5 text-sm text-[#6C757D]"
                        >
                            Добавьте ссылку на карточку в разделе “Настройки”, чтобы мы автоматически
                            получили реальные отзывы и рейтинг из Яндекс Карт.
                        </div>
                    </div>
                </div>
            </section>
    </Layout>
</template>

