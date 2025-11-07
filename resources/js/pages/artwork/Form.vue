<script setup lang="ts">
import ImageUpload from '@/components/ImageUpload.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/artwork';
import { type BreadcrumbItem } from '@/types';
import { type Artist, type Artwork } from '@/types/data';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Props {
    artwork?: Artwork;
    artists: Artist[];
}

const props = defineProps<Props>();

const isEditing = computed(() => !!props.artwork?.id);
const pageTitle = computed(() =>
    isEditing.value ? "Modifier l'œuvre" : 'Créer une œuvre',
);

const selectedArtistId = ref<number | string>(props.artwork?.artist?.id ?? '');
const newImages = ref<File[]>([]);
const deletedMediaIds = ref<number[]>([]);

watch(
    () => props.artwork?.artist?.id,
    (newVal) => {
        if (newVal) {
            selectedArtistId.value = newVal;
        }
    },
);

const form = useForm({
    title: props.artwork?.title ?? '',
    description: props.artwork?.description ?? '',
    year_created: props.artwork?.yearCreated ?? undefined,
    artist_id: props.artwork?.artist?.id ?? '',
    images: [] as File[],
    deleted_media_ids: [] as number[],
});

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Oeuvres',
        href: index().url,
    },
    {
        title: isEditing.value ? 'Modifier' : 'Créer',
        href:
            isEditing.value && props.artwork?.id
                ? `/artworks/${props.artwork.id}/edit`
                : '/artworks/create',
    },
]);

const handleNewImages = (files: File[]) => {
    newImages.value = files;
    form.images = files;
};

const handleDeletedIds = (ids: number[]) => {
    deletedMediaIds.value = ids;
    form.deleted_media_ids = ids;
};

const submit = () => {
    if (isEditing.value && props.artwork?.id) {
        // Utiliser transform pour ajouter _method uniquement pour l'édition
        form.transform((data) => ({
            ...data,
            _method: 'PATCH',
        })).post(`/artworks/${props.artwork.id}`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                router.visit(index().url);
            },
        });
    } else {
        form.post('/artworks', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                router.visit(index().url);
            },
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="pageTitle" />

        <div class="mx-auto w-full max-w-2xl p-6">
            <div class="flex flex-col space-y-6">
                <div>
                    <h2 class="text-2xl font-semibold">{{ pageTitle }}</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{
                            isEditing
                                ? "Modifiez les informations de l'œuvre"
                                : 'Ajoutez une nouvelle œuvre à la collection'
                        }}
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="title">Titre *</Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            class="mt-1 block w-full"
                            required
                            placeholder="Titre de l'œuvre"
                        />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="artist_id">Artiste *</Label>
                        <select
                            id="artist_id"
                            v-model="form.artist_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            required
                        >
                            <option value="">Sélectionnez un artiste</option>
                            <option
                                v-for="artist in artists"
                                :key="artist.id"
                                :value="artist.id"
                            >
                                {{ artist.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.artist_id"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Description</Label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            placeholder="Description de l'oeuvre"
                            rows="4"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.description"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="year_created">Année de création</Label>
                        <Input
                            id="year_created"
                            v-model="form.year_created"
                            type="number"
                            class="mt-1 block w-full"
                            placeholder="Ex: 1889"
                            min="1"
                            max="9999"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.year_created"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label>Images</Label>
                        <ImageUpload
                            :existing-images="artwork?.images ?? []"
                            @update:new-images="handleNewImages"
                            @update:deleted-ids="handleDeletedIds"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.images"
                        />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="form.processing">
                            {{ isEditing ? 'Mettre à jour' : 'Créer' }}
                        </Button>

                        <Button variant="outline" as-child>
                            <Link :href="index().url">Annuler</Link>
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="form.recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Enregistré.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
