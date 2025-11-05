<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store, update } from '@/routes/work';
import { type BreadcrumbItem } from '@/types';
import { type Artist, type Work } from '@/types/data';
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    work?: Work;
    artists: Artist[];
}

const props = defineProps<Props>();

const isEditing = computed(() => !!props.work?.id);
const pageTitle = computed(() =>
    isEditing.value ? "Modifier l'œuvre" : 'Créer une œuvre',
);

const formBinding = computed(() =>
    isEditing.value && props.work?.id
        ? update.form(props.work.id)
        : store.form(),
);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Works',
        href: index().url,
    },
    {
        title: isEditing.value ? 'Modifier' : 'Créer',
        href:
            isEditing.value && props.work?.id
                ? `/works/${props.work.id}/edit`
                : '/works/create',
    },
]);
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

                <Form
                    v-bind="formBinding"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="title">Titre *</Label>
                        <Input
                            id="title"
                            class="mt-1 block w-full"
                            name="title"
                            :default-value="work?.title"
                            required
                            placeholder="Titre de l'œuvre"
                        />
                        <InputError class="mt-2" :message="errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="artist_id">Artiste *</Label>
                        <select
                            id="artist_id"
                            name="artist_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            required
                        >
                            <option value="">Sélectionnez un artiste</option>
                            <option
                                v-for="artist in artists"
                                :key="artist.id"
                                :value="artist.id"
                                :selected="work?.artist.id === artist.id"
                            >
                                {{ artist.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="errors.artist_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="description">Description</Label>
                        <textarea
                            id="description"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            name="description"
                            :value="work?.description ?? ''"
                            placeholder="Description de l'oeuvre"
                            rows="4"
                        />
                        <InputError
                            class="mt-2"
                            :message="errors.description"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="year_created">Année de création</Label>
                        <Input
                            id="year_created"
                            type="number"
                            class="mt-1 block w-full"
                            name="year_created"
                            :default-value="work?.yearCreated"
                            placeholder="Ex: 1889"
                            min="1"
                            max="9999"
                        />
                        <InputError
                            class="mt-2"
                            :message="errors.year_created"
                        />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="processing">
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
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Enregistré.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
