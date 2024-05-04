<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { watchEffect, ref } from "vue";

const props = defineProps({
    show: Boolean,
    title: String,
    roles: Object,
});

const isShowUniqueCode = ref(false)

const emit = defineEmits(["close"]);

const form = useForm({
    unique_code: "",
    description: "",
    source: "",
    date: new Date().toISOString().substr(0, 10),
});

const create = () => {
    form.post(route("transaction.store"), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};

const onChange = (event) => {
    if(event.target.value == 'shopee' || event.target.value == 'tokped' || event.target.value == 'etsy' || event.target.value == 'amazon' || event.target.value == 'ebay' || event.target.value == 'website'){
        isShowUniqueCode.value = true;
    }else{
        isShowUniqueCode.value = false;
    }
};

watchEffect(() => {
    if (props.show) {
        form.errors = {};
    }
});

const roles = props.roles?.map((role) => ({
    label: role.name,
    value: role.name,
}));




const sources = [
    {
        value : 'etsy',
        label: 'Etsy'
    },
    {
        value : 'tokped',
        label: 'Tokopedia'
    },
    {
        value : 'shopee',
        label: 'Shopee'
    },
    {
        value : 'novica',
        label: 'Novica'
    },
    {
        value : 'wa',
        label: 'WhatsApp'
    },
    {
        value : 'instagram',
        label: 'Instagram'
    },
    {
        value : 'facebook',
        label: 'Facebook'
    },
    {
        value : 'email',
        label: 'Email'
    },
    {
        value : 'amazon',
        label: 'Amazon'
    },
    {
        value : 'ebay',
        label: 'Ebay'
    },
    {
        value : 'website',
        label: 'Website'
    }
]

</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="create">
                <h2
                    class="text-lg font-medium text-slate-900 dark:text-slate-100"
                >
                    {{ lang().label.add }} {{ props.title }}
                </h2>
                <div class="my-6 space-y-4">
                    <div>
                        <InputLabel for="source" value="Sumber" />
                        <SelectInput
                            id="source"
                            class="mt-1 block w-full"
                            v-model="form.source"
                            required
                            :dataSet="sources"
                            @change="onChange($event)"
                        >
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.source" />
                    </div>

                    <div v-if="isShowUniqueCode">
                        <InputLabel for="unique_code" value="Unique Code" />
                        <TextInput
                            id="unique_code"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.unique_code"
                            required
                            placeholder="ex: ASDASD1239888"
                            :error="form.errors.unique_code"
                        />
                        <InputError class="mt-2" :message="form.errors.unique_code" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Deskripsi" />
                        <TextInput
                            id="description"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.description"
                            placeholder="ex: Pandil Rama Sita 50 cm"
                            :error="form.errors.description"
                        />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div>
                        <InputLabel for="date" value="Tanggal" />
                        <TextInput
                            id="date"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.date"
                            placeholder="ex: Pandil Rama Sita 50 cm"
                            :error="form.errors.date"
                        />
                        <InputError class="mt-2" :message="form.errors.date" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <SecondaryButton
                        :disabled="form.processing"
                        @click="emit('close')"
                    >
                        {{ lang().button.close }}
                    </SecondaryButton>
                    <PrimaryButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="create"
                    >
                        {{
                            form.processing
                                ? lang().button.add + "..."
                                : lang().button.add
                        }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
