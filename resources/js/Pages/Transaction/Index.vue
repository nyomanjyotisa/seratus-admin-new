<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InfoButton from "@/Components/InfoButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import { reactive, watch, ref, onMounted } from "vue";
import DangerButton from "@/Components/DangerButton.vue";
import pkg from "lodash";
import { router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import {
    CheckBadgeIcon,
    ChevronUpDownIcon,
    EyeIcon,
    TrashIcon,
} from "@heroicons/vue/24/solid";
import Create from "@/Pages/Transaction/Create.vue";
import Delete from "@/Pages/Transaction/Delete.vue";
import DeleteBulk from "@/Pages/Transaction/DeleteBulk.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const { _, debounce, pickBy } = pkg;
const searchInput = ref(null);
const props = defineProps({
    title: String,
    filters: Object,
    transactions: Object,
    roles: Object,
    breadcrumbs: Object,
    perPage: Number,
    isDone: Boolean,
});
const data = reactive({
    params: {
        search: props.filters.search,
        field: props.filters.field,
        order: props.filters.order,
        perPage: props.perPage,
    },
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    transaction: null,
    dataSet: usePage().props.app.perpage,
});

const order = (field) => {
    data.params.field = field;
    data.params.order = data.params.order === "asc" ? "desc" : "asc";
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        if(props.title == 'Transaksi list Done'){
            router.get(route("transaction.index.done"), params, {
                replace: true,
                preserveState: true,
                preserveScroll: true,
            });
        }else{
            router.get(route("transaction.index"), params, {
                replace: true,
                preserveState: true,
                preserveScroll: true,
            });
        }
    }, 150)
);

const selectAll = (event) => {
    if (event.target.checked === false) {
        data.selectedId = [];
    } else {
        props.transactions?.data.forEach((transaction) => {
            data.selectedId.push(transaction.id);
        });
    }
};
const select = () => {
    if (props.transactions?.data.length == data.selectedId.length) {
        data.multipleSelect = true;
    } else {
        data.multipleSelect = false;
    }
};
const goToTransaction = (id) => {
    window.location.href = '/transaction/' + id;
}

onMounted(() => {
    if (searchInput.value) {
        searchInput.value.focus();
    }
});
</script>

