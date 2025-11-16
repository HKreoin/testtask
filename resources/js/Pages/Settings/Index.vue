<script setup>
import Layout from '@/Layouts/Layout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    profile: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    reviews_link: props.profile?.reviews_link ?? '',
});

const status = computed(() => usePage().props.flash?.status);

const submit = () => {
    form.post(route('settings.store'));
};
</script>

<template>
    <Layout active-section="settings">
        <template #header>Настройки</template>

        <section class="ps-[35px] pt-[18px]">
            <h2 class="heading-m">Подключить Яндекс</h2>

            <div class="mt-[15px] space-y-1">
                <p class="form-hint">Укажите ссылку на Яндекс, пример</p>
                <a
                    class="form-example"
                    href="https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/"
                    target="_blank"
                    rel="noreferrer"
                >
                    https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/
                </a>
            </div>

            <form class="mt-[11px] space-y-6" @submit.prevent="submit">
                <label class="flex flex-col gap-2">
                    <span class="sr-only">Ссылка на Яндекс</span>
                    <input
                        v-model="form.reviews_link"
                        type="url"
                        name="reviews_link"
                        placeholder="https://yandex.ru/maps/org/..."
                        class="h-[24px] w-[480px] rounded-md border border-[#DCE4EA] bg-white px-4 text-[12px] text-[#788397] underline shadow-sm focus:border-[#339AF0] focus:ring-[#339AF0]"
                        @click="$event.target.select()"
                        @mouseup.prevent
                        required
                    />
                    <span v-if="form.errors.reviews_link" class="text-sm text-red-500">
                        {{ form.errors.reviews_link }}
                    </span>
                </label>

                <button type="submit" :disabled="form.processing" class="btn-primary">
                    {{ form.processing ? 'Сохраняю...' : 'Сохранить' }}
                </button>

                <p v-if="status" class="text-sm text-[#339AF0]">
                    {{ status }}
                </p>
            </form>
        </section>
    </Layout>
</template>