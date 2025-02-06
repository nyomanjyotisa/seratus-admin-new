<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InfoButton from "@/Components/InfoButton.vue";
import SelectInput from "@/Components/SelectInput.vue";
import { reactive, watch, onMounted, computed } from "vue";
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
import { Link, useForm } from "@inertiajs/vue3";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

const { _, debounce, pickBy } = pkg;
const props = defineProps({
    title: String,
    filters: Object,
    transactions: Object,
    expenses: Object,
    otherIncomes: Object,
    roles: Object,
    breadcrumbs: Object,
    perPage: Number,
    isDone: Boolean,
    year: String,
    month: String,
    total_pemasukan: Number,
    total_pengeluaran: Number,
    laba: Number,
    persentase_laba_total: Number,
    disabled: {
        type: Boolean,
        default: false,
    },
    saldo: Number,
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
    labaButtonClicked: false,
    bagiHasilButtonDisabled: true,
    showModal: false,
    currentAction: null,
    modalTitle: '',
    modalMessage: '',
});

const downloadReport = () => {
    const year = props.year;
    const month = props.month;
    window.open(`/report/${year}/${month}/download`, '_blank');
};

const form = useForm({
    year: props.year,
    month: props.month,
});

const order = (field) => {
    data.params.field = field;
    data.params.order = data.params.order === "asc" ? "desc" : "asc";
};

