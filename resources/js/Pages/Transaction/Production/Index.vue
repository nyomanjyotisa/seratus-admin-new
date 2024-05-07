<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InfoButton from "@/Components/InfoButton.vue";
import { reactive } from "vue";
import DangerButton from "@/Components/DangerButton.vue";
import pkg from "lodash";
import {
    EyeIcon,
    PencilIcon,
    TrashIcon,
} from "@heroicons/vue/24/solid";
import Create from "@/Pages/Transaction/Production/Create.vue";
import Edit from "@/Pages/Transaction/Production/Edit.vue";
import Delete from "@/Pages/Transaction/Production/Delete.vue";
import DeleteBulk from "@/Pages/Transaction/Production/DeleteBulk.vue";
import Checkbox from "@/Components/Checkbox.vue";

const { _, debounce, pickBy } = pkg;
const props = defineProps({
    title: String,
    productions: Object,
    transaction: Object,
});
const data = reactive({
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    production: null,
});

</script>

<template>
    <header class="mb-4">
        <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
            Data Transaksi Produksi
        </h2>
    </header>
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton
                        v-if="transaction.status == 'pending'"
                        class="rounded-none"
                        @click="data.createOpen = true"
                    >
                        Tambah Transaksi Produksi
                    </PrimaryButton>
                    <Create
                        :show="data.createOpen"
                        @close="data.createOpen = false"
                        :title="props.title"
                        :transaction="props.transaction"
                    />
                    <Delete
                        :show="data.deleteOpen"
                        @close="data.deleteOpen = false"
                        :production="data.production"
                        :title="props.title"
                    />
                    <Edit
                        :show="data.editOpen"
                        @close="data.editOpen = false"
                        :production="data.production"
                        :title="props.title"
                        :transaction="props.transaction"
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
                </div>
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
                                v-for="(production, index) in productions"
                                :key="index"
                                class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20"
                            >
                                <td
                                    class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"
                                >
                                    {{ ++index }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    Rp{{ production.amount.toLocaleString() }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <p style="white-space: normal; word-break: break-all; display: block;">{{ production.description ?? '-' }}</p>
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ production.date }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div
                                        class="flex justify-center items-center"
                                    >
                                        <div class="rounded-md overflow-hidden">
                                            <InfoButton
                                                v-if="transaction.status == 'pending'"
                                                type="button"
                                                @click="
                                                    (data.editOpen = true),
                                                        (data.production = production)
                                                "
                                                class="px-2 py-1.5 rounded-none"
                                                v-tooltip="lang().tooltip.edit"
                                            >
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
                                            <DangerButton
                                                v-if="transaction.status == 'pending'"
                                                type="button"
                                                @click="
                                                    (data.deleteOpen = true),
                                                        (data.production = production)
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
            </div>
        </div>
</template>
