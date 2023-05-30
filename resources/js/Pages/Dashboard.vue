<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";
import VueAvatar from "@webzlodimir/vue-avatar";
import "@webzlodimir/vue-avatar/dist/style.css";

defineProps({
    rooms: {
        type: Array,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Xin chào {{ $page.props.auth.user.name }}!
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h2 class="text-2xl pb-5">Chọn nhóm để tham gia ...</h2>

                        <div class="grid grid-cols-5 gap-3">
                            <div v-for="(item, index) in rooms" :key="index">
                                <NavLink
                                    :href="route('rooms.show', item.id)"
                                    class="flex-col no-underline border-none"
                                >
                                    <span class="d-block">
                                        <!-- <vue-avatar
                                            :img-src="`https://robohash.org/${item.name}`"
                                            :img-alt="item.name"
                                            border-radius="100%"
                                            :size="80"
                                        /> -->
                                        <vue-avatar
                                            :username="item.name"
                                            border-radius="100%"
                                            :size="80"
                                        />
                                    </span>
                                    <span class="d-block">{{ item.name }}</span>
                                </NavLink>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
