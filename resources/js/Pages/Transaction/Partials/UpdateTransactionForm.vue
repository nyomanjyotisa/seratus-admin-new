<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    transaction: Object,    
});

const isShowUniqueCode = ref(false)

var date = new Date(props.transaction.date);
date.setHours(date.getHours() + 8);

if(props.transaction.source == 'shopee' || props.transaction.source == 'tokped' || props.transaction.source == 'etsy' || props.transaction.source == 'amazon' || props.transaction.source == 'ebay' || props.transaction.source == 'website'){
    isShowUniqueCode.value = true;
}else{
    isShowUniqueCode.value = false;
}


const form = useForm({
    unique_code: props.transaction.unique_code,
    description: props.transaction.description,
    status: props.transaction.status,
    source: props.transaction.source,
    date: date.toISOString().slice(0,10),
});

const statuses = [
    {
        value : 'pending',
        label: 'Pending'
    },
    {
        value : 'done',
        label: 'Done'
    }
]

const updateTransaction = () => {
    form.put(route("transaction.update", props.transaction), {
        preserveScroll: true,
    });
};

const sources = [
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
        value : 'shopee',
        label: 'Shopee'
    },
    {
        value : 'tokped',
        label: 'Tokopedia'
    },
    {
        value : 'etsy',
        label: 'Etsy'
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

const onChange = (event) => {
    if(event.target.value == 'shopee' || event.target.value == 'tokped' || event.target.value == 'etsy' || event.target.value == 'amazon' || event.target.value == 'ebay' || event.target.value == 'website'){
        isShowUniqueCode.value = true;
    }else{
        isShowUniqueCode.value = false;
    }
};

console.log(form.date)

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
                Edit Transaction Detail
            </h2>

            <!-- <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                Blablabla
            </p> -->
        </header>

        <form @submit.prevent="updateTransaction" class="mt-6 space-y-6">
            <div>
                <InputLabel for="source" value="Source" />
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
                    autocomplete="unique_code"
                    :error="form.errors.unique_code"
                />

                <InputError class="mt-2" :message="form.errors.unique_code" />
            </div>

            <div>
                <InputLabel for="description" value="Description" />

                <TextInput
                    id="description"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.description"
                    required
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

            <div>
                <InputLabel for="status" value="Status" />
                
                <SelectInput
                    id="status"
                    class="mt-1 block w-full"
                    v-model="form.status"
                    required
                    :dataSet="statuses"
                >
                </SelectInput>

                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? lang().button.save + "..."
                            : lang().button.save
                    }}
                </PrimaryButton>

                <Transition
                    enter-from-class="opacity-0"
                    leave-to-class="opacity-0"
                    class="transition ease-in-out"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-slate-600 dark:text-slate-400"
                    >
                        {{ lang().profile.saved }}
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
