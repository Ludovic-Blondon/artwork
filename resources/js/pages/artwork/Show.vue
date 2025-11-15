<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';
import { dashboard, home, login, register } from '@/routes';
import { Artwork } from '@/types/data';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    artwork: Artwork;
    canRegister?: boolean;
}>();

const imageLoading = ref(true);
const imageError = ref(false);

const handleImageLoad = () => {
    imageLoading.value = false;
};

const handleImageError = () => {
    imageLoading.value = false;
    imageError.value = true;
};
</script>

<template>
    <Head :title="artwork.title">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a] dark:text-[#EDEDEC]"
    >
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-between gap-4">
                <Link
                    :href="home()"
                    class="inline-flex items-center gap-2 rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Retour
                </Link>

                <div class="flex items-center gap-4">
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
                </div>
            </nav>
        </header>

        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full max-w-[335px] flex-col justify-center gap-8 rounded-lg lg:max-w-4xl"
            >
                <!-- Contenu principal -->
                <div class="w-full space-y-8">
                    <!-- Image principale -->
                    <div class="relative flex justify-center rounded-lg">
                        <Skeleton
                            v-if="imageLoading"
                            class="aspect-[4/3] w-full max-w-2xl"
                        />

                        <div
                            v-if="imageError"
                            class="flex aspect-[4/3] w-full max-w-2xl items-center justify-center rounded-md border bg-muted"
                        >
                            <div class="text-center text-muted-foreground">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mx-auto mb-2 h-16 w-16"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                                <p class="text-sm">Image non disponible</p>
                            </div>
                        </div>

                        <img
                            v-if="artwork.featuredImage"
                            :src="artwork.featuredImage"
                            :alt="artwork.title"
                            :class="[
                                'max-h-[600px] max-w-full rounded-lg shadow-md transition-opacity duration-300',
                                imageLoading
                                    ? 'absolute opacity-0'
                                    : 'opacity-100',
                            ]"
                            @load="handleImageLoad"
                            @error="handleImageError"
                        />
                    </div>

                    <!-- Informations de l'Suvre -->
                    <div class="space-y-6">
                        <!-- Titre et année -->
                        <div class="space-y-2">
                            <h1
                                class="text-3xl font-bold tracking-tight lg:text-4xl"
                            >
                                {{ artwork.title }}
                            </h1>
                            <div
                                class="flex flex-wrap items-center gap-4 text-lg"
                            >
                                <div
                                    v-if="artwork.artist.name"
                                    class="text-muted-foreground"
                                >
                                    Par
                                    <span
                                        class="font-semibold text-foreground"
                                        >{{ artwork.artist.name }}</span
                                    >
                                </div>
                                <span
                                    v-if="
                                        artwork.yearCreated &&
                                        artwork.artist.name
                                    "
                                    class="text-muted-foreground"
                                    >•</span
                                >
                                <div
                                    v-if="artwork.yearCreated"
                                    class="text-muted-foreground"
                                >
                                    {{ artwork.yearCreated }}
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div
                            v-if="artwork.description"
                            class="prose prose-neutral dark:prose-invert max-w-none"
                        >
                            <p class="text-base leading-relaxed">
                                {{ artwork.description }}
                            </p>
                        </div>

                        <!-- Message si pas de description -->
                        <div
                            v-else
                            class="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground"
                        >
                            Aucune description disponible pour cette Suvre.
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
