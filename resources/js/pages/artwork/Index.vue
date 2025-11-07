<script setup lang="ts">
import ConfirmDelete from '@/components/ConfirmDelete.vue';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, edit, index } from '@/routes/artwork';
import { type BreadcrumbItem } from '@/types';
import { Artwork, DataPaginated } from '@/types/data';
import { Head, Link } from '@inertiajs/vue3';
import { ImageIcon, Pencil, Plus } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Oeuvres',
        href: index().url,
    },
];

withDefaults(
    defineProps<{
        paginatedArtworks: DataPaginated<Artwork>;
    }>(),
    {
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
    <Head title="Artwork" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex justify-end">
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Créer une œuvre
                    </Link>
                </Button>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <Table>
                    <TableCaption>Liste des oeuvres.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[80px]">Image</TableHead>
                            <TableHead class="w-[80px]">Id</TableHead>
                            <TableHead> Titre </TableHead>
                            <TableHead>description</TableHead>
                            <TableHead>Année</TableHead>
                            <TableHead> Auteur </TableHead>
                            <TableHead class="text-right"> Action </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="artwork in paginatedArtworks.data"
                            :key="artwork.id"
                        >
                            <TableCell>
                                <div
                                    class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-md border bg-muted"
                                >
                                    <img
                                        v-if="artwork.featuredImage"
                                        :src="artwork.featuredImage"
                                        :alt="artwork.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <ImageIcon
                                        v-else
                                        class="h-8 w-8 text-muted-foreground"
                                    />
                                </div>
                            </TableCell>
                            <TableCell class="font-medium">
                                {{ artwork.id }}
                            </TableCell>
                            <TableCell>
                                {{ artwork.title }}
                            </TableCell>
                            <TableCell>{{
                                artwork.description?.slice(0, 25) + ' ...'
                            }}</TableCell>
                            <TableCell>{{ artwork.yearCreated }}</TableCell>
                            <TableCell>{{ artwork.artist.name }}</TableCell>
                            <TableCell class="space-x-1 text-right">
                                <Button size="icon" as-child>
                                    <Link :href="edit(artwork.id).url">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <ConfirmDelete
                                    :alert-phrase="`Etes-vous sûr de vouloir supprimer l'œuvre ${artwork.title} ?`"
                                    :delete-route="`/artworks/${artwork.id}`"
                                />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
