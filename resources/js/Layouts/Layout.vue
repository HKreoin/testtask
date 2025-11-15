<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import DailyGrowLogo from '@/assets/dailygrow.svg';
import CompanyLogo from '@/assets/Logo.svg';
import SpannerIcon from '@/assets/spanner.svg';
import ExitIcon from '@/assets/exit.svg';

const props = defineProps({
    activeSection: {
        type: String,
        default: 'reviews',
    },
});

const page = usePage();

const userName = computed(() => page.props.auth?.user?.name ?? 'Daily Grow');
const accountName = computed(
    () => page.props.auth?.user?.email ?? 'account@daily.grow',
);

const menuItems = [
    { key: 'reviews', label: 'Отзывы', routeName: 'reviews.index' },
    { key: 'settings', label: 'Настройки', routeName: 'settings.index' },
];

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-white text-[#252733]">
        <div
            class="mx-auto flex min-h-screen w-full max-w-[1381px] bg-white"
        >
            <aside
                class="w-70 bg-[#F6F8FA] shadow-[0px_4px_3px_rgba(229,229,229,1)]"
            >
                    <div class="flex h-7 w-40 items-end gap-2 ms-[29px] mt-[30px]">
                        <img
                            :src="CompanyLogo"
                            alt="Logo"
                            class="h-7 w-4 self-center"
                        />
                        <img
                            :src="DailyGrowLogo"
                            alt="Daily Grow"
                            class="h-6 w-full self-end"
                        />
                    </div>
                    <div
                        class="ms-[15px] mt-[14px] font-mulish text-[16px] font-bold leading-5 tracking-[0.2px] text-[#6C757D]"
                    >
                        {{ userName }}
                    </div>
                    <div class="px-4 mt-[28px]">
                    <div
                        class="mt-7 flex h-12 w-[249px] items-center gap-3 rounded-[12px] bg-white px-[14px] text-[#363740] shadow-[0px_2px_1px_rgba(0,0,0,0.02)]"
                    >
                        <span
                            class="flex h-10 w-10 items-center justify-center"
                        >
                            <img
                                :src="SpannerIcon"
                                alt="icon"
                                class="h-6 w-6"
                            />
                        </span>
                    <div
                        class="heading-m text-[#363740]"
                    >
                            Отзывы
                        </div>
                    </div>
                    <div class="mt-8 flex flex-col gap-2">
                        <Link
                            v-for="item in menuItems"
                            :key="item.key"
                            :href="route(item.routeName)"
                            class="flex h-[23px] w-[249px] items-center rounded-[12px] transition"
                            :class="
                                item.key === props.activeSection
                                    ? 'bg-white text-[#363740] shadow-[0px_2px_1px_rgba(0,0,0,0.02)] ps-12'
                                    : 'text-[#6C757D] hover:bg-white/70 ps-12'
                            "
                        >
                            <span
                            class="menu-text"
                                :class="
                                    item.key === props.activeSection
                                        ? 'text-[#363740]'
                                        : 'text-[#6C757D]'
                                "
                            >
                                {{ item.label }}
                            </span>
                        </Link>
                    </div>
                    </div>
            </aside>

            <div class="flex-1 bg-white">
                <header
                    class="relative flex h-[75px] items-center border-b border-[#DCE4EA] bg-white px-6"
                >
                    <h1 class="sr-only">
                        <slot name="header">Заголовок</slot>
                    </h1>
                    <button
                        type="button"
                        @click="logout"
                        class="absolute right-[17px] top-[14px] flex h-11 w-11 items-center justify-center transition hover:bg-[#F6F8FA]"
                    >
                        <img
                            :src="ExitIcon"
                            alt="Выход"
                            class="h-11 w-11"
                        />
                    </button>
                </header>

                <main class="min-h-[calc(100vh-75px)] bg-white">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

