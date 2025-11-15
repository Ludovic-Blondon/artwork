<script setup lang="ts">
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Artwork } from '@/types/data';
import { Link } from '@inertiajs/vue3';

defineProps<{
    artwork: Artwork;
}>();
</script>

<template>
    <Link :href="`/artworks/${artwork.id}`">
        <Card class="w-full min-[800px]:w-[350px] transition-all hover:shadow-lg hover:scale-[1.02]">
            <CardHeader>
                <CardTitle>{{ artwork.title }}</CardTitle>
            </CardHeader>
            <CardContent>
                <img
                    :src="artwork.featuredImage ?? undefined"
                    :alt="artwork.title"
                    class="h-full w-full object-cover"
                />
            </CardContent>
            <CardFooter class="flex justify-between">
                <div>
                    <strong>{{ artwork.artist.name }}</strong>
                </div>
                <div>
                    <strong>{{ artwork.yearCreated }}</strong>
                </div>
            </CardFooter>
        </Card>
    </Link>
</template>
