<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { onUnmounted, ref, onMounted, watch } from 'vue'
import VueAvatar from '@webzlodimir/vue-avatar'
import '@webzlodimir/vue-avatar/dist/style.css'

const page = usePage()
const props = defineProps({
    room: {
        type: Object,
    },
    isJoin: {
        type: Boolean,
    },
})
const messageInput = ref('')
const messages = ref([])
const chatContainer = ref(null)
const typing = ref(false)
const typingUser = ref(null)

if (props.room.messages && props.room.messages.length) {
    let messageList = []
    props.room.messages.forEach((element) => {
        messageList.push({
            createdAt: element.createdAt,
            roomId: element.roomId,
            senderId: element.senderId,
            senderName: element.senderName,
            text: element.text,
        })
    })

    messages.value = messageList
}
const debounce = (func, timeout = 300) => {
    let timer
    return (...args) => {
        clearTimeout(timer)
        timer = setTimeout(() => {
            func.apply(this, args)
        }, timeout)
    }
}

const customGetToken = (endpoint, ctx) => {
    return new Promise((resolve, reject) => {
        fetch(endpoint, {
            method: 'POST',
            headers: new Headers({ 'Content-Type': 'application/json' }),
            body: JSON.stringify(ctx),
        })
            .then((res) => {
                if (!res.ok) {
                    throw new Error(`Unexpected status code ${res.status}`)
                }
                return res.json()
            })
            .then((data) => {
                resolve(data.token)
            })
            .catch((err) => {
                reject(err)
            })
    })
}

const scrollToLastMessage = () => {
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight
    }
}

const sub = CENTRIFUGE_INSTANCE.newSubscription(`presencezone:chatroom.${page.props.room?.id}`, {
    debug: true,
    getToken: function (ctx) {
        return customGetToken(route('centrifuge.genSubscriptionToken'), {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            channel_name: `presencezone:chatroom.${page.props.room?.id}`,
        })
    },
})

sub.on('publication', function (ctx) {
    const { event } = ctx.data

    if (event && event === 'message.new') {
        const message = ctx.data
        messages.value.push(message)
    }

    if (event && event === 'typing') {
        const { status, user } = ctx.data
        typing.value = status
        if (user) {
            typingUser.value = user
        }
    }
})
    .on('join', function (ctx) {
        console.log(`join`)
        console.log({ ctx })
    })
    .on('leave', function (ctx) {
        console.log(`leave`)
        console.log({ ctx })
    })
    .on('subscribing', function (ctx) {
        console.log(`subscribing: ${ctx.code}, ${ctx.reason}`)
    })
    .on('subscribed', function (ctx) {
        console.log('subscribed', ctx)
    })
    .on('unsubscribed', function (ctx) {
        console.log(`unsubscribed: ${ctx.code}, ${ctx.reason}`)
    })
    .on('error', function (ctx) {
        console.log('subscription error', ctx)
    })
    .subscribe()

const sendMessage = async () => {
    if (messageInput.value) {
        try {
            const formData = {
                message: messageInput.value,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
            await fetch(route('rooms.publish', props.room?.id), {
                method: 'POST',
                headers: new Headers({
                    'Content-Type': 'application/json',
                }),
                body: JSON.stringify(formData),
            })
        } catch (error) {}
    }
    messageInput.value = ''
}

const startTyping = () => {
    typing.value = true
    sub.publish({ status: typing.value, event: 'typing', user: page.props.auth.user }).then(
        function (ctx) {},
        function (err) {}
    )
    debounceStopTyping()
}
const debounceStopTyping = debounce(function () {
    typing.value = false
    sub.publish({ status: typing.value, event: 'typing', user: page.props.auth.user }).then(
        function (ctx) {},
        function (err) {}
    )
}, 1200)

onMounted(() => {
    scrollToLastMessage()
})

watch(
    () => messages.value.length,
    () => {
        scrollToLastMessage()
    }
)

onUnmounted(() => {
    CENTRIFUGE_INSTANCE.removeSubscription(sub)
})
</script>
<template>
    <Head :title="room.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Chia sẻ kiến thức {{ room.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 text-gray-900 bg-gray-300">
                        <div class="flex flex-col items-center justify-center w-full text-gray-800">
                            <!-- Component Start -->
                            <div class="flex flex-col flex-grow w-full bg-white shadow-xl rounded-lg overflow-hidden">
                                <div class="flex flex-col flex-grow h-160 py-5 px-5 overflow-auto justify-end" id="chatContainer" ref="chatContainer">
                                    <template v-for="(item, index) in messages" :key="index">
                                        <template v-if="parseInt(item.senderId) === parseInt($page.props.auth.user.id)">
                                            <div class="flex w-full pb-3 space-x-3 max-w-md ml-auto justify-end" :key="index">
                                                <div>
                                                    <div class="bg-blue-600 text-white px-3 py-2 rounded-l-lg rounded-br-lg">
                                                        <p class="text-sm">
                                                            {{ item.text }}
                                                        </p>
                                                    </div>
                                                    <span class="text-xs text-gray-500 leading-none">{{ item.createdAt }}</span>
                                                </div>
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300">
                                                    <vue-avatar
                                                        :img-src="`https://robohash.org/${item.senderName}`"
                                                        :img-alt="item.senderName"
                                                        border-radius="100%"
                                                        :size="40" />
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="flex w-full pb-3 space-x-3 max-w-md" :key="index">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300">
                                                    <vue-avatar
                                                        :img-src="`https://robohash.org/${item.senderName}`"
                                                        :img-alt="item.senderName"
                                                        border-radius="100%"
                                                        :size="40" />
                                                </div>
                                                <div>
                                                    <div class="bg-gray-300 px-3 py-2 rounded-r-lg rounded-bl-lg">
                                                        <p class="text-sm">{{ item.text }}</p>
                                                    </div>
                                                    <span class="text-xs text-gray-500 leading-none">{{ item.createdAt }}</span>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                </div>

                                <div class="bg-gray-200 p-4">
                                    <div
                                        class="text-xs italic font-light text-lime-400"
                                        v-if="typing && parseInt(typingUser.id) !== parseInt($page.props.auth.user.id)">
                                        {{ typingUser.name }} đang soạn tin...
                                    </div>
                                    <div class="relative flex items-center">
                                        <input
                                            @keyup.enter="sendMessage"
                                            @input="startTyping"
                                            v-model="messageInput"
                                            placeholder="Viết gì đó nào…"
                                            type="text"
                                            class="peer text-sm relative h-10 w-full rounded-md bg-gray-50 pl-4 pr-20 font-thin outline-none drop-shadow-sm transition-all duration-200 ease-in-out focus:bg-white focus:drop-shadow-lg" />
                                        <button
                                            @click="sendMessage"
                                            class="absolute right-2 h-7 w-16 rounded-md bg-blue-600 text-xs font-semibold text-white transition-all duration-200 ease-in-out group-focus-within:bg-blue-600 group-focus-within:hover:bg-blue-600">
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
