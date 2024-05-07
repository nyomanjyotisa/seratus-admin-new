<script setup>
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    transaksiPending: Number,
    transaksiSelesai: Number,
    total_pemasukan: Number,
    total_pengeluaran: Number,
    laba: Number,
    saldo: Number,
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <Breadcrumb :title="'Dashboard'" :breadcrumbs="[]" />
        <div class="space-y-4">
            <div
                class="text-white dark:text-slate-100 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-2 sm:gap-4 overflow-hidden shadow-sm"
            >
                <div>
                    <div
                        class="rounded-none sm:rounded-lg px-4 py-6 flex justify-between bg-amber-600/70 dark:bg-amber-500/80 items-center overflow-hidden"
                    >
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">{{ props.transaksiPending }}</p>
                            <p class="text-md md:text-lg uppercase">
                                Transaksi Pending
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <div
                        class="rounded-none sm:rounded-lg px-4 py-6 flex justify-between bg-green-600/70 dark:bg-green-500/80 items-center overflow-hidden"
                    >
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">{{ props.transaksiSelesai }}</p>
                            <p class="text-md md:text-lg uppercase">
                                Total Transaksi Selesai
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <div
                        class="rounded-none sm:rounded-lg px-4 py-6 flex justify-between bg-blue-600/70 dark:bg-blue-500/80 items-center overflow-hidden"
                    >
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">Rp{{ props.saldo.toLocaleString() }}</p>
                            <p class="text-md md:text-lg uppercase">
                                Saldo Kas Saat Ini
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <div
                        class="rounded-none sm:rounded-lg px-4 py-6 flex justify-between bg-blue-600/70 dark:bg-blue-500/80 items-center overflow-hidden"
                    >
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">Rp{{ props.total_pemasukan.toLocaleString() }}</p>
                            <p class="text-md md:text-lg uppercase">
                                Total Omzet
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <div
                        class="rounded-none sm:rounded-lg px-4 py-6 flex justify-between bg-amber-600/70 dark:bg-amber-500/80 items-center overflow-hidden"
                    >
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">Rp{{ props.total_pengeluaran.toLocaleString() }}</p>
                            <p class="text-md md:text-lg uppercase">
                                Total Pengeluaran Operasional
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <div
                        class="rounded-none sm:rounded-lg px-4 py-6 flex justify-between bg-green-600/70 dark:bg-green-500/80 items-center overflow-hidden"
                    >
                        <div class="flex flex-col">
                            <p class="text-4xl font-bold">Rp{{ props.laba.toLocaleString() }}</p>
                            <p class="text-md md:text-lg uppercase">
                                Total Laba
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
