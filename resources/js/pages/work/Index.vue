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
import { work } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { DataPaginated, Work } from '@/types/data';
import { Head } from '@inertiajs/vue3';
import { Pencil, Trash } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Work',
        href: work().url,
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
                                    @click="console.log('Delete')"
                                >
                                    <Trash class="h-4 w-4" />
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
