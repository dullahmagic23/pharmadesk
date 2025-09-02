<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { router } from '@inertiajs/vue3';

const checkLicense = () => {
    router.get(route('license.check'));
}
</script>

<template>
    <div class="container p-6">
        <div class="flex justify-center items-center flex-col h-screen p-16">
            <p class="text-3xl text-gray-500">Connect to the internet then click the button below</p>
            <Button as="a" :href="route('license.check')" size="lg" variant="default" class="w-full">
                check your Licenses
            </Button>
        </div>
    </div>
</template>

<style scoped></style>
