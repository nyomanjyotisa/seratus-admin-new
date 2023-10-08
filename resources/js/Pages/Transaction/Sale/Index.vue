<script setup>
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
import Create from "@/Pages/Transaction/Sale/Create.vue";
import Edit from "@/Pages/Transaction/Sale/Edit.vue";
import Delete from "@/Pages/Transaction/Sale/Delete.vue";
import DeleteBulk from "@/Pages/Transaction/Sale/DeleteBulk.vue";

const { _, debounce, pickBy } = pkg;
const props = defineProps({
    title: String,
    sales: Object,
    transaction: Object,
});
const data = reactive({
    selectedId: [],
    multipleSelect: false,
    createOpen: false,
    editOpen: false,
    deleteOpen: false,
    deleteBulkOpen: false,
    sale: null,
});

</script>

<template>
    <header class="mb-4">
        <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
            Transaction Sales Data
        </h2>
    </header>
        <div class="space-y-4">
            <div class="px-4 sm:px-0">
                <div class="rounded-lg overflow-hidden w-fit">
                    <PrimaryButton
                        class="rounded-none"
                        @click="data.createOpen = true"
                    >
                        Add Sale Data
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
                        :sale="data.sale"
                        :title="props.title"
                    />
                    <Edit
                        :show="data.editOpen"
                        @close="data.editOpen = false"
                        :sale="data.sale"
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
                                        <span>Payment Type</span>
                                    </div>
                                </th>
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
                                        <span>Description</span>
                                    </div>
                                </th>
                                <th
                                    class="px-2 py-4 cursor-pointer"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span>Date</span>
                                    </div>
                                </th>
                                <th class="px-2 py-4 sr-only">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(sale, index) in sales"
                                :key="index"
                                class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20"
                            >
                                <td
                                    class="whitespace-nowrap py-4 px-2 sm:py-3 text-center"
                                >
                                    {{ ++index }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ sale.payment_type ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ sale.amount }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ sale.description }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    {{ sale.date }}
                                </td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">
                                    <div
                                        class="flex justify-center items-center"
                                    >
                                        <div class="rounded-md overflow-hidden">
                                            <InfoButton
                                                type="button"
                                                @click="
                                                    (data.editOpen = true),
                                                        (data.sale = sale)
                                                "
                                                class="px-2 py-1.5 rounded-none"
                                                v-tooltip="lang().tooltip.edit"
                                            >
                                                <PencilIcon class="w-4 h-4" />
                                            </InfoButton>
                                            <DangerButton
                                                type="button"
                                                @click="
                                                    (data.deleteOpen = true),
                                                        (data.sale = sale)
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
