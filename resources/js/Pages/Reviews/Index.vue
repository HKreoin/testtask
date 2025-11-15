<script setup>
import Layout from '@/Layouts/Layout.vue';
import YandexMapsIcon from '@/assets/yandex_maps.svg';
import ReviewCard from '@/Components/ReviewCard.vue';
import RatingSummaryCard from '@/Components/RatingSummaryCard.vue';

const sampleReviews = [
    {
        id: 1,
        author: 'Иван',
        rating: 4,
        date: '12.11.2025',
        time: '14:35',
        place: 'Daily Grow, Невский 14',
        phone: '+7 (900) 123-45-67',
        text: 'Так, с чего начать... Разнообразная алкогольная продукция, множество закусок и обычных блюд. Кухня вкусная и Разнообразная, от супа и салатов до мясных продуктов. Персонал молодые девушки, общительная и доброжелательные, всегда подскажут, вовремя принесут и вызовут такси. Отдыхали на летней веранде, свежо и тепло, в общем самое то в жаркую погоду. Сами залы не сильно рассмотрел, но видел что они удобные и просторные. ',
    },
    {
        id: 2,
        author: 'Мария',
        rating: 4,
        date: '10.11.2025',
        time: '11:20',
        place: 'Daily Grow, Пушкина 8',
        phone: '+7 (921) 555-22-11',
        text: 'Еда вкусная, но хотелось бы чуть быстрее обслуживание. В целом довольна.',
    },
    {
        id: 3,
        author: 'Алексей',
        rating: 5,
        date: '05.11.2025',
        time: '09:50',
        place: 'Daily Grow, Тверская 5',
        phone: '+7 (911) 777-88-00',
        text: 'Лучшее место для встреч в центре города. Всегда свежий кофе и десерты.',
    },
];

const getDisplayName = (name) => name;

const totalReviews = 162;
const averageRating = 4.8;
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
                            :average-rating="averageRating"
                            :total-reviews="totalReviews"
                        />
                    </div>
                    <div class="col-span-4 xl:col-span-3 space-y-4">
                        <ReviewCard
                            v-for="review in sampleReviews"
                            :key="review.id"
                            :date="review.date"
                            :time="review.time"
                            :place="review.place"
                            :rating="review.rating"
                            :author="review.author"
                            :phone="review.phone"
                            :text="review.text"
                        />
                    </div>
                </div>
            </section>
    </Layout>
</template>

