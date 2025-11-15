<script setup>
import Layout from '@/Layouts/Layout.vue';
import YandexMapsIcon from '@/assets/yandex_maps.svg';
import StarIcon from '@/assets/star.svg';
import StarEmptyIcon from '@/assets/star_empty.svg';

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
                    <aside class="col-span-4 xl:col-span-1 xl:order-last order-first">
                        <div class="card flex h-[155px] flex-col justify-between p-6">
                            <div class="flex items-center gap-4">
                                <p class="rating-value-text">
                                    {{ averageRating.toFixed(1) }}
                                </p>
                                <div class="flex items-center gap-1">
                                    <img
                                        v-for="n in 5"
                                        :key="`star-${n}`"
                                        :src="n <= Math.round(averageRating) ? StarIcon : StarEmptyIcon"
                                        alt="Rating star"
                                        class="h-4 w-4"
                                    />
                                </div>
                            </div>
                            <div class="h-[2px] w-full border border-[#F1F4F7]" />
                            <div>
                                <p class="text-dark-12-bold whitespace-nowrap">
                                    Всего отзывов: {{ totalReviews }}
                                </p>
                            </div>
                        </div>
                    </aside>
                    <div class="col-span-4 xl:col-span-3 space-y-4">
                        <article
                            v-for="review in sampleReviews"
                            :key="review.id"
                            class="review-card space-y-3 px-[17px] py-[19px]"
                        >
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-2 text-dark-12-bold">
                                    <span>{{ review.date }} · {{ review.time }}</span>
                                    <span class="inline-flex items-center gap-1 text-dark-12-bold">
                                    <img :src="YandexMapsIcon" alt="location" class="h-4 w-4" />
                                        <span>{{ review.place }}</span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <img
                                        v-for="n in 5"
                                        :key="`card-star-${review.id}-${n}`"
                                        :src="n <= review.rating ? StarIcon : StarEmptyIcon"
                                        alt="Rating"
                                        class="h-4 w-4"
                                    />
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-dark-12-bold">{{ getDisplayName(review.author) }}</span>
                                <span class="review-phone-text">{{ review.phone }}</span>
                            </div>
                            <p class="text-black-12-normal">
                                {{ review.text }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>
    </Layout>
</template>