<template>
    <Head :title="props.title" />

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton
                        class="rounded"
                        @click="data.createOpen = true"
                        v-if="props.isDone == false"
                    >
                        {{ lang().button.add }}
                    </PrimaryButton>
                    <Link
                        :href="route('transaction.index.done')"
                    >
                        <SecondaryButton
                            class="rounded ml-3"
                            v-if="props.isDone == false"
                        >
                            Lihat Transaksi Selesai
                        </SecondaryButton>
                    </Link>
                    <Create
                        :show="data.createOpen"
                        @close="data.createOpen = false"
                        :roles="props.roles"
                        :title="props.title"
                    />
                    <Delete
                        :show="data.deleteOpen"
                        @close="data.deleteOpen = false"
                        :transaction="data.transaction"
                        :title="props.title"
                    />
                    <DeleteBulk
                        :show="data.deleteBulkOpen"
                        @close="
                            (data.deleteBulkOpen = false),
                                (data.multipleSelect = false),
                                (data.selectedId = [])
                        "
                        :selectedId="data.selectedId"
                        :title="props.title"
                    />
                </div>
            </div>
            <div
                class="relative bg-white dark:bg-slate-800 shadow sm:rounded-lg"
            >
                <div class="flex justify-between p-2">
                    <div class="flex space-x-2">
                        <SelectInput
                            v-model="data.params.perPage"
                            :dataSet="data.dataSet"
                        />
                        <DangerButton
                            @click="data.deleteBulkOpen = true"
                            v-show="
                                data.selectedId.length != 0
                            "
                            class="px-3 py-1.5"
                            v-tooltip="lang().tooltip.delete_selected"
                        >
                            <TrashIcon class="w-5 h-5" />
                        </DangerButton>
                    </div>
                    <TextInput
                        v-model="data.params.search"
                        type="text"
                        ref="searchInput"
                        class="ml-3 block w-full rounded-lg"
                        :placeholder="lang().placeholder.search"
                    />
                </div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead
                            class="uppercase text-sm border-t border-slate-200 dark:border-slate-700"
                        >
                            <tr class="dark:bg-slate-900/50 text-left">
                                <!-- <th class="px-2 py-4 text-center">
                                    <Checkbox
                                        v-model:checked="data.multipleSelect"
                                        @change="selectAll"
                                    />
                                </th> -->
                                <th class="px-2 py-4 text-center">#</th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                    v-on:click="order('unique_code')"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Unique Code</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                    v-on:click="order('status')"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Status</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                    v-on:click="order('status')"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Laba</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                    v-on:click="order('source')"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Sumber</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                    v-on:click="order('date')"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Tanggal</span>
                                        <ChevronUpDownIcon class="w-4 h-4" />
                                    </div>
                                </th>
                                <th class="px-2 py-4 sr-only">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tr
                                v-for="(transaction, index) in transactions.data"
                                :key="index"
                                class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20"
                            >
                                <!-- <td
                                    class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"
                                >
                                    <input
                                        class="rounded dark:bg-slate-900 border-slate-300 dark:border-slate-700 text-primary dark:text-primary shadow-sm focus:ring-primary/80 dark:focus:ring-primary dark:focus:ring-offset-slate-800 dark:checked:bg-primary dark:checked:border-primary"
                                        type="checkbox"
                                        @change="select"
                                        :value="transaction.id"
                                        v-model="data.selectedId"
                                    />
                                </td> -->
                                <td
                                    class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"
                                >
                                    {{ ++index }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <p> {{ transaction.unique_code ?? '-' }} </p>
                                    <p style="white-space: normal; word-break: break-all; display: block;"> {{ transaction.description ?? '-' }} </p>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <p v-if="transaction.sales_count > 0" class="text-green-600">
                                        Penjualan ({{ transaction.sales_count }}) - Rp{{ transaction.sales_total.toLocaleString() }}
                                    </p>
                                    <p v-else class="text-red-600">
                                        Penjualan (0)
                                    </p>
                                    
                                    <p v-if="transaction.productions_count > 0" class="text-green-600">
                                        Produksi ({{ transaction.productions_count }}) - Rp{{ transaction.productions_total.toLocaleString() }}
                                    </p>
                                    <p v-else class="text-red-600">
                                        Produksi (0)
                                    </p>
                                    
                                    
                                    <p v-if="transaction.source != 'tokped'" class="text-yellow-600">
                                        Pengeluaran lain (0)
                                    </p>
                                    <p v-else :class="(transaction.source == 'tokped' && transaction.expenses_count == 0) ? 'text-red-600' : 'text-green-600'">
                                        Pengeluaran lain ({{ transaction.expenses_count }}) - Rp{{ transaction.expenses_total.toLocaleString() }}
                                    </p>
                                    
                                    <p v-if="transaction.other_incomes_count > 0" class="text-green-600">
                                        Pemasukan lain ({{ transaction.other_incomes_count }}) - Rp{{ transaction.other_incomes_total.toLocaleString() }}
                                    </p>
                                    <p v-else class="text-yellow-600">
                                        Pemasukan lain (0)
                                    </p>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <p> <strong> Rp{{ transaction.total.toLocaleString() ?? '-' }} </strong> </p>
                                    <p :class="(transaction.persentase_laba > 60 || transaction.persentase_laba < 30 )  ? 'text-red-600' : ''"> {{ transaction.persentase_laba ?? '-' }} % </p>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ transaction.source }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ transaction.date }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div
                                        class="flex justify-center items-center"
                                    >
                                        <div class="rounded-md overflow-hidden">
                                            <Link
                                                :href="route('transaction.show', transaction)"
                                            >
                                            <InfoButton
                                                type="button"
                                                class="px-2 py-1.5 rounded-none"
                                                v-tooltip="lang().tooltip.edit"
                                            >
                                                <EyeIcon class="w-4 h-4" />
                                            </InfoButton>
                                            </Link>
                                            
                                            <DangerButton
                                                v-if="props.isDone == false"
                                                type="button"
                                                @click="
                                                    (data.deleteOpen = true),
                                                        (data.transaction = transaction)
                                                "
                                                class="px-2 py-1.5 rounded-none"
                                                v-tooltip="
                                                    lang().tooltip.delete
                                                "
                                            >
                                                <TrashIcon class="w-4 h-4" />
                                            </DangerButton>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    class="flex justify-between items-center p-2 border-t border-slate-200 dark:border-slate-700"
                >
                    <Pagination :links="props.transactions" :filters="data.params" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
