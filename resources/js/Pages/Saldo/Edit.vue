<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { watchEffect, ref } from "vue";
import SelectInput from "@/Components/SelectInput.vue";

const props = defineProps({
    show: Boolean,
    title: String,
    transaction: Object,
    saldo: Object,
});

const emit = defineEmits(["close"]);

const form = useForm({
    amount: '',
    description: '',
    date: new Date().toISOString().substr(0, 10),
    type: "",
});

const update = () => {
    console.log(props.saldo)
    console.log(props.saldo?.id)
    form.amount = unformatNumber(form.amount);
    form.put(route("kas.update", props.saldo?.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
            form.reset();
        },
        onError: () => null,
        onFinish: () => null,
    });
};

watchEffect(() => {
    if (props.show) {
        var date = new Date(props.saldo?.date);
        date.setHours(date.getHours() + 8);

        form.errors = {};
        form.amount = props.saldo?.amount;
        form.description = props.saldo?.description;
        form.type = props.saldo?.type;
        form.date = date.toISOString().slice(0,10);
        form.errors = {};
    }
});

const sources = [
    {
        value : 'masuk',
        label: 'Uang Masuk'
    },
    {
        value : 'keluar',
        label: 'Uang Keluar'
    },
]

const formatNumber = (value) => {
    if (!value) return "";
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const unformatNumber = (value) => {
    if (!value || typeof value !== 'string') return value;
    return value.replace(/,/g, "");
};

const amountDisplay = ref("");

watchEffect(() => {
    amountDisplay.value = formatNumber(form.amount);
});
</script>

<template>
    <section class="space-y-6">
        <Modal :show="props.show" @close="emit('close')">
            <form class="p-6" @submit.prevent="update">
                <h2
                    class="text-lg font-medium text-slate-900 dark:text-slate-100"
                >
                    {{ lang().label.edit }} {{ props.title }}
                </h2>
                <div class="my-6 space-y-4">

                    <div>
                        <InputLabel for="amount" value="Amount" />
                        <TextInput
                            id="amount"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="amountDisplay"
                            @input="form.amount = unformatNumber($event.target.value)"
                            placeholder="ex: 100,000"
                            :error="form.errors.amount"
                        />
                        <InputError class="mt-2" :message="form.errors.amount" />
                    </div>

                    <div>
                        <InputLabel for="tipe" value="Tipe" />
                        <SelectInput
                            id="tipe"
                            class="mt-1 block w-full"
                            v-model="form.type"
                            required
                            :dataSet="sources"
                        >
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.type" />
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
                        @click="update"
                    >
                        {{
                            form.processing
                                ? lang().button.save + "..."
                                : lang().button.save
                        }}
                    </PrimaryButton>
                </div>
            </form>
        </Modal>
    </section>
</template>
