<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { type Media } from '@/types/data';
import { X } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    existingImages?: Media[];
}

withDefaults(defineProps<Props>(), {
    existingImages: () => [],
});

const emit = defineEmits<{
    'update:newImages': [files: File[]];
    'update:deletedIds': [ids: number[]];
}>();

const fileInputRef = ref<HTMLInputElement | null>(null);
const newImages = ref<File[]>([]);
const previewUrls = ref<string[]>([]);
const deletedMediaIds = ref<number[]>([]);

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const files = Array.from(target.files);
        newImages.value.push(...files);

        // Créer les previews
        files.forEach((file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (e.target?.result) {
                    previewUrls.value.push(e.target.result as string);
                }
            };
            reader.readAsDataURL(file);
        });

        emit('update:newImages', newImages.value);
    }
};

const removeNewImage = (index: number) => {
    newImages.value.splice(index, 1);
    previewUrls.value.splice(index, 1);
    emit('update:newImages', newImages.value);
};

const markExistingForDeletion = (mediaId: number) => {
    deletedMediaIds.value.push(mediaId);
    emit('update:deletedIds', deletedMediaIds.value);
};

const isMarkedForDeletion = (mediaId: number) => {
    return deletedMediaIds.value.includes(mediaId);
};

const openFilePicker = () => {
    fileInputRef.value?.click();
};
</script>

<template>
    <div class="space-y-4">
        <!-- Images existantes -->
        <div
            v-if="existingImages.length > 0"
            class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4"
        >
            <div
                v-for="image in existingImages"
                :key="image.id"
                class="group relative aspect-square overflow-hidden rounded-lg border"
                :class="{
                    'opacity-50 grayscale': isMarkedForDeletion(image.id),
                }"
            >
                <img
                    :src="image.url"
                    :alt="image.name"
                    class="h-full w-full object-cover"
                />
                <Button
                    v-if="!isMarkedForDeletion(image.id)"
                    type="button"
                    size="icon"
                    variant="destructive"
                    class="absolute top-2 right-2 h-8 w-8 opacity-0 transition-opacity group-hover:opacity-100"
                    @click="markExistingForDeletion(image.id)"
                >
                    <X class="h-4 w-4" />
                </Button>
                <div
                    v-else
                    class="absolute inset-0 flex items-center justify-center bg-black/50 text-white"
                >
                    Sera supprimée
                </div>
            </div>
        </div>

        <!-- Nouvelles images (preview) -->
        <div
            v-if="previewUrls.length > 0"
            class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4"
        >
            <div
                v-for="(url, index) in previewUrls"
                :key="index"
                class="group relative aspect-square overflow-hidden rounded-lg border border-dashed"
            >
                <img
                    :src="url"
                    alt="Preview"
                    class="h-full w-full object-cover"
                />
                <Button
                    type="button"
                    size="icon"
                    variant="destructive"
                    class="absolute top-2 right-2 h-8 w-8 opacity-0 transition-opacity group-hover:opacity-100"
                    @click="removeNewImage(index)"
                >
                    <X class="h-4 w-4" />
                </Button>
            </div>
        </div>

        <!-- Bouton d'upload -->
        <div>
            <input
                ref="fileInputRef"
                type="file"
                name="images"
                multiple
                accept="image/jpeg,image/png,image/jpg,image/webp"
                class="hidden"
                @change="handleFileSelect"
            />
            <Button type="button" variant="outline" @click="openFilePicker">
                Ajouter des images
            </Button>
            <p class="mt-2 text-sm text-muted-foreground">
                JPG, PNG, WEBP jusqu'à 5MB
            </p>
        </div>
    </div>
</template>
