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
import { create, edit, index } from '@/routes/work';
import { type BreadcrumbItem } from '@/types';
import { DataPaginated, Work } from '@/types/data';
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, Plus } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Work',
        href: index().url,
    },
];

withDefaults(
    defineProps<{
        paginatedWorks: DataPaginated<Work>;
    }>(),
    {
        paginatedWorks: () => ({
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
    <Head title="Work" />

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
                            <TableHead class="w-[100px]">Id</TableHead>
                            <TableHead> Titre </TableHead>
                            <TableHead>description</TableHead>
                            <TableHead>Année</TableHead>
                            <TableHead> Autheur </TableHead>
                            <TableHead class="text-right"> Action </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="work in paginatedWorks.data"
                            :key="work.id"
                        >
                            <TableCell class="font-medium">
                                {{ work.id }}
                            </TableCell>
                            <TableCell>
                                {{ work.title }}
                            </TableCell>
                            <TableCell>{{
                                work.description?.slice(0, 25) + ' ...'
                            }}</TableCell>
                            <TableCell>{{ work.yearCreated }}</TableCell>
                            <TableCell>{{ work.artist.name }}</TableCell>
                            <TableCell class="space-x-1 text-right">
                                <Button size="icon" as-child>
                                    <Link :href="edit(work.id).url">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <ConfirmDelete
                                    :alert-phrase="`Etes-vous sûr de vouloir supprimer l'œuvre ${work.title} ?`"
                                    :delete-route="`/works/${work.id}`"
                                />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
