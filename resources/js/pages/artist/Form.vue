<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, store, update } from '@/routes/artist';
import { type BreadcrumbItem } from '@/types';
import { type Artist } from '@/types/data';
import { Form, Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    artist?: Artist;
}

const props = defineProps<Props>();

const isEditing = computed(() => !!props.artist?.id);
const pageTitle = computed(() =>
    isEditing.value ? "Modifier l'artiste" : 'Créer un artiste',
);

const formBinding = computed(() =>
    isEditing.value && props.artist?.id
        ? update.form(props.artist.id)
        : store.form(),
);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Artists',
        href: index().url,
    },
    {
        title: isEditing.value ? 'Modifier' : 'Créer',
        href:
            isEditing.value && props.artist?.id
                ? `/artists/${props.artist.id}/edit`
                : '/artists/create',
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
                                ? "Modifiez les informations de l'artiste"
                                : 'Ajoutez un nouvel artiste à la collection'
                        }}
                    </p>
                </div>

                <Form
                    v-bind="formBinding"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nom *</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            name="name"
                            :default-value="artist?.name"
                            required
                            placeholder="Nom de l'artiste"
                        />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="bio">Biographie</Label>
                        <textarea
                            id="bio"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            name="bio"
                            :value="artist?.bio ?? ''"
                            placeholder="Biographie de l'artiste"
                            rows="4"
                        />
                        <InputError class="mt-2" :message="errors.bio" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="birth_date">Date de naissance</Label>
                        <Input
                            id="birth_date"
                            type="date"
                            class="mt-1 block w-full"
                            name="birth_date"
                            :default-value="artist?.birthDate"
                        />
                        <InputError class="mt-2" :message="errors.birth_date" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="death_date">Date de décès</Label>
                        <Input
                            id="death_date"
                            type="date"
                            class="mt-1 block w-full"
                            name="death_date"
                            :default-value="artist?.deathDate"
                        />
                        <InputError class="mt-2" :message="errors.death_date" />
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
