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
    transaction: Object,
});

const isShowNonMPFields = ref(false)
const isDP = ref(false)

if(props.transaction.source == 'shopee' || props.transaction.source == 'tokped' || props.transaction.source == 'etsy' || props.transaction.source == 'amazon' || props.transaction.source == 'ebay' || props.transaction.source == 'website'){
    isShowNonMPFields.value = false;
}else{
    isShowNonMPFields.value = true;
}

const emit = defineEmits(["close"]);

const form = useForm({
    payment_channel: "",
    payment_type: "",
    proof_of_payment: "",
    amount: "",
    price: "",
    description: "",
    date: "",
    transaction_id: props.transaction.id,
});

const create = () => {
    form.post(route("sale.store"), {
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
    if(event.target.value == 'dp'){
        isDP.value = true;
    }else{
        isDP.value = false;
    }
};

watchEffect(() => {
    if (props.show) {
        form.errors = {};
    }
});

const payment_channels = [
    {
        value : 'bca',
        label: 'BCA'
    },
    {
        value : 'mandiri',
        label: 'Mandiri'
    },
    {
        value : 'bni',
        label: 'BNI'
    },
    {
        value : 'jenius',
        label: 'Jenius'
    }
]

const payment_types = [
    {
        value : 'full_payment',
        label: 'Full Payment'
    },
    {
        value : 'dp',
        label: 'DP'
    },
    {
        value : 'pelunasan',
        label: 'Pelunasan'
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
                    <div v-if="isShowNonMPFields">
                        <InputLabel for="payment_channel" value="Payment Channel" />
                        <SelectInput
                            id="payment_channel"
                            class="mt-1 block w-full"
                            v-model="form.payment_channel"
                            required
                            :dataSet="payment_channels"
                        >
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.payment_channel" />
                    </div>
                    
                    <div v-if="isShowNonMPFields">
                        <InputLabel for="payment_type" value="Payment Type" />
                        <SelectInput
                            id="payment_type"
                            class="mt-1 block w-full"
                            v-model="form.payment_type"
                            required
                            :dataSet="payment_types"
                            @change="onChange($event)"
                        >
                        </SelectInput>
                        <InputError class="mt-2" :message="form.errors.payment_type" />
                    </div>

                    <div>
                        <InputLabel for="amount" value="Amount" />
                        <TextInput
                            id="amount"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.amount"
                            placeholder="ex: 100000"
                            :error="form.errors.amount"
                        />
                        <InputError class="mt-2" :message="form.errors.amount" />
                    </div>

                    <div v-if="isShowNonMPFields && isDP">
                        <InputLabel for="price" value="Full Price" />
                        <TextInput
                            id="price"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.price"
                            placeholder="ex: 200000"
                            :error="form.errors.price"
                        />
                        <InputError class="mt-2" :message="form.errors.price" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Description" />
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
                        <InputLabel for="date" value="Date" />
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
