<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import UpdateTransactionForm from "./Partials/UpdateTransactionForm.vue";
import SaleIndex from "./Sale/Index.vue";
import ProductionIndex from "./Production/Index.vue";
import ExpenseIndex from "./Expense/Index.vue";
import OtherIncomeIndex from "./OtherIncome/Index.vue";
import { Head } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";

defineProps({
    transaction: Object,
    sales: Object,
    productions: Object,
    expenses: Object,
    otherIncomes: Object,
});
</script>

<template>
    <Head title="Transaction" />

    <AuthenticatedLayout>
        <Breadcrumb :title="'Transaction'" :breadcrumbs="[]" />

        <div class="space-y-4">
            
            <div
                class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg"
            >
                <UpdateTransactionForm
                    :transaction="transaction"
                />
            </div>

            <div
                class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg"
            >
                <SaleIndex
                    title="Sale"
                    :sales="sales"
                    :transaction="transaction"
                    class="max-w-xl"
                />
            </div>

            <div
                class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg"
            >
                <ProductionIndex
                    title="Production"
                    :productions="productions"
                    :transaction="transaction"
                    class="max-w-xl"
                />
            </div>

            <div
                class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg"
            >
                <ExpenseIndex
                    title="Expense"
                    :expenses="expenses"
                    :transaction="transaction"
                    class="max-w-xl"
                />
            </div>

            <div
                class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg"
            >
                <OtherIncomeIndex
                    title="Pendapatan Lainnya"
                    :otherIncomes="otherIncomes"
                    :transaction="transaction"
                    class="max-w-xl"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
