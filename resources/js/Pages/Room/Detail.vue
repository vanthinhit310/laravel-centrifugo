<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Centrifuge } from "centrifuge";
import { toRefs } from "vue";

const props = defineProps({
    room: {
        type: Object,
    },
    isJoin: {
        type: Boolean,
    },
    connectionToken: {
        type: String,
    },
});

const getToken = (url, ctx) => {
    return new Promise((resolve, reject) => {
        fetch(url, {
            method: "POST",
            headers: new Headers({ "Content-Type": "application/json" }),
            body: JSON.stringify(ctx),
        })
            .then((res) => {
                if (!res.ok) {
                    throw new Error(`Unexpected status code ${res.status}`);
                }
                return res.json();
            })
            .then((data) => {
                resolve(data.token);
            })
            .catch((err) => {
                reject(err);
            });
    });
};

const subscribeTokenEndpoint =
    "http://laravel-centrifugo.develop/broadcasting/auth";
const centrifuge = new Centrifuge("ws://localhost:8002/connection/websocket", {
    token: props.connectionToken,
    getToken: function (ctx) {
        return getToken("/centrifuge/connection_token", ctx);
    },
});

centrifuge
    .on("connecting", function (ctx) {
        console.log(`connecting: ${ctx.code}, ${ctx.reason}`);
    })
    .on("connected", function (ctx) {
        console.log(`connected over ${ctx.transport}`);
    })
    .on("disconnected", function (ctx) {
        console.log(`disconnected: ${ctx.code}, ${ctx.reason}`);
    })
    .connect();

// const sub = centrifuge.newSubscription("test:test", {
//     getToken: function (ctx) {
//         return customGetToken(subscribeTokenEndpoint, ctx);
//     },
// });

// sub.on("publication", function (ctx) {
//     container.innerHTML = ctx.data.value;
//     document.title = ctx.data.value;
// })
//     .on("subscribing", function (ctx) {
//         console.log(`subscribing: ${ctx.code}, ${ctx.reason}`);
//     })
//     .on("subscribed", function (ctx) {
//         console.log("subscribed", ctx);
//     })
//     .on("unsubscribed", function (ctx) {
//         console.log(`unsubscribed: ${ctx.code}, ${ctx.reason}`);
//     })
//     .subscribe();

