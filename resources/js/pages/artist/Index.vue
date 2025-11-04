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
import { create, edit, index } from '@/routes/artist';
import { type BreadcrumbItem } from '@/types';
import { Artist, DataPaginated } from '@/types/data';
import { Head, Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { Pencil, Plus } from 'lucide-vue-next';

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
</script>

<template>
    <Head title="Artist" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex justify-end">
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Créer un artiste
                    </Link>
                </Button>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <Table>
                    <TableCaption>Liste des artistes.</TableCaption>
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
                            <TableCell class="font-medium">
                                {{ artist.id }}
                            </TableCell>
                            <TableCell>
                                {{ artist.name }}
                            </TableCell>
                            <TableCell>{{
                                artist.bio
                                    ? artist.bio?.slice(0, 25) + ' ...'
                                    : 'N/A'
                            }}</TableCell>
                            <TableCell>{{
                                artist.birthDate
                                    ? dayjs(artist.birthDate).format(
                                          'DD/MM/YYYY',
                                      )
                                    : 'N/A'
                            }}</TableCell>
                            <TableCell>{{
                                artist.deathDate
                                    ? dayjs(artist.deathDate).format(
                                          'DD/MM/YYYY',
                                      )
                                    : 'N/A'
                            }}</TableCell>
                            <TableCell class="space-x-1 text-right">
                                <Button size="icon" as-child>
                                    <Link :href="edit(artist.id).url">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <ConfirmDelete
                                    :alert-phrase="`Etes-vous sûr de vouloir supprimer l'artiste ${artist.name} ?`"
                                    :delete-route="`/artists/${artist.id}`"
                                />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
