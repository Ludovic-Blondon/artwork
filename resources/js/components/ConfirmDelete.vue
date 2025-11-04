<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/vue3';
import { Trash } from 'lucide-vue-next';

const props = defineProps<{
    alertPhrase: string;
    deleteRoute: string;
}>();

const handleDelete = () => {
    router.delete(props.deleteRoute);
};
</script>

<template>
    <AlertDialog>
        <AlertDialogTrigger>
            <Button variant="destructive" size="icon" class="h-7 w-7">
                <Trash class="h-2 w-2" />
            </Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Etes-vous absolument sûr</AlertDialogTitle>
                <AlertDialogDescription>
                    {{ alertPhrase }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Annuler</AlertDialogCancel>
                <AlertDialogAction @click="handleDelete">
                    Supprimer
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
