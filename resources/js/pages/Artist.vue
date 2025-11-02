<script setup lang="ts">
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
import { index } from '@/routes/artist';
import { type BreadcrumbItem } from '@/types';
import { Artist, DataPaginated } from '@/types/data';
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, Trash } from 'lucide-vue-next';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Artist',
        href: index().url,
    },
];

withDefaults(
    defineProps<{
        paginatedArtists: DataPaginated<Artist>;
    }>(),
    {
        paginatedArtists: () => ({
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

const confirmDelete = (artistName: string) => {
    return window.confirm(
        `Êtes-vous sûr de vouloir supprimer l'artiste ${artistName} ?`,
    );
};
</script>

<template>
    <Head title="Artist" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <Table>
                    <TableCaption>Liste des oeuvres.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]">Id</TableHead>
                            <TableHead>Nom</TableHead>
                            <TableHead>Bio</TableHead>
                            <TableHead>Date de naissance</TableHead>
                            <TableHead>Date de décès</TableHead>
                            <TableHead class="text-right"> Action </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="artist in paginatedArtists.data"
                            :key="artist.id"
                        >
                            {{ console.log(artist) }}
                            <TableCell class="font-medium">
                                {{ artist.id }}
                            </TableCell>
                            <TableCell>
                                {{ artist.name }}
                            </TableCell>
                            <TableCell>{{
                                artist.bio?.slice(0, 25) + ' ...'
                            }}</TableCell>
                            <TableCell>{{ artist.birthDate }}</TableCell>
                            <TableCell>{{ artist.deathDate }}</TableCell>
                            <TableCell class="text-right">
                                <Button
                                    size="icon"
                                    @click="console.log('Edit')"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="icon"
                                    as-child
                                >
                                    <Link
                                        :href="`/artists/${artist.id}`"
                                        method="delete"
                                        :onBefore="
                                            () => confirmDelete(artist.name)
                                        "
                                    >
                                        <Trash class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
