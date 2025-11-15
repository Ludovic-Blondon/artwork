<script setup lang="ts">
import ArtworkCard from '@/components/ArtworkCard.vue';
import { dashboard, login, register } from '@/routes';
import { Artwork, DataPaginated } from '@/types/data';
import { Head, Link } from '@inertiajs/vue3';

// 🔹 Props avec valeurs par défaut cohérentes
withDefaults(
    defineProps<{
        canRegister: boolean;
        paginatedArtworks: DataPaginated<Artwork>;
    }>(),
    {
        canRegister: true,
        paginatedArtworks: () => ({
            data: [],
            meta: {
                current_page: 1,
                last_page: 1,
                per_page: 30,
                total: 0,
            },
            links: [],
        }),
    },
);
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="3xl:max-w-[1600px] mb-6 w-full max-w-full text-sm not-has-[nav]:hidden min-[800px]:max-w-[732px] min-[1150px]:max-w-[1082px] 2xl:max-w-[1448px]"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="masonry-grid 3xl:max-w-[1600px] w-full max-w-full columns-1 min-[800px]:max-w-[732px] min-[800px]:columns-2 min-[1150px]:max-w-[1082px] min-[1150px]:columns-3 2xl:max-w-[1448px] 2xl:columns-4"
            >
                <div
                    v-for="artwork in paginatedArtworks.data"
                    :key="artwork.id"
                    class="masonry-item"
                >
                    <ArtworkCard :artwork="artwork" />
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