watch(
    () => _.cloneDeep(data.params),
    debounce(() => {
        let params = pickBy(data.params);
        router.get(route("transaction.index"), params, {
            replace: true,
            preserveState: true,
            preserveScroll: true,
        });
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


const applyFilter = () => {
    router.get(route("report",  { year: form.year, month: form.month}));
};

const years = [
    {
        value : '2022',
        label: '2022'
    },
    {
        value : '2023',
        label: '2023'
    },
    {
        value : '2024',
        label: '2024'
    },
    {
        value : '2025',
        label: '2025'
    }
]

const months = [
    {
        value : '1',
        label: 'Januari'
    },
    {
        value : '2',
        label: 'Februari'
    },
    {
        value : '3',
        label: 'Maret'
    },
    {
        value : '4',
        label: 'April'
    },
    {
        value : '5',
        label: 'Mei'
    },
    {
        value : '6',
        label: 'Juni'
    },
    {
        value : '7',
        label: 'Juli'
    },
    {
        value : '8',
        label: 'Agustus'
    },
    {
        value : '9',
        label: 'September'
    },
    {
        value : '10',
        label: 'Oktober'
    },
    {
        value : '11',
        label: 'November'
    },
    {
        value : '12',
        label: 'Desember'
    }
]

const getMonthName = (monthNumber) => {
    const month = months.find(m => m.value === monthNumber);
    return month ? month.label : '';
};

const isDateRestricted = computed(() => {
    const currentYear = parseInt(props.year);
    const currentMonth = parseInt(props.month);
    return currentYear < 2024 || (currentYear === 2024 && currentMonth <= 5);
});

// Load button states from localStorage
const loadButtonStates = () => {
    if (isDateRestricted.value) {
        data.labaButtonClicked = true;
        data.bagiHasilButtonDisabled = true;
        return;
    }

    const labaState = localStorage.getItem(`laba-${props.year}-${props.month}`);
    if (labaState === 'clicked') {
        data.labaButtonClicked = true;
        data.bagiHasilButtonDisabled = false;
    }

    const bagiHasilState = localStorage.getItem(`bagiHasil-${props.year}-${props.month}`);
    if (bagiHasilState === 'clicked') {
        data.bagiHasilButtonDisabled = true;
    }
};

// Run this on component mount to load the previous states
onMounted(() => {
    loadButtonStates();
});

const formLaba = useForm({
    laba: props.laba,
    type: 'masuk',
    description: `Laba ${getMonthName(props.month)} ${props.year}`,
    date: '',
});

const formBagiHasil = useForm({
    laba: (() => {
         const maxSaldo = parseInt(localStorage.getItem('maksimalSaldoKas') || '50000000', 10);
         const newSaldo = props.saldo;
         const defaultBagiHasil = Math.ceil((props.laba * 2 / 3) / 100000) * 100000;

         return newSaldo - defaultBagiHasil >= maxSaldo 
             ? Math.floor((newSaldo - maxSaldo) / 100000) * 100000 
             : defaultBagiHasil;
    })(),
    type: 'keluar',
    description: `Bagi Hasil ${getMonthName(props.month)} ${props.year}`,
    date: '',
});

const showConfirmation = (action) => {
    data.showModal = true;
    data.currentAction = action;

    if (action === 'addProfit') {
        data.modalTitle = 'Konfirmasi Tambah Laba ke Kas';
        data.modalMessage = 'Anda yakin ingin menambahkan laba ke kas?';
    } else if (action === 'addBagiHasil') {
        data.modalTitle = 'Konfirmasi Proses Bagi Hasil';
        data.modalMessage = 'Anda yakin ingin memproses bagi hasil?';
    }
};

const handleConfirm = () => {
    data.showModal = false;
    if (data.currentAction === 'addProfit') {
        addProfitToKas();
    } else if (data.currentAction === 'addBagiHasil') {
        addBagiHasil();
    }
};

const handleCancel = () => {
    data.showModal = false;
};

const addProfitToKas = () => {
    if (isDateRestricted.value || data.labaButtonClicked) return;

    formLaba.post(route("kas.tambahKas", { type: 'masuk' }), {
        onSuccess: () => {
            data.labaButtonClicked = true;
            data.bagiHasilButtonDisabled = false;  // Enable Bagi Hasil button

            // Save the state in localStorage
            localStorage.setItem(`laba-${props.year}-${props.month}`, 'clicked');
            console.log('Laba berhasil ditambahkan ke kas');

            window.location.reload();
        },
        onError: () => {
            console.error('Error menambahkan laba ke kas');
        },
    });
};

const addBagiHasil = () => {
    if (isDateRestricted.value || data.bagiHasilButtonDisabled) return;

    formBagiHasil.post(route("kas.tambahKas", { type: 'keluar' }), {
        onSuccess: () => {
            data.bagiHasilButtonDisabled = true;  // Disable after clicking

            // Save the state in localStorage
            localStorage.setItem(`bagiHasil-${props.year}-${props.month}`, 'clicked');
            console.log('Bagi hasil berhasil ditambahkan ke kas');

            window.location.reload();
        },
        onError: () => {
            console.error('Error menambahkan bagi hasil ke kas');
        },
    });
};
</script>

<template>
    <Head :title="props.title" />

    <AuthenticatedLayout>
        <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <SelectInput
                        id="status"
                        class="mt-1"
                        v-model="form.year"
                        required
                        :dataSet="years"
                        @change="applyFilter"
                    >
                    </SelectInput>
                    <SelectInput
                        id="status"
                        class="mt-1 ml-3"
                        v-model="form.month"
                        required
                        :dataSet="months"
                        @change="applyFilter"
                    >
                    </SelectInput>
                    <!-- <Link
                        :href="route('report', { year: form.year, month: form.month})"
                    >
                        <PrimaryButton
                            class="rounded ml-3"
                        >
                            Terapkan
                        </PrimaryButton>
                    </Link> -->
                    <!-- <Link
                        :href="route('report.download', { year: form.year, month: form.month})"
                    >
                        <PrimaryButton
                            class="rounded ml-3"
                        >
                            Download Laporan
                        </PrimaryButton>
                    </Link> -->
                    <div class="my-4">
                        <PrimaryButton @click="downloadReport">Download Report as PDF</PrimaryButton>
                    </div>
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
            <table>
                <tbody>
                    <tr
                        class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20"
                    >
                        <td
                            class="whitespace-nowrap py-4 px-2 sm:py-3"
                        >
                            Total Pemasukan
                        </td>
                        <td
                            class="whitespace-nowrap py-4 px-2 sm:py-3"
                        >
                            <strong> Rp{{total_pemasukan.toLocaleString()}} </strong>
                        </td>
                    </tr>
                    <tr
                        class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20"
                    >
                        <td
                            class="whitespace-nowrap py-4 px-2 sm:py-3"
                        >
                            Total Pengeluaran
                        </td>
                        <td
                            class="whitespace-nowrap py-4 px-2 sm:py-3"
                        >
                            <strong> Rp{{total_pengeluaran.toLocaleString()}} </strong>
                        </td>
                    </tr>
                    <tr
                        class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20"
                    >
                        <td
                            class="whitespace-nowrap py-4 px-2 sm:py-3"
                        >
                            Laba
                        </td>
                        <td
                            class="whitespace-nowrap py-4 px-2 sm:py-3"
                        >
                            <strong> Rp{{laba.toLocaleString()}} </strong>
                        </td>
                    </tr>
                    <tr
                        class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20"
                    >
                        <td
                            class="whitespace-nowrap py-4 px-2 sm:py-3"
                        >
                            Persentase Laba
                        </td>
                        <td
                            class="whitespace-nowrap py-4 px-2 sm:py-3"
                        >
                            <strong> {{persentase_laba_total.toFixed(2)}}% </strong>
                        </td>
                    </tr>
                </tbody>
            </table>
            <PrimaryButton :disabled="data.labaButtonClicked || isDateRestricted" @click="showConfirmation('addProfit')">
                Tambahkan Laba ke Kas
            </PrimaryButton>
            <br>
            <PrimaryButton :disabled="data.bagiHasilButtonDisabled || isDateRestricted" @click="showConfirmation('addBagiHasil')">
                Proses Bagi Hasil
            </PrimaryButton>
            <ConfirmationModal
                v-if="data.showModal"
                :show="data.showModal"
                :title="data.modalTitle"
                :message="data.modalMessage"
                okText="YA"
                cancelText="TIDAK"
                @confirm="handleConfirm"
                @cancel="handleCancel"
            />
            <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
                Transaksi
            </h2>
            <div
                class="relative bg-white dark:bg-slate-800 shadow sm:rounded-lg"
            >
                <!-- <div class="flex justify-between p-2">
                    <TextInput
                        v-model="data.params.search"
                        type="text"
                        class="ml-3 block w-full rounded-lg"
                        :placeholder="lang().placeholder.search"
                    />
                </div> -->
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead
                            class="uppercase text-sm border-t border-slate-200 dark:border-slate-700"
                        >
                            <tr class="dark:bg-slate-900/50 text-left">
                                <th class="px-2 py-4 text-center">#</th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Unique Code</span>
                                        <!-- <ChevronUpDownIcon class="w-4 h-4" /> -->
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Status</span>
                                        <!-- <ChevronUpDownIcon class="w-4 h-4" /> -->
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Laba</span>
                                        <!-- <ChevronUpDownIcon class="w-4 h-4" /> -->
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Sumber</span>
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Tanggal</span>
                                        <!-- <ChevronUpDownIcon class="w-4 h-4" /> -->
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
                                    
                                    <p v-if="transaction.expenses_count > 0" class="text-green-600">
                                        Pengeluaran lain ({{ transaction.expenses_count }}) - Rp{{ transaction.expenses_total.toLocaleString() }}
                                    </p>
                                    <p v-else class="text-yellow-600">
                                        Pengeluaran lain (0)
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

        
        <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100 mt-10 mb-5">
            Pengeluaran Lainnya
        </h2>

        
        <div class="space-y-4">
            <div
                class="relative bg-white dark:bg-slate-800 shadow sm:rounded-lg"
            >
                <!-- <div class="flex justify-between p-2">
                    <TextInput
                        v-model="data.params.search"
                        type="text"
                        class="ml-3 block w-full rounded-lg"
                        :placeholder="lang().placeholder.search"
                    />
                </div> -->
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead
                            class="uppercase text-sm border-t border-slate-200 dark:border-slate-700"
                        >
                            <tr class="dark:bg-slate-900/50 text-left">
                                <th class="px-2 py-4 text-center">#</th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Amount</span>
                                        <!-- <ChevronUpDownIcon class="w-4 h-4" /> -->
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Deskripsi</span>
                                        <!-- <ChevronUpDownIcon class="w-4 h-4" /> -->
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Tanggal</span>
                                        <!-- <ChevronUpDownIcon class="w-4 h-4" /> -->
                                    </div>
                                </th>
                                <th class="px-2 py-4 sr-only">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(expense, index) in expenses.data"
                                :key="index"
                                class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20"
                            >
                                <td
                                    class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"
                                >
                                    {{ ++index }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    Rp{{ parseInt(expense.amount).toLocaleString() }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <p style="white-space: normal; word-break: break-all; display: block;">{{ expense.description ?? '-' }}</p>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ expense.date }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100 mt-10 mb-5">
            Pendapatan Lainnya
        </h2>

        
        <div class="space-y-4">
            <div
                class="relative bg-white dark:bg-slate-800 shadow sm:rounded-lg"
            >
                <!-- <div class="flex justify-between p-2">
                    <TextInput
                        v-model="data.params.search"
                        type="text"
                        class="ml-3 block w-full rounded-lg"
                        :placeholder="lang().placeholder.search"
                    />
                </div> -->
                <div class="overflow-x-auto scrollbar-table">
                    <table class="w-full">
                        <thead
                            class="uppercase text-sm border-t border-slate-200 dark:border-slate-700"
                        >
                            <tr class="dark:bg-slate-900/50 text-left">
                                <th class="px-2 py-4 text-center">#</th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Amount</span>
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Deskripsi</span>
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Tanggal</span>
                                    </div>
                                </th>
                                <th class="px-2 py-4 sr-only">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(otherIncome, index) in otherIncomes.data"
                                :key="index"
                                class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20"
                            >
                                <td
                                    class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"
                                >
                                    {{ ++index }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    Rp{{ parseInt(otherIncome.amount).toLocaleString() }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <p style="white-space: normal; word-break: break-all; display: block;">{{ otherIncome.description ?? '-' }}</p>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ otherIncome.date }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
