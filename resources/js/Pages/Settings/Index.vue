<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/Layout.vue';

const yandexLink = ref('');
const saving = ref(false);

const handleSubmit = () => {
    saving.value = true;
    setTimeout(() => {
        saving.value = false;
    }, 600);
};
</script>

<template>
    <Layout active-section="settings">
        <template #header>Настройки</template>

        <section
            class="ps-[35px] pt-[18px]"
        >
            <h2 class="heading-m">
                Подключить Яндекс
            </h2>

            <div class="mt-[15px] space-y-1">
                <p class="form-hint">
                    Укажите ссылку на Яндекс, пример
                </p>
                <a
                    class="form-example"
                    href="https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/"
                    target="_blank"
                    rel="noreferrer"
                >
                    https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/
                </a>
            </div>

            <form class="mt-[11px] space-y-6" @submit.prevent="handleSubmit">
                <label class="flex flex-col gap-2">
                    <span class="sr-only">Ссылка на Яндекс</span>
                    <input
                        v-model="yandexLink"
                        type="url"
                        name="yandex_link"
                        placeholder="https://yandex.ru/maps/org/samoye_populyarnoye_kafe/1010501395/reviews/"
                        class="h-[24px] w-[480px] rounded-md border border-[#DCE4EA] bg-white px-4 text-[12px] text-[#788397] underline shadow-sm focus:border-[#339AF0] focus:ring-[#339AF0]"
                        required
                    />
                </label>

                <button
                    type="submit"
                    :disabled="saving"
                    class="btn-primary"
                >
                    {{ saving ? 'Сохраняю...' : 'Сохранить' }}
                </button>
            </form>
        </section>
    </Layout>
</template>