const sendMessage = () => {
    console.log("Button clicked!");
};
</script>
<template>
    <Head :title="room.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chia sẻ kiến thức {{ room.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 text-gray-900 bg-gray-300">
                        <div
                            class="flex flex-col items-center justify-center w-full h-160 text-gray-800"
                        >
                            <!-- Component Start -->
                            <div
                                class="flex flex-col flex-grow w-full bg-white shadow-xl rounded-lg overflow-hidden"
                            >
                                <div
                                    class="flex flex-col flex-grow h-0 p-4 overflow-auto"
                                >
                                    <div
                                        class="flex w-full mt-2 space-x-3 max-w-xs"
                                    >
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300"
                                        ></div>
                                        <div>
                                            <div
                                                class="bg-gray-300 p-3 rounded-r-lg rounded-bl-lg"
                                            >
                                                <p class="text-sm">
                                                    Lorem ipsum dolor sit amet,
                                                    consectetur adipiscing elit.
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 leading-none"
                                                >2 min ago</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex w-full mt-2 space-x-3 max-w-xs ml-auto justify-end"
                                    >
                                        <div>
                                            <div
                                                class="bg-blue-600 text-white p-3 rounded-l-lg rounded-br-lg"
                                            >
                                                <p class="text-sm">
                                                    Lorem ipsum dolor sit amet,
                                                    consectetur adipiscing elit,
                                                    sed do eiusmod.
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 leading-none"
                                                >2 min ago</span
                                            >
                                        </div>
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="flex w-full mt-2 space-x-3 max-w-xs ml-auto justify-end"
                                    >
                                        <div>
                                            <div
                                                class="bg-blue-600 text-white p-3 rounded-l-lg rounded-br-lg"
                                            >
                                                <p class="text-sm">
                                                    Lorem ipsum dolor sit amet.
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 leading-none"
                                                >2 min ago</span
                                            >
                                        </div>
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="flex w-full mt-2 space-x-3 max-w-xs"
                                    >
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300"
                                        ></div>
                                        <div>
                                            <div
                                                class="bg-gray-300 p-3 rounded-r-lg rounded-bl-lg"
                                            >
                                                <p class="text-sm">
                                                    Lorem ipsum dolor sit amet,
                                                    consectetur adipiscing elit,
                                                    sed do eiusmod tempor
                                                    incididunt ut labore et
                                                    dolore magna aliqua.
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 leading-none"
                                                >2 min ago</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex w-full mt-2 space-x-3 max-w-xs ml-auto justify-end"
                                    >
                                        <div>
                                            <div
                                                class="bg-blue-600 text-white p-3 rounded-l-lg rounded-br-lg"
                                            >
                                                <p class="text-sm">
                                                    Lorem ipsum dolor sit amet,
                                                    consectetur adipiscing elit,
                                                    sed do eiusmod tempor
                                                    incididunt ut labore et
                                                    dolore magna aliqua.
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 leading-none"
                                                >2 min ago</span
                                            >
                                        </div>
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="flex w-full mt-2 space-x-3 max-w-xs ml-auto justify-end"
                                    >
                                        <div>
                                            <div
                                                class="bg-blue-600 text-white p-3 rounded-l-lg rounded-br-lg"
                                            >
                                                <p class="text-sm">
                                                    Lorem ipsum dolor sit amet,
                                                    consectetur adipiscing elit,
                                                    sed do eiusmod tempor
                                                    incididunt.
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 leading-none"
                                                >2 min ago</span
                                            >
                                        </div>
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="flex w-full mt-2 space-x-3 max-w-xs ml-auto justify-end"
                                    >
                                        <div>
                                            <div
                                                class="bg-blue-600 text-white p-3 rounded-l-lg rounded-br-lg"
                                            >
                                                <p class="text-sm">
                                                    Lorem ipsum dolor sit amet.
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 leading-none"
                                                >2 min ago</span
                                            >
                                        </div>
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300"
                                        ></div>
                                    </div>
                                    <div
                                        class="flex w-full mt-2 space-x-3 max-w-xs"
                                    >
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300"
                                        ></div>
                                        <div>
                                            <div
                                                class="bg-gray-300 p-3 rounded-r-lg rounded-bl-lg"
                                            >
                                                <p class="text-sm">
                                                    Lorem ipsum dolor sit amet,
                                                    consectetur adipiscing elit,
                                                    sed do eiusmod tempor
                                                    incididunt ut labore et
                                                    dolore magna aliqua.
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 leading-none"
                                                >2 min ago</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex w-full mt-2 space-x-3 max-w-xs ml-auto justify-end"
                                    >
                                        <div>
                                            <div
                                                class="bg-blue-600 text-white p-3 rounded-l-lg rounded-br-lg"
                                            >
                                                <p class="text-sm">
                                                    Lorem ipsum dolor sit.
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs text-gray-500 leading-none"
                                                >2 min ago</span
                                            >
                                        </div>
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300"
                                        ></div>
                                    </div>
                                </div>

                                <div class="bg-gray-200 p-4">
                                    <div class="relative flex items-center">
                                        <input
                                            placeholder="Viết gì đó nào…"
                                            type="text"
                                            class="peer text-sm relative h-10 w-full rounded-md bg-gray-50 pl-4 pr-20 font-thin outline-none drop-shadow-sm transition-all duration-200 ease-in-out focus:bg-white focus:drop-shadow-lg"
                                        />
                                        <button
                                            @click="sendMessage"
                                            class="absolute right-2 h-7 w-16 rounded-md bg-blue-600 text-xs font-semibold text-white transition-all duration-200 ease-in-out group-focus-within:bg-blue-600 group-focus-within:hover:bg-blue-600"
                                        >
                                            Gửi
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Component End  -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
